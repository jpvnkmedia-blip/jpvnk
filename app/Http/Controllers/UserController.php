<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pemunya;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Closure;

class UserController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            function (Request $request, Closure $next) {
                if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
                    abort(403, 'Akses Ditolak: Hanya Super Admin yang dibenarkan mengakses Pengurusan Pengguna.');
                }
                return $next($request);
            }
        ];
    }

    public static function getRoleDefinitions(): array
    {
        return [
            'Pentadbiran & Pengurusan Sistem' => [
                'super_admin' => [
                    'label' => 'Super Admin',
                    'desc' => 'Akses penuh ke semua modul sistem dan pengurusan pengguna pentadbir.',
                    'badge' => 'bg-purple-100 text-purple-800 border-purple-300',
                    'icon' => 'fa-crown text-purple-600',
                ],
                'pengarah' => [
                    'label' => 'Pengarah Perkhidmatan Veterinar Negeri',
                    'desc' => 'Pegawai tertinggi jabatan, pegawai pelulus lesen EPU (Enakmen Penternakan Unggas), rayuan, dan pemantauan analitik/laporan eksekutif semua modul.',
                    'badge' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                    'icon' => 'fa-user-tie text-emerald-700',
                ],
                'admin_eptr' => [
                    'label' => 'Admin EPTR Negeri',
                    'desc' => 'Pengurusan pendaftaran ruminan, permit sembelihan & pemindahan peringkat negeri.',
                    'badge' => 'bg-amber-100 text-amber-800 border-amber-300',
                    'icon' => 'fa-cow text-amber-600',
                ],
                'admin_jajahan' => [
                    'label' => 'Admin EPTR Jajahan',
                    'desc' => 'Kelulusan tag telinga, verifikasi dan pengesahan ternakan mengikut Jajahan.',
                    'badge' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                    'icon' => 'fa-landmark text-emerald-600',
                ],
                'admin_program' => [
                    'label' => 'Admin Program Pawah',
                    'desc' => 'Pengurusan skim bantuan pawah, perjanjian pembiakan dan pemantauan kelahiran.',
                    'badge' => 'bg-teal-100 text-teal-800 border-teal-300',
                    'icon' => 'fa-handshake-angle text-teal-600',
                ],
                'admin_epu_negeri' => [
                    'label' => 'Admin EPU Negeri',
                    'desc' => 'Pengurusan Enakmen Perladangan Unggas, kelulusan lesen Borang B & pemantauan permit peringkat Negeri.',
                    'badge' => 'bg-orange-100 text-orange-800 border-orange-300',
                    'icon' => 'fa-feather text-orange-600',
                ],
                'admin_epu_jajahan' => [
                    'label' => 'Admin EPU Jajahan (Pegawai Verifikasi)',
                    'desc' => 'Semakan kelengkapan dokumen, verifikasi kepatuhan tapak & laporan pemeriksaan Borang D mengikut Jajahan.',
                    'badge' => 'bg-amber-100 text-amber-800 border-amber-300',
                    'icon' => 'fa-clipboard-check text-amber-600',
                ],
                'admin_naimbif_negeri' => [
                    'label' => 'Admin NAIMbif Negeri',
                    'desc' => 'Kelulusan rasmi geran/program Ladang Bridlot Pedaging NAIMbif peringkat negeri.',
                    'badge' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                    'icon' => 'fa-cow text-emerald-600',
                ],
                'admin_naimbif_jajahan' => [
                    'label' => 'Admin NAIMbif Jajahan',
                    'desc' => 'Siasatan premis, verifikasi ladang bridlot dan perakuan syor mengikut Jajahan.',
                    'badge' => 'bg-teal-100 text-teal-800 border-teal-300',
                    'icon' => 'fa-clipboard-check text-teal-600',
                ],
                'admin_kursus' => [
                    'label' => 'Admin Kursus',
                    'desc' => 'Penerbitan modul latihan, jadual kursus penternakan dan pengurusan peserta.',
                    'badge' => 'bg-cyan-100 text-cyan-800 border-cyan-300',
                    'icon' => 'fa-graduation-cap text-cyan-600',
                ],
                'admin_ubat' => [
                    'label' => 'Admin Stor Ubat & Farmasi',
                    'desc' => 'Pengurusan inventori ubat, vaksin veterinar dan kelulusan pesanan jajahan.',
                    'badge' => 'bg-rose-100 text-rose-800 border-rose-300',
                    'icon' => 'fa-pills text-rose-600',
                ],
                'admin_klinik' => [
                    'label' => 'Admin Klinik Haiwan',
                    'desc' => 'Pengurusan temujanji rawatan klinikal, surgeri dan kad rawatan haiwan.',
                    'badge' => 'bg-pink-100 text-pink-800 border-pink-300',
                    'icon' => 'fa-stethoscope text-pink-600',
                ],
                'admin_pejabat' => [
                    'label' => 'Pegawai Stor Pejabat (Kemasukan Data)',
                    'desc' => 'Pendaftaran stok alatan pejabat, kemasukan data transaksi, rekod pinjaman dan serahan bekalan.',
                    'badge' => 'bg-indigo-100 text-indigo-800 border-indigo-300',
                    'icon' => 'fa-boxes-stacked text-indigo-600',
                ],
                'pegawai_pengesah_pejabat' => [
                    'label' => 'Pegawai Pengesah & Pelulus Stor Pejabat',
                    'desc' => 'Semakan, pengesahan dan kelulusan atau penolakan permohonan stok alatan pejabat daripada kakitangan.',
                    'badge' => 'bg-teal-100 text-teal-800 border-teal-300',
                    'icon' => 'fa-clipboard-check text-teal-600',
                ],
                'admin_kenderaan' => [
                    'label' => 'Admin Kenderaan & Fleet',
                    'desc' => 'Pengurusan armada kenderaan rasmi jabatan, jadual pemandu dan kelulusan tempahan perjalanan.',
                    'badge' => 'bg-blue-100 text-blue-800 border-blue-300',
                    'icon' => 'fa-truck-pickup text-blue-600',
                ],
                'admin_media' => [
                    'label' => 'Admin Media Jabatan',
                    'desc' => 'Pengurusan tempahan liputan media, jurufoto, videografi, reka bentuk dan kelulusan permohonan di Panel Tindakan Unit Media.',
                    'badge' => 'bg-indigo-100 text-indigo-800 border-indigo-300',
                    'icon' => 'fa-camera text-indigo-600',
                ],
                'staf' => [
                    'label' => 'Kakitangan Jabatan (Staf)',
                    'desc' => 'Staf JPVNK yang boleh memohon alatan stor, tempahan kenderaan, dan tempahan unit media.',
                    'badge' => 'bg-blue-100 text-blue-800 border-blue-300',
                    'icon' => 'fa-user-tie text-blue-600',
                ],
            ],
            'Penternak & Pengguna Luar' => [
                'penternak' => [
                    'label' => 'Penternak Ruminan',
                    'desc' => 'Pemilik ternakan lembu, kambing, biri-biri dan kerbau (Profil Pemunya dicipta automatik).',
                    'badge' => 'bg-lime-100 text-lime-800 border-lime-300',
                    'icon' => 'fa-wheat-awn text-lime-600',
                ],
                'usahawan' => [
                    'label' => 'Usahawan Unggas / Komersial',
                    'desc' => 'Pengusaha ladang ayam pedaging, penelur, itik dan puyuh komersial.',
                    'badge' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                    'icon' => 'fa-briefcase text-yellow-600',
                ],
                'orang_awam' => [
                    'label' => 'Orang Awam',
                    'desc' => 'Pengguna awam yang memohon permit sembelihan, temujanji klinik atau kursus.',
                    'badge' => 'bg-slate-100 text-slate-800 border-slate-300',
                    'icon' => 'fa-user text-slate-600',
                ],
            ],
        ];
    }

    public function index(Request $request)
    {
        $query = User::query();

        // Carian Nama, IC, Emel, Telefon
        if ($request->filled('search')) {
            $search = trim($request->search);
            $cleanIc = str_replace(['-', ' '], '', $search);
            $query->where(function ($q) use ($search, $cleanIc) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('ic_number', 'like', "%{$search}%")
                  ->orWhere('ic_number', 'like', "%{$cleanIc}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Tapis Peranan (Role)
        if ($request->filled('role') && $request->role !== 'semua') {
            $selectedRole = $request->role;
            if ($selectedRole === 'admin_jajahan') {
                $query->where(function ($q) {
                    $q->whereIn('role', ['admin_jajahan', 'admin_eptr_jajahan'])
                      ->orWhereJsonContains('roles', 'admin_jajahan')
                      ->orWhereJsonContains('roles', 'admin_eptr_jajahan');
                });
            } elseif ($selectedRole === 'admin_epu_negeri') {
                $query->where(function ($q) {
                    $q->whereIn('role', ['admin_epu_negeri', 'admin_epu', 'pegawai_pelesen'])
                      ->orWhereJsonContains('roles', 'admin_epu_negeri')
                      ->orWhereJsonContains('roles', 'admin_epu')
                      ->orWhereJsonContains('roles', 'pegawai_pelesen');
                });
            } elseif ($selectedRole === 'admin_epu_jajahan') {
                $query->where(function ($q) {
                    $q->whereIn('role', ['admin_epu_jajahan', 'pegawai_verifikasi_epu'])
                      ->orWhereJsonContains('roles', 'admin_epu_jajahan')
                      ->orWhereJsonContains('roles', 'pegawai_verifikasi_epu');
                });
            } elseif ($selectedRole === 'admin_naimbif_negeri') {
                $query->where(function ($q) {
                    $q->whereIn('role', ['admin_naimbif_negeri', 'admin_naimbif'])
                      ->orWhereJsonContains('roles', 'admin_naimbif_negeri')
                      ->orWhereJsonContains('roles', 'admin_naimbif');
                });
            } elseif ($selectedRole === 'admin_naimbif_jajahan') {
                $query->where(function ($q) {
                    $q->where('role', 'admin_naimbif_jajahan')
                      ->orWhereJsonContains('roles', 'admin_naimbif_jajahan');
                });
            } elseif ($selectedRole === 'admin_pejabat') {
                $query->where(function ($q) {
                    $q->whereIn('role', ['admin_pejabat', 'admin_stor_pejabat'])
                      ->orWhereJsonContains('roles', 'admin_pejabat')
                      ->orWhereJsonContains('roles', 'admin_stor_pejabat');
                });
            } elseif ($selectedRole === 'pegawai_pengesah_pejabat') {
                $query->where(function ($q) {
                    $q->whereIn('role', ['pegawai_pengesah_pejabat', 'admin_pelulus_pejabat'])
                      ->orWhereJsonContains('roles', 'pegawai_pengesah_pejabat')
                      ->orWhereJsonContains('roles', 'admin_pelulus_pejabat');
                });
            } else {
                $query->where(function ($q) use ($selectedRole) {
                    $q->where('role', $selectedRole)
                      ->orWhereJsonContains('roles', $selectedRole);
                });
            }
        }

        // Tapis Jajahan
        if ($request->filled('jajahan') && $request->jajahan !== 'semua') {
            $query->where('jajahan', $request->jajahan);
        }

        // Tapis Status
        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('status', $request->status);
        }

        $users = $query->orderBy('role')->orderBy('name')->paginate(20)->withQueryString();

        // Statistik Keseluruhan
        $totalUsers = User::count();
        $totalStaff = User::whereIn('role', [
            'super_admin', 'pengarah', 'admin_media', 'admin_eptr', 'admin_jajahan', 'admin_eptr_jajahan',
            'admin_program', 'admin_epu_negeri', 'admin_epu', 'pegawai_pelesen',
            'admin_epu_jajahan', 'pegawai_verifikasi_epu',
            'admin_kursus', 'admin_ubat', 'admin_klinik', 'admin_pejabat', 'admin_stor_pejabat',
            'pegawai_pengesah_pejabat', 'admin_pelulus_pejabat', 'admin_kenderaan',
            'admin_naimbif_negeri', 'admin_naimbif', 'admin_naimbif_jajahan', 'staf'
        ])->count();
        $totalPenternak = User::where('role', 'penternak')->count();
        $totalUsahawan = User::where('role', 'usahawan')->count();
        $totalAwam = User::where('role', 'orang_awam')->count();
        $totalAktif = User::where('status', 'Aktif')->count();

        $kelantanData = config('kelantan.jajahan', []);
        $jajahanList = array_keys($kelantanData);
        $roleDefinitions = self::getRoleDefinitions();

        return view('users.index', compact(
            'users',
            'totalUsers',
            'totalStaff',
            'totalPenternak',
            'totalUsahawan',
            'totalAwam',
            'totalAktif',
            'jajahanList',
            'roleDefinitions'
        ));
    }

    public function create()
    {
        $kelantanData = config('kelantan.jajahan', []);
        $jajahanList = array_keys($kelantanData);
        $roleDefinitions = self::getRoleDefinitions();

        return view('users.create', compact('jajahanList', 'kelantanData', 'roleDefinitions'));
    }

    public function store(Request $request)
    {
        $cleanIc = str_replace(['-', ' '], '', $request->input('ic_number', ''));
        $request->merge(['ic_number' => $cleanIc]);

        // Sokong kedua-dua input roles (array) dan role tunggal (backward compatibility)
        $rolesInput = $request->input('roles');
        if (empty($rolesInput) && $request->filled('role')) {
            $rolesInput = is_array($request->input('role')) ? $request->input('role') : [$request->input('role')];
        }
        if (!is_array($rolesInput)) {
            $rolesInput = $rolesInput ? [$rolesInput] : [];
        }
        $request->merge(['roles' => $rolesInput]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'jawatan' => ['required', 'string', 'max:150'],
            'bahagian_unit' => ['required', 'string', 'max:200'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'ic_number' => ['required', 'string', 'max:20', 'unique:users,ic_number'],
            'phone' => ['required', 'string', 'max:25'],
            'address' => ['required', 'string'],
            'jajahan' => ['required', 'string'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['string'],
            'status' => ['required', 'in:Aktif,Tidak Aktif'],
            'password' => ['nullable', 'confirmed', Password::min(6)],
            'signature' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ], [
            'name.required' => 'Nama penuh pengguna wajib diisi.',
            'jawatan.required' => 'Jawatan pengguna wajib diisi.',
            'bahagian_unit.required' => 'Bahagian / Unit / Pejabat Perkhidmatan Veterinar Jajahan wajib diisi.',
            'email.required' => 'Alamat emel wajib diisi.',
            'email.unique' => 'Alamat emel ini telah pun digunakan oleh pengguna lain.',
            'ic_number.required' => 'No. Kad Pengenalan wajib diisi.',
            'ic_number.unique' => 'No. Kad Pengenalan ini telah pun didaftarkan dalam sistem.',
            'phone.required' => 'No. Telefon wajib diisi.',
            'address.required' => 'Alamat kediaman / pejabat wajib diisi.',
            'jajahan.required' => 'Sila pilih Jajahan.',
            'roles.required' => 'Sila pilih sekurang-kurangnya satu peranan untuk pengguna.',
            'roles.min' => 'Sila pilih sekurang-kurangnya satu peranan untuk pengguna.',
            'password.confirmed' => 'Pengesahan kata laluan tidak sepadan.',
            'password.min' => 'Kata laluan mestilah sekurang-kurangnya 6 aksara.',
            'signature.image' => 'Fail tandatangan mestilah imej yang sah.',
            'signature.mimes' => 'Format tandatangan mestilah JPEG, PNG, JPG atau WEBP.',
            'signature.max' => 'Saiz fail tandatangan tidak boleh melebihi 2MB.',
        ]);

        $finalPassword = !empty($validated['password'])
            ? $validated['password']
            : User::generateDefaultPassword($validated['ic_number']);

        $primaryRole = in_array('super_admin', $validated['roles']) ? 'super_admin' : $validated['roles'][0];

        $signaturePath = null;
        if ($request->hasFile('signature')) {
            $signaturePath = $this->uploadFileSafely($request->file('signature'), 'signatures');
        }

        $user = User::create([
            'name' => $validated['name'],
            'jawatan' => $validated['jawatan'],
            'bahagian_unit' => $validated['bahagian_unit'],
            'email' => $validated['email'],
            'ic_number' => $validated['ic_number'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'jajahan' => $validated['jajahan'],
            'role' => $primaryRole,
            'roles' => $validated['roles'],
            'auth_provider' => 'manual',
            'status' => $validated['status'],
            'password' => Hash::make($finalPassword),
            'signature' => $signaturePath,
        ]);

        // Jika peranan penternak dipilih, cipta atau pautkan profil Pemunya Ternakan
        if (in_array('penternak', $validated['roles'])) {
            Pemunya::firstOrCreate(
                ['no_kp' => $user->ic_number],
                [
                    'user_id' => $user->id,
                    'nama' => $user->name,
                    'no_telefon' => $user->phone,
                    'alamat' => $user->address,
                    'jajahan' => $user->jajahan,
                    'status' => 'Aktif',
                ]
            );
        }

        return redirect()->route('users.index')->with('success', "Akaun pengguna baharu berjaya didaftarkan untuk: {$user->name} ({$user->role_label})!");
    }

    public function show($id)
    {
        $targetUser = User::with([
            'pemunya.ternakan',
            'pawahPerjanjian',
            'ladangUnggas',
            'permohonanKursus.course',
            'temujanjiKlinik',
            'tempahanKenderaan.kenderaan',
            'permohonanInventori'
        ])->findOrFail($id);

        return view('users.show', compact('targetUser'));
    }

    public function edit($id)
    {
        $targetUser = User::findOrFail($id);
        $kelantanData = config('kelantan.jajahan', []);
        $jajahanList = array_keys($kelantanData);
        $roleDefinitions = self::getRoleDefinitions();

        return view('users.edit', compact('targetUser', 'jajahanList', 'kelantanData', 'roleDefinitions'));
    }

    public function update(Request $request, $id)
    {
        $targetUser = User::findOrFail($id);
        $currentUser = Auth::user();

        $cleanIc = str_replace(['-', ' '], '', $request->input('ic_number', ''));
        $request->merge(['ic_number' => $cleanIc]);

        // Sokong kedua-dua input roles (array) dan role tunggal (backward compatibility)
        $rolesInput = $request->input('roles');
        if (empty($rolesInput) && $request->filled('role')) {
            $rolesInput = is_array($request->input('role')) ? $request->input('role') : [$request->input('role')];
        }
        if (!is_array($rolesInput)) {
            $rolesInput = $rolesInput ? [$rolesInput] : [];
        }
        $request->merge(['roles' => $rolesInput]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'jawatan' => ['required', 'string', 'max:150'],
            'bahagian_unit' => ['required', 'string', 'max:200'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $id],
            'ic_number' => ['required', 'string', 'max:20', 'unique:users,ic_number,' . $id],
            'phone' => ['required', 'string', 'max:25'],
            'address' => ['required', 'string'],
            'jajahan' => ['required', 'string'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['string'],
            'status' => ['required', 'in:Aktif,Tidak Aktif'],
            'password' => ['nullable', 'confirmed', Password::min(6)],
            'signature' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ], [
            'name.required' => 'Nama penuh pengguna wajib diisi.',
            'jawatan.required' => 'Jawatan pengguna wajib diisi.',
            'bahagian_unit.required' => 'Bahagian / Unit / Pejabat Perkhidmatan Veterinar Jajahan wajib diisi.',
            'email.required' => 'Alamat emel wajib diisi.',
            'email.unique' => 'Alamat emel ini telah pun digunakan oleh pengguna lain.',
            'ic_number.required' => 'No. Kad Pengenalan wajib diisi.',
            'ic_number.unique' => 'No. Kad Pengenalan ini telah pun didaftarkan.',
            'roles.required' => 'Sila pilih sekurang-kurangnya satu peranan untuk pengguna.',
            'roles.min' => 'Sila pilih sekurang-kurangnya satu peranan untuk pengguna.',
            'password.confirmed' => 'Pengesahan kata laluan tidak sepadan.',
            'password.min' => 'Kata laluan mestilah sekurang-kurangnya 6 aksara.',
            'signature.image' => 'Fail tandatangan mestilah imej yang sah.',
            'signature.mimes' => 'Format tandatangan mestilah JPEG, PNG, JPG atau WEBP.',
            'signature.max' => 'Saiz fail tandatangan tidak boleh melebihi 2MB.',
        ]);

        // Lindungi akaun sendiri dari diturunkan taraf atau dinyahaktif
        if ($currentUser->id === $targetUser->id) {
            if (!in_array('super_admin', $validated['roles'])) {
                return back()->with('error', 'Akses Ditolak: Anda tidak boleh membuang peranan Super Admin daripada akaun anda sendiri.');
            }
            if ($validated['status'] !== 'Aktif') {
                return back()->with('error', 'Akses Ditolak: Anda tidak boleh menyahaktifkan akaun anda sendiri.');
            }
        }

        $primaryRole = in_array('super_admin', $validated['roles']) ? 'super_admin' : $validated['roles'][0];

        $targetUser->name = $validated['name'];
        $targetUser->jawatan = $validated['jawatan'];
        $targetUser->bahagian_unit = $validated['bahagian_unit'];
        $targetUser->email = $validated['email'];
        $targetUser->ic_number = $validated['ic_number'];
        $targetUser->phone = $validated['phone'];
        $targetUser->address = $validated['address'];
        $targetUser->jajahan = $validated['jajahan'];
        $targetUser->role = $primaryRole;
        $targetUser->roles = $validated['roles'];
        $targetUser->status = $validated['status'];

        if (!empty($validated['password'])) {
            $targetUser->password = Hash::make($validated['password']);
        }

        if ($request->hasFile('signature')) {
            $newSignature = $this->uploadFileSafely($request->file('signature'), 'signatures');
            if ($newSignature) {
                if (!empty($targetUser->signature) && is_string($targetUser->signature) && Storage::disk('public')->exists($targetUser->signature)) {
                    Storage::disk('public')->delete($targetUser->signature);
                }
                $targetUser->signature = $newSignature;
            }
        }

        $targetUser->save();

        // Kemaskini atau cipta profil Pemunya jika peranan adalah penternak
        if (in_array('penternak', $validated['roles'])) {
            if ($targetUser->pemunya) {
                $targetUser->pemunya->update([
                    'nama' => $targetUser->name,
                    'no_kp' => $targetUser->ic_number,
                    'no_telefon' => $targetUser->phone,
                    'alamat' => $targetUser->address,
                    'jajahan' => $targetUser->jajahan,
                ]);
            } else {
                Pemunya::firstOrCreate(
                    ['no_kp' => $targetUser->ic_number],
                    [
                        'user_id' => $targetUser->id,
                        'nama' => $targetUser->name,
                        'no_telefon' => $targetUser->phone,
                        'alamat' => $targetUser->address,
                        'jajahan' => $targetUser->jajahan,
                        'status' => 'Aktif',
                    ]
                );
            }
        }

        return redirect()->route('users.index')->with('success', "Maklumat pengguna {$targetUser->name} berjaya dikemaskini!");
    }

    public function toggleStatus($id)
    {
        $targetUser = User::findOrFail($id);
        $currentUser = Auth::user();

        if ($currentUser->id === $targetUser->id) {
            return back()->with('error', 'Akses Ditolak: Anda tidak boleh menyahaktifkan akaun anda sendiri.');
        }

        $targetUser->status = ($targetUser->status === 'Aktif') ? 'Tidak Aktif' : 'Aktif';
        $targetUser->save();

        return back()->with('success', "Status akaun {$targetUser->name} telah ditukar kepada: {$targetUser->status}.");
    }

    public function destroy($id)
    {
        $targetUser = User::findOrFail($id);
        $currentUser = Auth::user();

        if ($currentUser->id === $targetUser->id) {
            return back()->with('error', 'Akses Ditolak: Anda tidak boleh memadam akaun Super Admin anda sendiri.');
        }

        $name = $targetUser->name;
        if (!empty($targetUser->signature) && is_string($targetUser->signature) && Storage::disk('public')->exists($targetUser->signature)) {
            Storage::disk('public')->delete($targetUser->signature);
        }
        $targetUser->delete();

        return redirect()->route('users.index')->with('success', "Akaun pengguna {$name} telah berjaya dipadam dari sistem.");
    }

    public function multiDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'integer', 'exists:users,id'],
        ], [
            'ids.required' => 'Sila pilih sekurang-kurangnya satu akaun pengguna untuk dipadam.',
            'ids.min' => 'Sila pilih sekurang-kurangnya satu akaun pengguna untuk dipadam.',
            'ids.*.exists' => 'Salah satu pengguna yang dipilih tidak wujud dalam pangkalan data.',
        ]);

        $currentUser = Auth::user();
        $selectedIds = $validated['ids'];

        // Tapis keluar akaun Super Admin sendiri jika terpilih secara tidak sengaja
        $validIds = collect($selectedIds)
            ->reject(fn($id) => (int)$id === (int)$currentUser->id)
            ->values()
            ->all();

        if (empty($validIds)) {
            return back()->with('error', 'Akses Ditolak: Anda tidak boleh memadam akaun Super Admin anda sendiri.');
        }

        $users = User::whereIn('id', $validIds)->get();
        $deletedCount = 0;

        foreach ($users as $user) {
            if (!empty($user->signature) && is_string($user->signature) && Storage::disk('public')->exists($user->signature)) {
                Storage::disk('public')->delete($user->signature);
            }
            $user->delete();
            $deletedCount++;
        }

        $skippedSelf = in_array($currentUser->id, $selectedIds);
        $message = "Sebanyak {$deletedCount} akaun pengguna berjaya dipadam secara pukal dari sistem.";
        if ($skippedSelf) {
            $message .= " (Akaun Super Admin anda sendiri telah dikecualikan daripada pemadaman).";
        }

        return redirect()->route('users.index')->with('success', $message);
    }

    /**
     * Simpan fail muat naik dengan selamat
     */
    protected function uploadFileSafely($file, string $folder = 'signatures'): ?string
    {
        if (!$file || !$file->isValid()) {
            return null;
        }

        $extension = $file->getClientOriginalExtension() ?: 'png';
        $filename = time() . '_' . uniqid() . '.' . $extension;

        try {
            return $file->storeAs($folder, $filename, 'public');
        } catch (\Throwable $e) {
            try {
                $targetDir = storage_path('app/public/' . $folder);
                if (!file_exists($targetDir)) {
                    @mkdir($targetDir, 0755, true);
                }
                $file->move($targetDir, $filename);
                return $folder . '/' . $filename;
            } catch (\Throwable $inner) {
                return null;
            }
        }
    }
}
