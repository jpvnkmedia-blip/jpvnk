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
        ];
    }

    // Role checks
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdminPejabat(): bool
    {
        return in_array($this->role, ['super_admin', 'admin_pejabat']);
    }

    public function isAdminEptr(): bool
    {
        return in_array($this->role, ['super_admin', 'admin_eptr', 'admin_jajahan', 'admin_eptr_jajahan']);
    }

    public function isAdminProgram(): bool
    {
        return in_array($this->role, ['super_admin', 'admin_program']);
    }

    public function isAdminEpu(): bool
    {
        return in_array($this->role, ['super_admin', 'admin_epu', 'admin_jajahan', 'admin_eptr_jajahan']);
    }

    public function isAdminKursus(): bool
    {
        return in_array($this->role, ['super_admin', 'admin_kursus']);
    }

    public function canPublishCourse(): bool
    {
        return in_array($this->role, ['super_admin', 'admin_kursus']);
    }

    public function isAdminJajahan(): bool
    {
        return in_array($this->role, ['super_admin', 'admin_jajahan', 'admin_eptr_jajahan']) || ($this->role === 'admin_eptr' && !empty($this->jajahan));
    }

    public function isAdminEptrJajahan(): bool
    {
        return in_array($this->role, ['super_admin', 'admin_jajahan', 'admin_eptr_jajahan']) || ($this->role === 'admin_eptr' && !empty($this->jajahan));
    }

    public function isAdminUbat(): bool
    {
        return in_array($this->role, ['super_admin', 'admin_ubat']);
    }

    public function isAdminKlinik(): bool
    {
        return in_array($this->role, ['super_admin', 'admin_klinik']);
    }

    public function canAccessStorPejabat(): bool
    {
        return in_array($this->role, [
            'super_admin',
            'admin_pejabat',
        ]);
    }

    public function canAccessStorUbat(): bool
    {
        return in_array($this->role, [
            'super_admin',
            'admin_ubat',
        ]);
    }

    public function canBookVehicle(): bool
    {
        return !in_array($this->role, [
            'admin_eptr',
            'admin_program',
            'admin_kursus',
            'admin_ubat',
            'admin_jajahan',
            'admin_eptr_jajahan',
            'penternak',
            'usahawan',
            'orang_awam'
        ]);
    }

    public function canAccessKenderaan(): bool
    {
        return in_array($this->role, [
            'super_admin',
            'admin_pejabat',
            'admin_epu',
        ]);
    }

    public function canAccessPengurusanPejabat(): bool
    {
        return $this->canAccessStorPejabat() || $this->canAccessKenderaan();
    }

    public function canManageKenderaanFleet(): bool
    {
        return in_array($this->role, [
            'super_admin',
            'admin_pejabat',
        ]);
    }

    public function canAccessEptr(): bool
    {
        return !in_array($this->role, [
            'admin_program',
            'admin_epu',
            'admin_kursus',
            'admin_pejabat',
            'admin_ubat',
            'admin_klinik',
            'staf',
        ]);
    }

    public function canAccessPawah(): bool
    {
        return !in_array($this->role, [
            'admin_eptr',
            'admin_kursus',
            'admin_pejabat',
            'admin_ubat',
            'admin_klinik',
            'staf',
        ]);
    }

    public function canAccessEpu(): bool
    {
        return !in_array($this->role, [
            'admin_eptr',
            'admin_program',
            'admin_kursus',
            'admin_pejabat',
            'admin_ubat',
            'admin_klinik',
            'staf',
        ]);
    }

    public function canAccessKursus(): bool
    {
        return !in_array($this->role, [
            'admin_eptr',
            'admin_program',
            'admin_pejabat',
            'admin_ubat',
            'admin_klinik',
        ]);
    }

    public function canAccessKlinik(): bool
    {
        return !in_array($this->role, [
            'admin_eptr',
            'admin_program',
            'admin_kursus',
            'admin_pejabat',
            'admin_ubat',
            'staf',
        ]);
    }

    public function canAccessInventori(): bool
    {
        return $this->canAccessStorPejabat() || $this->canAccessStorUbat();
    }

    public function canRequestInventori(): bool
    {
        return $this->isStaff() && !in_array($this->role, ['admin_program', 'admin_eptr']);
    }

    public function canRequestAlatanPejabat(): bool
    {
        return $this->isStaff() && !in_array($this->role, ['admin_program', 'admin_eptr']);
    }

    public function canRequestUbat(): bool
    {
        return in_array($this->role, ['admin_jajahan', 'admin_eptr_jajahan', 'super_admin']);
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
        return in_array($this->role, [
            'super_admin',
            'admin_pejabat',
            'admin_ubat',
            'admin_klinik',
            'admin_eptr',
            'admin_program',
            'admin_epu',
            'admin_kursus',
            'admin_jajahan',
            'admin_eptr_jajahan',
            'staf',
        ]);
    }

    public function hasRole(string|array $roles): bool
    {
        if ($this->role === 'super_admin') {
            return true;
        }

        if (is_array($roles)) {
            return in_array($this->role, $roles);
        }

        return $this->role === $roles;
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'super_admin' => 'Super Admin',
            'admin_pejabat' => 'Admin Pejabat (Stor Pejabat & Kenderaan)',
            'admin_ubat' => 'Admin Stor Ubat & Farmasi Veterinar',
            'admin_klinik' => 'Admin Klinik Haiwan & Rawatan',
            'admin_eptr' => 'Admin EPTR Negeri',
            'admin_program' => 'Admin Program Pawah',
            'admin_epu' => 'Admin EPU',
            'admin_kursus' => 'Admin Kursus',
            'admin_jajahan', 'admin_eptr_jajahan' => 'Admin EPTR Jajahan (' . ($this->jajahan ?? 'Kelantan') . ')',
            'staf' => 'Kakitangan Jabatan (Staf Biasa)',
            'penternak' => 'Penternak Ruminan / Ternakan',
            'usahawan' => 'Usahawan Unggas & Ladang',
            'orang_awam' => 'Orang Awam',
            default => ucfirst(str_replace('_', ' ', $this->role)),
        };
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
