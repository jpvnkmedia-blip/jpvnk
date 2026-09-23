<?php

namespace App\Http\Controllers;

use App\Models\MediaTempahan;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class MediaTempahanController extends Controller
{
    public function __construct()
    {
        // Akses dikhaskan untuk kakitangan jabatan (staf) dan admin
    }

    /**
     * Semak kebenaran akses modul
     */
    private function authorizeAccess()
    {
        $user = Auth::user();
        if (!$user || !$user->isStaff()) {
            abort(403, 'Akses Ditolak: Modul Tempahan Unit Media dikhaskan untuk kakitangan jabatan (staf JPVNK) sahaja.');
        }
    }

    /**
     * Laman Utama: Kalendar Ketersediaan Slot & Senarai Tempahan
     */
    public function index(Request $request)
    {
        $this->authorizeAccess();
        $user = Auth::user();

        // 1. Parameter Kalendar (Bulan & Tahun)
        $month = (int) $request->input('month', Carbon::now()->month);
        $year = (int) $request->input('year', Carbon::now()->year);

        if ($month < 1 || $month > 12) {
            $month = Carbon::now()->month;
        }
        if ($year < 2020 || $year > 2035) {
            $year = Carbon::now()->year;
        }

        $currentDate = Carbon::createFromDate($year, $month, 1);
        $daysInMonth = $currentDate->daysInMonth;
        $startDayOfWeek = ($currentDate->copy()->startOfMonth()->dayOfWeekIso) % 7; // 0 for Sunday or adjusted

        // Dapatkan semua tempahan aktif dalam bulan ini untuk kalendar
        $startDate = $currentDate->copy()->startOfMonth()->toDateString();
        $endDate = $currentDate->copy()->endOfMonth()->toDateString();

        $monthBookings = MediaTempahan::whereBetween('tarikh_program', [$startDate, $endDate])
            ->whereIn('status', ['Menunggu Kelulusan', 'Diluluskan', 'Perlu Pembetulan', 'Selesai'])
            ->get();

        // Struktur data harian kalendar
        $calendarDays = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $d);
            $dayBookings = $monthBookings->filter(function ($b) use ($dateStr) {
                return $b->tarikh_program->format('Y-m-d') === $dateStr;
            });

            $count = $dayBookings->count();
            if ($count === 0) {
                $status = 'kosong';
                $color = 'emerald';
                $badge = 'Kosong';
            } elseif ($count <= 2) {
                $status = 'sebahagian';
                $color = 'amber';
                $badge = "$count Slot";
            } else {
                $status = 'penuh';
                $color = 'rose';
                $badge = "Penuh ($count)";
            }

            $calendarDays[$d] = [
                'day' => $d,
                'date' => $dateStr,
                'status' => $status,
                'color' => $color,
                'badge' => $badge,
                'count' => $count,
                'is_full' => $count >= 3,
                'bookings' => $dayBookings->values(),
            ];
        }

        // 2. Statistik Ringkas
        $statTotal = MediaTempahan::count();
        $statPending = MediaTempahan::where('status', 'Menunggu Kelulusan')->count();
        $statApproved = MediaTempahan::where('status', 'Diluluskan')->count();
        $statNeedsCorrection = MediaTempahan::where('status', 'Perlu Pembetulan')->count();
        $statCompleted = MediaTempahan::where('status', 'Selesai')->count();

        // 3. Senarai Tempahan (Jadual)
        $query = MediaTempahan::with('pemohon', 'pelulus')->latest();

        // Jika bukan admin media / super admin / pengarah, paparkan permohonan sendiri atau permohonan bahagian
        if (!$user->canManageMedia() && !$user->isPengarah()) {
            if ($request->input('view') !== 'semua') {
                $query->where('user_id', $user->id);
            }
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('carian')) {
            $c = $request->carian;
            $query->where(function ($q) use ($c) {
                $q->where('no_rujukan', 'like', "%$c%")
                    ->orWhere('nama_program', 'like', "%$c%")
                    ->orWhere('nama_pemohon', 'like', "%$c%")
                    ->orWhere('bahagian_unit_jajahan', 'like', "%$c%");
            });
        }

        if ($request->filled('tarikh_pilih')) {
            $query->whereDate('tarikh_program', $request->tarikh_pilih);
        }

        $tempahanList = $query->paginate(15)->withQueryString();

        $activeTab = $request->input('tab', 'kalendar');

        return view('media.index', compact(
            'month',
            'year',
            'currentDate',
            'daysInMonth',
            'startDayOfWeek',
            'calendarDays',
            'statTotal',
            'statPending',
            'statApproved',
            'statNeedsCorrection',
            'statCompleted',
            'tempahanList',
            'activeTab'
        ));
    }

    /**
     * Borang Permohonan Tempahan Baharu
     */
    public function create(Request $request)
    {
        $this->authorizeAccess();
        $user = Auth::user();

        // Pra-isi tarikh jika dipilih dari kalendar (pastikan bukan tarikh lepas)
        $tarikhPilihan = $request->input('tarikh');
        if (!$tarikhPilihan || $tarikhPilihan < date('Y-m-d')) {
            $tarikhPilihan = Carbon::tomorrow()->format('Y-m-d');
        }

        return view('media.create', compact('user', 'tarikhPilihan'));
    }

    /**
     * Simpan Permohonan Tempahan Media Baharu
     */
    public function store(Request $request)
    {
        $this->authorizeAccess();
        $user = Auth::user();

        $validated = $request->validate([
            // 1. Maklumat Pemohon
            'nama_pemohon' => 'required|string|max:255',
            'jawatan' => 'nullable|string|max:255',
            'jawatan_pemohon' => 'nullable|string|max:255',
            'bahagian_unit_jajahan' => 'nullable|string|max:255',
            'bahagian_unit' => 'nullable|string|max:255',
            'no_telefon' => 'required|string|max:50',
            'emel' => 'nullable|email|max:255',

            // 2. Maklumat Program
            'nama_program' => 'required|string|max:255',
            'tarikh_program' => 'required|date|after_or_equal:today',
            'tarikh_tamat' => 'nullable|date|after_or_equal:tarikh_program',
            'masa_mula' => 'required|string|max:20',
            'masa_tamat' => 'required|string|max:20',
            'lokasi' => 'required|string|max:255',
            'penganjur' => 'required|string|max:255',
            'pegawai_bertanggungjawab' => 'required|string|max:255',
            'anggaran_peserta' => 'nullable|integer|min:1',

            // 3. Jenis Permohonan (Checkbox Array)
            'jenis_permohonan' => 'required|array|min:1',
            'jenis_permohonan.*' => 'string|max:100',

            // 4. Butiran Keperluan Khusus
            'butiran_fotografi' => 'nullable',
            'keperluan_fotografi' => 'nullable',
            'butiran_poster' => 'nullable',
            'keperluan_poster' => 'nullable',
            'butiran_video' => 'nullable',
            'keperluan_video' => 'nullable',
            'butiran_lain' => 'nullable|string',
            'catatan_keperluan' => 'nullable|string',

            // 5. Keutamaan & Tarikh Diperlukan
            'tarikh_diperlukan' => 'nullable|date',
            'tahap_keutamaan' => 'nullable|in:Biasa,Segera,Sangat Segera',
            'keutamaan' => 'nullable|in:Biasa,Segera,Sangat Segera',
            'sebab_segera' => 'nullable|string',

            // 6. Lampiran Fail
            'lampiran_files.*' => 'nullable|file|max:10240',
            'lampiran.*' => 'nullable|file|max:10240',

            // 7. Pengesahan
            'pengesahan_pemohon' => 'nullable',
            'perakuan' => 'nullable',
        ], [
            'jenis_permohonan.required' => 'Sila pilih sekurang-kurangnya SATU jenis permohonan media.',
            'tarikh_program.required' => 'Tarikh program diperlukan.',
        ]);

        // Proses fail lampiran
        $lampiranData = [];
        $files = $request->file('lampiran_files') ?? $request->file('lampiran') ?? [];
        if (!empty($files)) {
            foreach ($files as $file) {
                if ($file && $file->isValid()) {
                    $originalName = $file->getClientOriginalName();
                    $path = $file->store('media_lampiran', 'public');
                    $lampiranData[] = [
                        'nama_asal' => $originalName,
                        'fail' => $path,
                        'saiz' => $file->getSize(),
                        'format' => $file->getClientOriginalExtension(),
                    ];
                }
            }
        }

        $noRujukan = MediaTempahan::generateNoRujukan();

        // Butiran poster & video data compilation
        $posterData = $request->input('butiran_poster', []);
        if (empty($posterData) && $request->filled('poster_tajuk')) {
            $posterData = [
                'tajuk' => $request->input('poster_tajuk'),
                'saiz' => $request->input('poster_saiz'),
                'konsep' => $request->input('poster_konsep'),
            ];
        }

        $videoData = $request->input('butiran_video', []);
        if (empty($videoData) && ($request->filled('video_format') || $request->filled('video_durasi'))) {
            $videoData = [
                'format' => $request->input('video_format'),
                'durasi' => $request->input('video_durasi'),
            ];
        }

        $fotoData = $request->input('butiran_fotografi') ?? $request->input('keperluan_fotografi') ?? [];

        $tempahan = MediaTempahan::create([
            'no_rujukan' => $noRujukan,
            'user_id' => $user->id,
            'nama_pemohon' => $validated['nama_pemohon'],
            'jawatan' => $validated['jawatan'] ?? $validated['jawatan_pemohon'] ?? 'Pegawai Veterinar',
            'bahagian_unit_jajahan' => $validated['bahagian_unit_jajahan'] ?? $validated['bahagian_unit'] ?? 'Ibu Pejabat JPVNK',
            'no_telefon' => $validated['no_telefon'],
            'emel' => $validated['emel'] ?? $user->email,
            'nama_program' => $validated['nama_program'],
            'tarikh_program' => $validated['tarikh_program'],
            'tarikh_tamat' => $validated['tarikh_tamat'] ?? $validated['tarikh_program'],
            'masa_mula' => $validated['masa_mula'],
            'masa_tamat' => $validated['masa_tamat'],
            'lokasi' => $validated['lokasi'],
            'penganjur' => $validated['penganjur'],
            'pegawai_bertanggungjawab' => $validated['pegawai_bertanggungjawab'],
            'anggaran_peserta' => $validated['anggaran_peserta'] ?? null,
            'jenis_permohonan' => $validated['jenis_permohonan'],
            'butiran_fotografi' => $fotoData,
            'butiran_poster' => $posterData,
            'butiran_video' => $videoData,
            'butiran_lain' => $validated['butiran_lain'] ?? $validated['catatan_keperluan'] ?? null,
            'tarikh_diperlukan' => $validated['tarikh_diperlukan'] ?? null,
            'tahap_keutamaan' => $validated['tahap_keutamaan'] ?? $validated['keutamaan'] ?? 'Biasa',
            'sebab_segera' => $validated['sebab_segera'] ?? null,
            'lampiran' => $lampiranData,
            'pengesahan_pemohon' => true,
            'tarikh_hantar' => Carbon::now(),
            'status' => 'Menunggu Kelulusan',
        ]);

        // Hantar notifikasi kepada Admin Unit Media & Super Admin
        $adminsToNotify = User::where(function ($q) {
            $q->where('role', 'admin_media')
                ->orWhereJsonContains('roles', 'admin_media')
                ->orWhere('role', 'super_admin')
                ->orWhereJsonContains('roles', 'super_admin');
        })->get();

        foreach ($adminsToNotify as $admin) {
            UserNotification::send(
                $admin->id,
                'Permohonan Tempahan Unit Media Baharu',
                "Permohonan baharu ({$tempahan->no_rujukan}) untuk program '{$tempahan->nama_program}' daripada {$tempahan->nama_pemohon}.",
                'media',
                route('media.show', $tempahan->id),
                'fa-solid fa-camera',
                'indigo'
            );
        }

        return redirect()->route('media.show', $tempahan->id)
            ->with('success', "Permohonan tempahan Unit Media berjaya dihantar dengan No. Rujukan: {$tempahan->no_rujukan}.");
    }

    /**
     * Paparan Butiran Permohonan Tempahan Media
     */
    public function show($id)
    {
        $this->authorizeAccess();
        $user = Auth::user();

        $tempahan = MediaTempahan::with('pemohon', 'pelulus')->findOrFail($id);

        // Kawalan akses semakan: Pemohon, Admin Media, Super Admin, Pengarah
        if ($tempahan->user_id !== $user->id && !$user->canManageMedia() && !$user->isPengarah()) {
            abort(403, 'Akses Ditolak: Anda tidak dibenarkan melihat butiran tempahan media pemohon lain.');
        }

        return view('media.show', compact('tempahan', 'user'));
    }

    /**
     * Borang Kemas Kini Permohonan (Sekiranya Perlu Pembetulan)
     */
    public function edit($id)
    {
        $this->authorizeAccess();
        $user = Auth::user();

        $tempahan = MediaTempahan::findOrFail($id);

        if ($tempahan->user_id !== $user->id && !$user->canManageMedia()) {
            abort(403, 'Akses Ditolak: Anda tidak dibenarkan mengemas kini permohonan ini.');
        }

        if (!in_array($tempahan->status, ['Perlu Pembetulan', 'Menunggu Kelulusan']) && !$user->canManageMedia()) {
            return redirect()->route('media.show', $tempahan->id)
                ->with('error', 'Permohonan ini tidak boleh dikemas kini kerana status semasa adalah: ' . $tempahan->status);
        }

        return view('media.edit', compact('tempahan', 'user'));
    }

    /**
     * Simpan Kemas Kini Permohonan
     */
    public function update(Request $request, $id)
    {
        $this->authorizeAccess();
        $user = Auth::user();

        $tempahan = MediaTempahan::findOrFail($id);

        if ($tempahan->user_id !== $user->id && !$user->canManageMedia()) {
            abort(403, 'Akses Ditolak: Anda tidak dibenarkan mengemas kini permohonan ini.');
        }

        $validated = $request->validate([
            'nama_pemohon' => 'required|string|max:255',
            'jawatan' => 'nullable|string|max:255',
            'jawatan_pemohon' => 'nullable|string|max:255',
            'bahagian_unit_jajahan' => 'nullable|string|max:255',
            'bahagian_unit' => 'nullable|string|max:255',
            'no_telefon' => 'required|string|max:50',
            'emel' => 'nullable|email|max:255',
            'nama_program' => 'required|string|max:255',
            'tarikh_program' => 'required|date',
            'tarikh_tamat' => 'nullable|date',
            'masa_mula' => 'required|string|max:20',
            'masa_tamat' => 'required|string|max:20',
            'lokasi' => 'required|string|max:255',
            'penganjur' => 'required|string|max:255',
            'pegawai_bertanggungjawab' => 'required|string|max:255',
            'anggaran_peserta' => 'nullable|integer|min:1',
            'jenis_permohonan' => 'required|array|min:1',
            'tarikh_diperlukan' => 'nullable|date',
            'tahap_keutamaan' => 'nullable|in:Biasa,Segera,Sangat Segera',
            'keutamaan' => 'nullable|in:Biasa,Segera,Sangat Segera',
            'sebab_segera' => 'nullable|string',
            'lampiran_files.*' => 'nullable|file|max:10240',
            'lampiran.*' => 'nullable|file|max:10240',
        ]);

        $lampiranData = $tempahan->lampiran ?? [];
        $files = $request->file('lampiran_files') ?? $request->file('lampiran') ?? [];
        if (!empty($files)) {
            foreach ($files as $file) {
                if ($file && $file->isValid()) {
                    $originalName = $file->getClientOriginalName();
                    $path = $file->store('media_lampiran', 'public');
                    $lampiranData[] = [
                        'nama_asal' => $originalName,
                        'fail' => $path,
                        'saiz' => $file->getSize(),
                        'format' => $file->getClientOriginalExtension(),
                    ];
                }
            }
        }

        $posterData = $request->input('butiran_poster') ?? $tempahan->butiran_poster ?? [];
        if ($request->filled('poster_tajuk')) {
            $posterData['tajuk'] = $request->input('poster_tajuk');
            $posterData['saiz'] = $request->input('poster_saiz');
            $posterData['konsep'] = $request->input('poster_konsep');
        }

        $videoData = $request->input('butiran_video') ?? $tempahan->butiran_video ?? [];
        if ($request->filled('video_format') || $request->filled('video_durasi')) {
            $videoData['format'] = $request->input('video_format');
            $videoData['durasi'] = $request->input('video_durasi');
        }

        $fotoData = $request->input('butiran_fotografi') ?? $request->input('keperluan_fotografi') ?? $tempahan->butiran_fotografi;

        $tempahan->update([
            'nama_pemohon' => $validated['nama_pemohon'],
            'jawatan' => $validated['jawatan'] ?? $validated['jawatan_pemohon'] ?? $tempahan->jawatan,
            'bahagian_unit_jajahan' => $validated['bahagian_unit_jajahan'] ?? $validated['bahagian_unit'] ?? $tempahan->bahagian_unit_jajahan,
            'no_telefon' => $validated['no_telefon'],
            'emel' => $validated['emel'] ?? $tempahan->emel,
            'nama_program' => $validated['nama_program'],
            'tarikh_program' => $validated['tarikh_program'],
            'tarikh_tamat' => $validated['tarikh_tamat'] ?? $validated['tarikh_program'],
            'masa_mula' => $validated['masa_mula'],
            'masa_tamat' => $validated['masa_tamat'],
            'lokasi' => $validated['lokasi'],
            'penganjur' => $validated['penganjur'],
            'pegawai_bertanggungjawab' => $validated['pegawai_bertanggungjawab'],
            'anggaran_peserta' => $validated['anggaran_peserta'] ?? null,
            'jenis_permohonan' => $validated['jenis_permohonan'],
            'butiran_fotografi' => $fotoData,
            'butiran_poster' => $posterData,
            'butiran_video' => $videoData,
            'butiran_lain' => $request->input('butiran_lain', $request->input('catatan_keperluan', $tempahan->butiran_lain)),
            'tarikh_diperlukan' => $validated['tarikh_diperlukan'] ?? null,
            'tahap_keutamaan' => $validated['tahap_keutamaan'] ?? $validated['keutamaan'] ?? $tempahan->tahap_keutamaan,
            'sebab_segera' => $validated['sebab_segera'] ?? null,
            'lampiran' => $lampiranData,
            'status' => 'Menunggu Kelulusan', // Reset status semula untuk semakan
        ]);

        // Notifikasi kepada Admin Media
        $adminsToNotify = User::where(function ($q) {
            $q->where('role', 'admin_media')
                ->orWhereJsonContains('roles', 'admin_media')
                ->orWhere('role', 'super_admin')
                ->orWhereJsonContains('roles', 'super_admin');
        })->get();

        foreach ($adminsToNotify as $admin) {
            UserNotification::send(
                $admin->id,
                'Pembetulan Permohonan Tempahan Media',
                "Permohonan ({$tempahan->no_rujukan}) untuk program '{$tempahan->nama_program}' telah dikemas kini oleh pemohon.",
                'media',
                route('media.show', $tempahan->id),
                'fa-solid fa-pen-to-square',
                'amber'
            );
        }

        return redirect()->route('media.show', $tempahan->id)
            ->with('success', 'Permohonan telah berjaya dikemas kini dan dihantar semula kepada Unit Media.');
    }

    /**
     * Keputusan & Tindakan Unit Media (Lulus / Perlu Pembetulan / Tolak / Selesai)
     */
    public function tindakan(Request $request, $id)
    {
        $this->authorizeAccess();
        $user = Auth::user();

        if (!$user->canManageMedia()) {
            abort(403, 'Akses Ditolak: Hanya Admin Unit Media dan Super Admin dibenarkan membuat keputusan tempahan media.');
        }

        $tempahan = MediaTempahan::findOrFail($id);

        $keputusan = $request->input('keputusan') ?? $request->input('status');
        $catatan = $request->input('catatan_unit_media') ?? $request->input('catatan_admin');
        $pegawai = $request->input('pegawai_media_bertugas') ?? $request->input('pegawai_bertugas');
        $peralatan = $request->input('peralatan_disediakan');

        if (!in_array($keputusan, ['Diluluskan', 'Perlu Pembetulan', 'Ditolak', 'Selesai', 'Dibatalkan', 'Menunggu Kelulusan'])) {
            return redirect()->back()->with('error', 'Pilihan keputusan status tidak sah.');
        }

        $tempahan->status = $keputusan;
        if ($catatan !== null) {
            $tempahan->catatan_unit_media = $catatan;
        }

        if ($pegawai !== null) {
            $tempahan->pegawai_media_bertugas = $pegawai;
        }

        if ($peralatan !== null) {
            $tempahan->peralatan_disediakan = $peralatan;
        }

        if (in_array($keputusan, ['Diluluskan', 'Ditolak', 'Selesai'])) {
            $tempahan->diluluskan_oleh = $user->id;
            $tempahan->tarikh_kelulusan = Carbon::now();
        }

        $tempahan->save();

        // Notifikasi kepada Pemohon
        $statusLabels = [
            'Diluluskan' => 'telah DILULUSKAN dan disahkan',
            'Perlu Pembetulan' => 'memerlukan PEMBETULAN maklumat',
            'Ditolak' => 'telah DITOLAK',
            'Selesai' => 'telah ditandakan sebagai SELESAI',
            'Dibatalkan' => 'telah DIBATALKAN',
        ];

        $actionText = $statusLabels[$keputusan] ?? 'telah dikemas kini statusnya';

        UserNotification::send(
            $tempahan->user_id,
            'Status Tempahan Unit Media: ' . $keputusan,
            "Permohonan anda ({$tempahan->no_rujukan}) untuk program '{$tempahan->nama_program}' {$actionText} oleh Unit Media.",
            'media',
            route('media.show', $tempahan->id),
            $keputusan === 'Diluluskan' ? 'fa-solid fa-circle-check' : 'fa-solid fa-bell',
            $keputusan === 'Diluluskan' ? 'emerald' : ($keputusan === 'Ditolak' ? 'rose' : 'sky')
        );

        return redirect()->route('media.show', $tempahan->id)
            ->with('success', "Keputusan tempahan berjaya dikemas kini kepada status: {$tempahan->status}.");
    }

    /**
     * Cetak Slip / Pengesahan Rasmi Tempahan Unit Media (PDF / Print)
     */
    public function cetakSlip($id)
    {
        $this->authorizeAccess();
        $user = Auth::user();

        $tempahan = MediaTempahan::with('pemohon', 'pelulus')->findOrFail($id);

        if ($tempahan->user_id !== $user->id && !$user->canManageMedia() && !$user->isPengarah()) {
            abort(403, 'Akses Ditolak: Anda tidak dibenarkan mencetak slip tempahan media pemohon lain.');
        }

        return view('media.cetak-slip', compact('tempahan', 'user'));
    }
}
