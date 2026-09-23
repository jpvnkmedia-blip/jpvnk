<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseApplication;
use App\Models\User;
use App\Models\Pemunya;
use App\Models\Ternakan;
use App\Models\EpuLadang;
use App\Models\PawahPerjanjian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Closure;
use Carbon\Carbon;

class KursusController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            function (Request $request, Closure $next) {
                if (Auth::check()) {
                    $user = Auth::user();
                    if (in_array($user->role, ['admin_program', 'admin_eptr'])) {
                        abort(403, 'Akses Ditolak: Peranan ' . ($user->role_label ?? $user->role) . ' tidak dibenarkan mengakses modul Kursus Ternakan.');
                    }
                }
                return $next($request);
            }
        ];
    }

    // 1. Direktori Kursus & Bengkel
    public function index(Request $request)
    {
        $query = Course::withCount('applications');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('trainer_name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('jajahan', 'like', "%{$search}%");
            });
        }

        $courses = $query->latest('start_date')->paginate(9);
        $myApplications = Auth::check() ? CourseApplication::where('user_id', Auth::id())->pluck('course_id')->toArray() : [];
        $myApplicationsList = Auth::check() ? CourseApplication::where('user_id', Auth::id())->with('course')->get()->keyBy('course_id') : collect();
        
        $pendingApplicationsCount = CourseApplication::where('status', 'Menunggu')->count();

        return view('kursus.index', compact('courses', 'myApplications', 'myApplicationsList', 'pendingApplicationsCount'));
    }

    // 2. Borang Terbit Kursus Baharu
    public function create()
    {
        if (!Auth::user()->canPublishCourse()) {
            abort(403, 'Akses Ditolak: Hanya Admin Kursus dan Super Admin dibenarkan menerbitkan kursus baharu.');
        }

        return view('kursus.create');
    }

    // 3. Simpan Kursus Baharu
    public function store(Request $request)
    {
        if (!Auth::user()->canPublishCourse()) {
            abort(403, 'Akses Ditolak: Hanya Admin Kursus dan Super Admin dibenarkan menerbitkan kursus baharu.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'description' => 'required|string',
            'trainer_name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'time' => 'required|string',
            'location' => 'required|string|max:255',
            'jajahan' => 'required|string',
            'capacity' => 'required|integer|min:5',
            'fee' => 'nullable|numeric|min:0',
        ]);

        $code = 'KURSUS-' . strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $validated['category']), 0, 3)) . '-' . date('Y') . '-' . rand(10, 99);

        $course = Course::create([
            'title' => $validated['title'],
            'code' => $code,
            'category' => $validated['category'],
            'description' => $validated['description'],
            'trainer_name' => $validated['trainer_name'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'time' => $validated['time'],
            'location' => $validated['location'],
            'jajahan' => $validated['jajahan'],
            'capacity' => $validated['capacity'],
            'registered_count' => 0,
            'fee' => $validated['fee'] ?? 0,
            'status' => 'Buka',
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('kursus.index')->with('success', "Kursus '{$course->title}' ({$course->code}) berjaya diterbitkan.");
    }

    // 4. Paparan Butiran Kursus & Senarai Peserta
    public function show($id)
    {
        $course = Course::with(['applications.user', 'creator'])->findOrFail($id);
        $isApplied = Auth::check() ? CourseApplication::where('course_id', $id)->where('user_id', Auth::id())->first() : null;

        return view('kursus.show', compact('course', 'isApplied'));
    }

    // 5. Borang Kemaskini Kursus
    public function edit($id)
    {
        if (!Auth::user()->canPublishCourse()) {
            abort(403, 'Akses Ditolak: Hanya Admin Kursus dan Super Admin dibenarkan mengemaskini maklumat kursus.');
        }

        $course = Course::findOrFail($id);
        return view('kursus.edit', compact('course'));
    }

    // 6. Simpan Kemaskini Kursus
    public function update(Request $request, $id)
    {
        if (!Auth::user()->canPublishCourse()) {
            abort(403, 'Akses Ditolak: Hanya Admin Kursus dan Super Admin dibenarkan mengemaskini maklumat kursus.');
        }

        $course = Course::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'description' => 'required|string',
            'trainer_name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'time' => 'required|string',
            'location' => 'required|string|max:255',
            'jajahan' => 'required|string',
            'capacity' => 'required|integer|min:5',
            'fee' => 'nullable|numeric|min:0',
            'status' => 'required|string|in:Buka,Tutup,Sedang Berlangsung,Selesai,Diarkibkan,Batal',
        ]);

        $course->update([
            'title' => $validated['title'],
            'category' => $validated['category'],
            'description' => $validated['description'],
            'trainer_name' => $validated['trainer_name'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'time' => $validated['time'],
            'location' => $validated['location'],
            'jajahan' => $validated['jajahan'],
            'capacity' => $validated['capacity'],
            'fee' => $validated['fee'] ?? 0,
            'status' => $validated['status'],
        ]);

        return redirect()->route('kursus.show', $course->id)->with('success', "Maklumat kursus '{$course->title}' berjaya dikemaskini.");
    }

    // 7. Kemaskini Status Kursus Pantas
    public function updateStatus(Request $request, $id)
    {
        if (!Auth::user()->canPublishCourse()) {
            abort(403, 'Akses Ditolak: Hanya Admin Kursus dan Super Admin dibenarkan menukar status kursus.');
        }

        $course = Course::findOrFail($id);
        $request->validate([
            'status' => 'required|string|in:Buka,Tutup,Sedang Berlangsung,Selesai,Diarkibkan,Batal',
        ]);

        $course->update(['status' => $request->status]);

        return back()->with('success', "Status kursus ditukar kepada '{$request->status}'.");
    }

    // 8a. Arkibkan Kursus (Melindungi Rekod Pemohon & Sijil Digital)
    public function arkib($id)
    {
        if (!Auth::user()->canPublishCourse()) {
            abort(403, 'Akses Ditolak: Hanya Admin Kursus dan Super Admin dibenarkan mengarkibkan kursus.');
        }

        $course = Course::findOrFail($id);
        $course->update(['status' => 'Diarkibkan']);

        return redirect()->route('kursus.index')->with('success', "Kursus '{$course->title}' ({$course->code}) telah berjaya DIARKIBKAN. Maklumat kursus, rekod kehadiran, dan sijil digital pemohon kekal terpelihara sepenuhnya.");
    }

    // 8b. Padam Kursus (Dengan Perlindungan Integriti Sijil & Pemohon)
    public function destroy($id)
    {
        if (!Auth::user()->canPublishCourse()) {
            abort(403, 'Akses Ditolak: Hanya Admin Kursus dan Super Admin dibenarkan memadam kursus.');
        }

        $course = Course::with('applications')->findOrFail($id);
        $title = $course->title;
        $code = $course->code;

        // Sekiranya kursus mempunyai peserta berdaftar atau telah selesai/diarkibkan, jangan padam - tukar kepada Diarkibkan
        if ($course->applications()->count() > 0 || in_array($course->status, ['Selesai', 'Diarkibkan'])) {
            $course->update(['status' => 'Diarkibkan']);
            return redirect()->route('kursus.index')->with('success', "Kursus '{$title}' ({$code}) mempunyai rekod penyertaan pemohon. Kursus telah DIARKIBKAN secara automatik bagi memelihara maklumat kursus, rekod pemohon dan sijil digital.");
        }

        $course->delete();

        return redirect()->route('kursus.index')->with('success', "Kursus '{$title}' berjaya dipadam daripada sistem.");
    }

    // 9. Pendaftaran Kursus oleh Peserta (Penternak / Usahawan / Awam)
    public function apply(Request $request, $id)
    {
        $course = Course::findOrFail($id);
        $user = Auth::user();

        // Semak status kursus
        if ($course->status !== 'Buka') {
            return back()->with('error', "Pendaftaran ditutup. Status kursus semasa ialah '{$course->status}'.");
        }

        // Semak jika sudah memohon
        $existing = CourseApplication::where('course_id', $id)->where('user_id', $user->id)->first();
        if ($existing) {
            return back()->with('error', 'Anda telah mendaftar bagi kursus ini sebelum ini.');
        }

        $regNo = 'REG-' . date('Y') . '-' . rand(1000, 9999);

        CourseApplication::create([
            'course_id' => $course->id,
            'user_id' => $user->id,
            'registration_number' => $regNo,
            'status' => 'Menunggu', // Menunggu kelulusan Admin Kursus
        ]);

        $course->increment('registered_count');

        \App\Models\UserNotification::send(
            $user->id,
            'Permohonan Kursus Dihantar',
            "Permohonan pendaftaran anda untuk '{$course->title}' ({$regNo}) telah dihantar dan sedang disemak.",
            'kursus',
            route('kursus.show', $course->id),
            'fa-solid fa-graduation-cap',
            'cyan'
        );

        return back()->with('success', 'Permohonan pendaftaran kursus berjaya dihantar! Sila tunggu kelulusan daripada Pegawai Kursus.');
    }

    // 10. Hab Pengurusan Pemohon & Peserta Kursus (Admin Kursus View)
    public function pengurusanPemohon(Request $request)
    {
        if (!Auth::user()->canPublishCourse()) {
            abort(403, 'Akses Ditolak: Hanya Admin Kursus dan Super Admin dibenarkan menguruskan pemohon.');
        }

        $query = CourseApplication::with(['course', 'user']);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('registration_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($qu) use ($search) {
                      $qu->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere('ic_number', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  })
                  ->orWhereHas('course', function ($qc) use ($search) {
                      $qc->where('title', 'like', "%{$search}%")
                         ->orWhere('code', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        $totalApplications = CourseApplication::count();
        $totalPending = CourseApplication::where('status', 'Menunggu')->count();
        $totalApproved = CourseApplication::where('status', 'Disahkan')->count();
        $totalAttended = CourseApplication::where('status', 'Hadir')->count();
        $totalRejected = CourseApplication::where('status', 'Ditolak')->count();

        $applications = $query->latest()->paginate(15)->withQueryString();
        $coursesList = Course::orderBy('title')->get();

        return view('kursus.pemohon', compact(
            'applications',
            'coursesList',
            'totalApplications',
            'totalPending',
            'totalApproved',
            'totalAttended',
            'totalRejected'
        ));
    }

    // 10b. Maklumat Penuh Pemohon Kursus & Rekod Ternakan
    public function showPemohon($id)
    {
        if (!Auth::user()->canPublishCourse()) {
            abort(403, 'Akses Ditolak: Hanya Admin Kursus dan Super Admin dibenarkan melihat maklumat pemohon.');
        }

        $application = CourseApplication::with(['course', 'user.permohonanKursus.course'])->findOrFail($id);
        $user = $application->user;

        $pemunya = null;
        $ternakanList = collect();
        $pawahList = collect();
        $ladangUnggasList = collect();

        if ($user) {
            $pemunya = $user->pemunya ?: Pemunya::where('no_kp', $user->ic_number)->with('ternakan')->first();
            if ($pemunya) {
                $ternakanList = $pemunya->ternakan()->latest()->get();
            }
            $pawahList = $user->pawahPerjanjian()->with('ternakanList')->latest()->get();
            $ladangUnggasList = $user->ladangUnggas()->latest()->get();
        }

        return view('kursus.pemohon_show', compact(
            'application',
            'pemunya',
            'ternakanList',
            'pawahList',
            'ladangUnggasList'
        ));
    }

    // 11. Kelulusan Permohonan Peserta
    public function luluskanPemohon(Request $request, $id)
    {
        if (!Auth::user()->canPublishCourse()) {
            abort(403, 'Akses Ditolak: Hanya Admin Kursus dan Super Admin dibenarkan meluluskan permohonan.');
        }

        $application = CourseApplication::with('course', 'user')->findOrFail($id);
        $application->update([
            'status' => 'Disahkan',
            'rejection_reason' => null,
        ]);

        if ($application->user_id) {
            \App\Models\UserNotification::send(
                $application->user_id,
                'Permohonan Kursus Diluluskan',
                "Permohonan anda bagi kursus '{$application->course->title}' telah DILULUSKAN! Sila hadir mengikut jadual tarikh dan masa yang ditetapkan.",
                'kursus',
                route('kursus.show', $application->course_id),
                'fa-solid fa-circle-check',
                'emerald'
            );
        }

        return back()->with('success', "Permohonan pendaftaran bagi '{$application->user->name}' ({$application->registration_number}) telah DILULUSKAN.");
    }

    // 12. Penolakan Permohonan Peserta
    public function tolakPemohon(Request $request, $id)
    {
        if (!Auth::user()->canPublishCourse()) {
            abort(403, 'Akses Ditolak: Hanya Admin Kursus dan Super Admin dibenarkan menolak permohonan.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $application = CourseApplication::with('course', 'user')->findOrFail($id);
        $application->update([
            'status' => 'Ditolak',
            'rejection_reason' => $request->rejection_reason,
        ]);

        if ($application->course && $application->course->registered_count > 0) {
            $application->course->decrement('registered_count');
        }

        if ($application->user_id) {
            \App\Models\UserNotification::send(
                $application->user_id,
                'Permohonan Kursus Ditolak',
                "Permohonan pendaftaran anda bagi kursus '{$application->course->title}' tidak diluluskan. Sebab: {$request->rejection_reason}.",
                'kursus',
                route('kursus.show', $application->course_id),
                'fa-solid fa-circle-xmark',
                'rose'
            );
        }

        return back()->with('success', "Permohonan bagi '{$application->user->name}' telah DITOLAK. Sebab penolakan telah direkodkan.");
    }

    // 13. Pengesahan Kehadiran & Pengeluaran Sijil Digital
    public function sahkanKehadiran(Request $request, $id)
    {
        if (!Auth::user()->canPublishCourse()) {
            abort(403, 'Akses Ditolak: Hanya Admin Kursus dan Super Admin dibenarkan mengesahkan kehadiran dan mengeluarkan sijil.');
        }

        $application = CourseApplication::with('course', 'user')->findOrFail($id);

        $certNo = $application->certificate_number ?: ('SIJIL-JPVNK-' . date('Y') . '-' . rand(1000, 9999));
        $certDate = $application->certificate_issued_at ?: Carbon::now()->toDateString();

        $application->update([
            'status' => 'Hadir',
            'certificate_number' => $certNo,
            'certificate_issued_at' => $certDate,
        ]);

        if ($application->user_id) {
            \App\Models\UserNotification::send(
                $application->user_id,
                'Sijil Digital Kursus Dikeluarkan',
                "Tahniah! Sijil Digital rasmi bagi kursus '{$application->course->title}' ({$certNo}) sedia untuk dimuat turun.",
                'kursus',
                route('kursus.show', $application->course_id),
                'fa-solid fa-award',
                'amber'
            );
        }

        return back()->with('success', "Kehadiran '{$application->user->name}' disahkan. Sijil Digital rasmi ({$certNo}) berjaya dijana.");
    }

    // 14. Kelulusan Pukal Permohonan
    public function lulusPukal(Request $request)
    {
        if (!Auth::user()->canPublishCourse()) {
            abort(403, 'Akses Ditolak: Hanya Admin Kursus dan Super Admin dibenarkan memproses kelulusan pukal.');
        }

        $request->validate([
            'application_ids' => 'required|array',
            'application_ids.*' => 'exists:course_applications,id',
        ]);

        CourseApplication::whereIn('id', $request->application_ids)->update([
            'status' => 'Disahkan',
            'rejection_reason' => null,
        ]);

        $count = count($request->application_ids);
        return back()->with('success', "Sebanyak {$count} permohonan peserta berjaya DILULUSKAN serentak.");
    }

    // 15. Cetak Sijil Digital Kursus (PDF View)
    public function cetakSijil($applicationId)
    {
        $application = CourseApplication::with('course', 'user')->findOrFail($applicationId);

        // Kawalan keselamatan: Hanya permohonan yang berstatus Disahkan / Hadir / Selesai dengan No Sijil dibenarkan cetak
        if (!in_array($application->status, ['Disahkan', 'Hadir', 'Selesai']) || !$application->certificate_number) {
            return back()->with('error', 'Sijil digital belum sedia untuk dicetak. Sila pastikan kehadiran telah disahkan oleh Pegawai Latihan.');
        }

        return view('kursus.sijil', compact('application'));
    }

    // 16. Padam Permohonan Peserta Kursus (Super Admin Sahaja)
    public function destroyPemohon($id)
    {
        $user = Auth::user();
        if (!$user || !$user->isSuperAdmin()) {
            abort(403, 'Akses Ditolak: Hanya Super Admin dibenarkan memadam permohonan kursus.');
        }

        $application = CourseApplication::findOrFail($id);
        $name = $application->user->name ?? 'Pemohon';
        $regNo = $application->registration_number;

        if ($application->course && $application->course->registered_count > 0) {
            $application->course->decrement('registered_count');
        }

        $application->delete();

        return back()->with('success', "Permohonan kursus bagi '{$name}' ({$regNo}) berjaya dipadam daripada sistem.");
    }
}
