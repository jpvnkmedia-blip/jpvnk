<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'nama_syarikat',
        'no_ssm',
        'bentuk_perniagaan',
        'email',
        'ic_number',
        'phone',
        'fax',
        'address',
        'poskod',
        'negeri',
        'jajahan',
        'role',
        'roles',
        'auth_provider',
        'provider_id',
        'avatar',
        'status',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'roles' => 'array',
        ];
    }

    /**
     * Dapatkan senarai semua peranan pengguna
     */
    public function getRolesList(): array
    {
        $list = [];
        if (!empty($this->roles) && is_array($this->roles)) {
            $list = $this->roles;
        }
        if (!empty($this->role) && !in_array($this->role, $list)) {
            array_unshift($list, $this->role);
        }
        return array_values(array_unique(array_filter($list)));
    }

    public function hasAnyRole(array $roles): bool
    {
        $myRoles = $this->getRolesList();
        if (in_array('super_admin', $myRoles)) {
            return true;
        }
        return !empty(array_intersect($roles, $myRoles));
    }

    // Role checks
    public function isSuperAdmin(): bool
    {
        return in_array('super_admin', $this->getRolesList());
    }

    public function isAdmin(): bool
    {
        return $this->hasAnyRole([
            'super_admin',
            'admin_eptr',
            'admin_program',
            'admin_epu_negeri',
            'admin_epu',
            'pegawai_pelesen',
            'admin_epu_jajahan',
            'pegawai_verifikasi_epu',
            'admin_jajahan',
            'admin_eptr_jajahan',
            'admin_naimbif_negeri',
            'admin_naimbif',
            'admin_naimbif_jajahan',
            'admin_kursus',
            'admin_ubat',
            'admin_klinik',
            'admin_pejabat',
            'admin_kenderaan',
        ]);
    }

    public function isPengarah(): bool
    {
        return $this->hasAnyRole(['pengarah', 'super_admin']);
    }

    public function isPegawaiJajahan(): bool
    {
        return $this->hasAnyRole([
            'super_admin',
            'pegawai_jajahan',
            'admin_jajahan',
            'admin_eptr_jajahan',
            'admin_naimbif_jajahan',
            'admin_epu_jajahan',
            'pegawai_verifikasi_epu',
        ]);
    }

    public function isPegawaiNegeri(): bool
    {
        return $this->hasAnyRole([
            'super_admin',
            'pengarah',
            'admin_eptr',
            'admin_program',
            'admin_naimbif_negeri',
            'admin_naimbif',
            'admin_epu_negeri',
            'admin_epu',
            'pegawai_pelesen',
        ]);
    }

    public function isAdminNaimbifNegeri(): bool
    {
        return $this->hasAnyRole([
            'super_admin',
            'pengarah',
            'admin_naimbif_negeri',
            'admin_naimbif',
        ]);
    }

    public function isAdminNaimbifJajahan(): bool
    {
        return $this->hasAnyRole([
            'super_admin',
            'admin_naimbif_jajahan',
            'admin_jajahan',
            'admin_eptr_jajahan',
        ]);
    }

    public function isAdminNaimbif(): bool
    {
        return $this->hasAnyRole([
            'super_admin',
            'pengarah',
            'admin_naimbif_negeri',
            'admin_naimbif',
            'admin_naimbif_jajahan',
        ]);
    }

    public function isAdminPejabat(): bool
    {
        return $this->hasAnyRole(['super_admin', 'admin_pejabat', 'admin_stor_pejabat']);
    }

    public function isAdminKenderaan(): bool
    {
        return $this->hasAnyRole(['super_admin', 'admin_kenderaan']);
    }

    public function isAdminEptr(): bool
    {
        return $this->hasAnyRole(['super_admin', 'admin_eptr', 'admin_jajahan', 'admin_eptr_jajahan']);
    }

    public function isAdminProgram(): bool
    {
        return $this->hasAnyRole(['super_admin', 'admin_program']);
    }

    public function isAdminEpuNegeri(): bool
    {
        return $this->hasAnyRole([
            'super_admin',
            'pengarah',
            'admin_epu_negeri',
            'admin_epu',
            'pegawai_pelesen',
        ]);
    }

    public function isAdminEpuJajahan(): bool
    {
        return $this->hasAnyRole([
            'super_admin',
            'admin_epu_jajahan',
            'pegawai_verifikasi_epu',
            'admin_jajahan',
            'admin_eptr_jajahan',
        ]);
    }

    public function isAdminEpu(): bool
    {
        return $this->hasAnyRole([
            'super_admin',
            'pengarah',
            'admin_epu_negeri',
            'admin_epu',
            'pegawai_pelesen',
            'pegawai_verifikasi_epu',
            'admin_epu_jajahan',
            'admin_jajahan',
            'admin_eptr_jajahan',
        ]);
    }

    public function isPegawaiVerifikasiEpu(): bool
    {
        return $this->hasAnyRole([
            'super_admin',
            'admin_epu_negeri',
            'admin_epu',
            'pegawai_verifikasi_epu',
            'admin_epu_jajahan',
            'admin_jajahan',
            'admin_eptr_jajahan',
        ]);
    }

    public function isPegawaiPelesen(): bool
    {
        return $this->hasAnyRole([
            'super_admin',
            'pengarah',
            'pegawai_pelesen',
            'admin_epu_negeri',
            'admin_epu',
        ]);
    }

    public function canPerformVerifikasi(?string $ladangJajahan = null): bool
    {
        if ($this->isSuperAdmin() || $this->hasRole(['admin_epu', 'admin_epu_negeri'])) {
            return true;
        }

        if ($this->hasRole(['pegawai_verifikasi_epu', 'admin_epu_jajahan', 'admin_jajahan', 'admin_eptr_jajahan'])) {
            if ($ladangJajahan && !empty($this->jajahan)) {
                return strcasecmp($this->jajahan, $ladangJajahan) === 0;
            }
            return true;
        }

        return false;
    }

    public function canPerformKeputusanPelesen(): bool
    {
        return $this->hasAnyRole(['super_admin', 'pengarah', 'pegawai_pelesen', 'admin_epu', 'admin_epu_negeri']);
    }

    public function isAdminKursus(): bool
    {
        return $this->hasAnyRole(['super_admin', 'admin_kursus']);
    }

    public function canPublishCourse(): bool
    {
        return $this->hasAnyRole(['super_admin', 'admin_kursus']);
    }

    public function isAdminJajahan(): bool
    {
        return $this->hasAnyRole(['super_admin', 'admin_jajahan', 'admin_eptr_jajahan']) || ($this->hasRole('admin_eptr') && !empty($this->jajahan));
    }

    public function isAdminEptrJajahan(): bool
    {
        return $this->hasAnyRole(['super_admin', 'admin_jajahan', 'admin_eptr_jajahan']) || ($this->hasRole('admin_eptr') && !empty($this->jajahan));
    }

    public function isAdminUbat(): bool
    {
        return $this->hasAnyRole(['super_admin', 'admin_ubat']);
    }

    public function isAdminKlinik(): bool
    {
        return $this->hasAnyRole(['super_admin', 'admin_klinik']);
    }

    public function canAccessStorPejabat(): bool
    {
        return $this->hasAnyRole([
            'super_admin',
            'admin_pejabat',
            'admin_stor_pejabat',
        ]);
    }

    public function canAccessStorUbat(): bool
    {
        return $this->hasAnyRole([
            'super_admin',
            'admin_ubat',
        ]);
    }

    public function canBookVehicle(): bool
    {
        $roles = $this->getRolesList();
        if (in_array('super_admin', $roles)) {
            return true;
        }
        $blocked = [
            'admin_epu',
            'admin_epu_negeri',
            'admin_epu_jajahan',
            'pegawai_pelesen',
            'pegawai_verifikasi_epu',
            'admin_eptr',
            'admin_program',
            'admin_kursus',
            'admin_ubat',
            'admin_jajahan',
            'admin_eptr_jajahan',
            'admin_naimbif_negeri',
            'admin_naimbif',
            'admin_naimbif_jajahan',
            'penternak',
            'usahawan',
            'orang_awam'
        ];
        return !empty(array_diff($roles, $blocked));
    }

    public function canAccessKenderaan(): bool
    {
        return $this->hasAnyRole([
            'super_admin',
            'admin_kenderaan',
        ]) || $this->canBookVehicle();
    }

    public function canAccessPengurusanPejabat(): bool
    {
        return $this->canAccessStorPejabat() || $this->canAccessKenderaan();
    }

    public function canManageKenderaanFleet(): bool
    {
        return $this->hasAnyRole([
            'super_admin',
            'admin_kenderaan',
        ]);
    }

    public function canAccessEptr(): bool
    {
        $roles = $this->getRolesList();
        if (in_array('super_admin', $roles) || !empty(array_intersect(['admin_eptr', 'admin_jajahan', 'admin_eptr_jajahan', 'penternak', 'orang_awam'], $roles))) {
            return true;
        }
        $blocked = [
            'admin_program',
            'admin_epu',
            'admin_epu_negeri',
            'admin_epu_jajahan',
            'pegawai_pelesen',
            'pegawai_verifikasi_epu',
            'admin_kursus',
            'admin_pejabat',
            'admin_kenderaan',
            'admin_ubat',
            'admin_klinik',
            'staf',
        ];
        return !empty(array_diff($roles, $blocked));
    }

    public function canAccessPawah(): bool
    {
        $roles = $this->getRolesList();
        if (in_array('super_admin', $roles) || !empty(array_intersect(['admin_program', 'penternak', 'orang_awam'], $roles))) {
            return true;
        }
        $blocked = [
            'admin_eptr',
            'admin_epu',
            'admin_epu_negeri',
            'admin_epu_jajahan',
            'pegawai_pelesen',
            'pegawai_verifikasi_epu',
            'admin_kursus',
            'admin_pejabat',
            'admin_kenderaan',
            'admin_ubat',
            'admin_klinik',
            'staf',
        ];
        return !empty(array_diff($roles, $blocked));
    }

    public function canAccessEpu(): bool
    {
        $roles = $this->getRolesList();
        if (in_array('super_admin', $roles) || !empty(array_intersect(['admin_epu', 'admin_epu_negeri', 'admin_epu_jajahan', 'pegawai_pelesen', 'pegawai_verifikasi_epu', 'usahawan', 'penternak', 'orang_awam'], $roles))) {
            return true;
        }
        $blocked = [
            'admin_eptr',
            'admin_program',
            'admin_kursus',
            'admin_pejabat',
            'admin_kenderaan',
            'admin_ubat',
            'admin_klinik',
            'staf',
        ];
        return !empty(array_diff($roles, $blocked));
    }

    public function canAccessKursus(): bool
    {
        $roles = $this->getRolesList();
        if (in_array('super_admin', $roles) || !empty(array_intersect(['admin_kursus', 'penternak', 'usahawan', 'orang_awam', 'staf'], $roles))) {
            return true;
        }
        $blocked = [
            'admin_eptr',
            'admin_epu',
            'admin_epu_negeri',
            'admin_epu_jajahan',
            'pegawai_pelesen',
            'pegawai_verifikasi_epu',
            'admin_program',
            'admin_pejabat',
            'admin_kenderaan',
            'admin_ubat',
            'admin_klinik',
        ];
        return !empty(array_diff($roles, $blocked));
    }

    public function canAccessKlinik(): bool
    {
        $roles = $this->getRolesList();
        if (in_array('super_admin', $roles) || !empty(array_intersect(['admin_klinik', 'penternak', 'usahawan', 'orang_awam'], $roles))) {
            return true;
        }
        $blocked = [
            'admin_eptr',
            'admin_epu',
            'admin_epu_negeri',
            'admin_epu_jajahan',
            'pegawai_pelesen',
            'pegawai_verifikasi_epu',
            'admin_program',
            'admin_kursus',
            'admin_pejabat',
            'admin_kenderaan',
            'admin_ubat',
            'staf',
        ];
        return !empty(array_diff($roles, $blocked));
    }

    public function canAccessInventori(): bool
    {
        return $this->canAccessStorPejabat() || $this->canAccessStorUbat();
    }

    public function canRequestInventori(): bool
    {
        return $this->isStaff() && !$this->hasAnyRole(['admin_program', 'admin_eptr', 'admin_epu', 'pegawai_pelesen', 'pegawai_verifikasi_epu']);
    }

    public function canRequestAlatanPejabat(): bool
    {
        return $this->isStaff() && !$this->hasAnyRole(['admin_program', 'admin_eptr', 'admin_epu', 'pegawai_pelesen', 'pegawai_verifikasi_epu']);
    }

    public function canRequestUbat(): bool
    {
        return $this->hasAnyRole(['admin_jajahan', 'admin_eptr_jajahan', 'super_admin']);
    }

    public function canCetakBorangEpu(): bool
    {
        return $this->hasAnyRole([
            'super_admin',
            'admin_epu',
            'admin_epu_negeri',
            'pegawai_verifikasi_epu',
            'admin_epu_jajahan',
        ]);
    }

    public function canManagePermohonanPejabat(): bool
    {
        return $this->isAdminPejabat() || $this->isSuperAdmin();
    }

    public function canManagePermohonanUbat(): bool
    {
        return $this->isAdminUbat() || $this->isSuperAdmin();
    }

    public function isStaff(): bool
    {
        return $this->hasAnyRole([
            'super_admin',
            'admin_pejabat',
            'admin_kenderaan',
            'admin_ubat',
            'admin_klinik',
            'admin_eptr',
            'admin_program',
            'admin_epu_negeri',
            'admin_epu',
            'pegawai_pelesen',
            'pegawai_verifikasi_epu',
            'admin_epu_jajahan',
            'admin_kursus',
            'admin_jajahan',
            'admin_eptr_jajahan',
            'admin_naimbif_negeri',
            'admin_naimbif',
            'admin_naimbif_jajahan',
            'staf',
        ]);
    }

    public function hasRole(string|array $roles): bool
    {
        $myRoles = $this->getRolesList();
        if (in_array('super_admin', $myRoles)) {
            return true;
        }

        if (is_array($roles)) {
            return !empty(array_intersect($roles, $myRoles));
        }

        return in_array($roles, $myRoles);
    }

    public static function getSingleRoleLabel(string $role, ?string $jajahan = null): string
    {
        return match ($role) {
            'super_admin' => 'Super Admin',
            'admin_pejabat', 'admin_stor_pejabat' => 'Admin Stor Pejabat',
            'admin_kenderaan' => 'Admin Kenderaan & Fleet',
            'admin_ubat' => 'Admin Stor Ubat & Farmasi Veterinar',
            'admin_klinik' => 'Admin Klinik Haiwan & Rawatan',
            'admin_eptr' => 'Admin EPTR Negeri',
            'admin_program' => 'Admin Program Pawah',
            'admin_epu_negeri', 'admin_epu' => 'Admin EPU Negeri',
            'pegawai_pelesen' => 'Pegawai Pelesen / Pengarah (EPU)',
            'pegawai_verifikasi_epu', 'admin_epu_jajahan' => 'Admin EPU Jajahan (PPVJ ' . ($jajahan ?? 'Jajahan') . ')',
            'admin_kursus' => 'Admin Kursus',
            'admin_jajahan', 'admin_eptr_jajahan' => 'Admin EPTR Jajahan (' . ($jajahan ?? 'Kelantan') . ')',
            'admin_naimbif_negeri', 'admin_naimbif' => 'Admin NAIMbif Negeri',
            'admin_naimbif_jajahan' => 'Admin NAIMbif Jajahan (' . ($jajahan ?? 'Kelantan') . ')',
            'staf' => 'Kakitangan Jabatan (Staf Biasa)',
            'penternak' => 'Penternak Ruminan / Ternakan',
            'usahawan' => 'Usahawan Unggas & Ladang',
            'orang_awam' => 'Orang Awam',
            default => ucfirst(str_replace('_', ' ', $role)),
        };
    }

    public function getRoleLabelsAttribute(): array
    {
        $roles = $this->getRolesList();
        return array_map(fn($r) => self::getSingleRoleLabel($r, $this->jajahan), $roles);
    }

    public function getRoleLabelAttribute(): string
    {
        $labels = $this->role_labels;
        return !empty($labels) ? implode(', ', $labels) : 'Orang Awam';
    }

    public function getRolesDataAttribute(): array
    {
        $definitions = \App\Http\Controllers\UserController::getRoleDefinitions();
        $flatDefinitions = [];
        foreach ($definitions as $group => $roles) {
            foreach ($roles as $key => $meta) {
                $flatDefinitions[$key] = $meta;
            }
        }

        $roles = $this->getRolesList();
        $result = [];
        foreach ($roles as $r) {
            if (isset($flatDefinitions[$r])) {
                $result[] = [
                    'role' => $r,
                    'label' => $flatDefinitions[$r]['label'],
                    'badge' => $flatDefinitions[$r]['badge'],
                    'icon' => $flatDefinitions[$r]['icon'],
                    'desc' => $flatDefinitions[$r]['desc'],
                ];
            } else {
                $result[] = [
                    'role' => $r,
                    'label' => self::getSingleRoleLabel($r, $this->jajahan),
                    'badge' => 'bg-slate-100 text-slate-800 border-slate-200',
                    'icon' => 'fa-user',
                    'desc' => '',
                ];
            }
        }
        return $result;
    }

    /**
     * Jana format kata laluan automatik lalai (default): super@DVS[4 angka belakang IC]
     */
    public static function generateDefaultPassword(?string $icNumber): string
    {
        $cleanIc = preg_replace('/[^0-9]/', '', (string)$icNumber);
        $last4 = strlen($cleanIc) >= 4 ? substr($cleanIc, -4) : (strlen($cleanIc) > 0 ? str_pad($cleanIc, 4, '0', STR_PAD_LEFT) : '1234');
        return 'super@DVS' . $last4;
    }

    // Relationships
    public function pemunya()
    {
        return $this->hasOne(Pemunya::class);
    }

    public function pawahPerjanjian()
    {
        return $this->hasMany(PawahPerjanjian::class);
    }

    public function ladangUnggas()
    {
        return $this->hasMany(EpuLadang::class);
    }

    public function permohonanKursus()
    {
        return $this->hasMany(CourseApplication::class);
    }

    public function temujanjiKlinik()
    {
        return $this->hasMany(KlinikTemujanji::class);
    }

    public function tempahanKenderaan()
    {
        return $this->hasMany(KenderaanTempahan::class);
    }

    public function pinjamanInventori()
    {
        return $this->hasMany(InventoriPinjaman::class);
    }

    public function permohonanInventori()
    {
        return $this->hasMany(InventoriPermohonan::class);
    }

    public function canAccessNaimbif(): bool
    {
        $roles = $this->getRolesList();
        if (in_array('super_admin', $roles) || !empty(array_intersect(['admin_naimbif_negeri', 'admin_naimbif', 'admin_naimbif_jajahan', 'admin_eptr', 'admin_jajahan', 'admin_eptr_jajahan', 'penternak', 'orang_awam'], $roles))) {
            return true;
        }
        $blocked = [
            'admin_epu',
            'admin_epu_negeri',
            'admin_epu_jajahan',
            'pegawai_pelesen',
            'pegawai_verifikasi_epu',
            'admin_kursus',
            'admin_pejabat',
            'admin_ubat',
            'admin_klinik',
            'staf',
        ];
        return !empty(array_diff($roles, $blocked));
    }

    public function naimbifPermohonan()
    {
        return $this->hasMany(NaimbifPermohonan::class);
    }

    public function userNotifications()
    {
        return $this->hasMany(UserNotification::class);
    }

    public function unreadNotificationsCount(): int
    {
        return $this->userNotifications()->unread()->count();
    }

    public function notifyActivity(string $title, string $message, string $type = 'sistem', ?string $actionUrl = null, string $icon = 'fa-solid fa-bell', string $color = 'emerald'): UserNotification
    {
        return UserNotification::send($this->id, $title, $message, $type, $actionUrl, $icon, $color);
    }
}
