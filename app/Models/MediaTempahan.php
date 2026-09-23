<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MediaTempahan extends Model
{
    use HasFactory;

    protected $table = 'media_tempahan';

    protected $fillable = [
        'no_rujukan',
        'user_id',
        'nama_pemohon',
        'jawatan',
        'jawatan_pemohon',
        'bahagian_unit_jajahan',
        'bahagian_unit',
        'no_telefon',
        'emel',
        'nama_program',
        'tarikh_program',
        'tarikh_tamat',
        'masa_mula',
        'masa_tamat',
        'lokasi',
        'penganjur',
        'pegawai_bertanggungjawab',
        'anggaran_peserta',
        'jenis_permohonan',
        'butiran_fotografi',
        'keperluan_fotografi',
        'butiran_poster',
        'keperluan_poster',
        'butiran_video',
        'keperluan_video',
        'butiran_lain',
        'catatan_keperluan',
        'tarikh_diperlukan',
        'tahap_keutamaan',
        'keutamaan',
        'sebab_segera',
        'lampiran',
        'pengesahan_pemohon',
        'perakuan',
        'tarikh_hantar',
        'status',
        'catatan_unit_media',
        'catatan_admin',
        'pegawai_media_bertugas',
        'peralatan_disediakan',
        'pautan_hasil_media',
        'diluluskan_oleh',
        'tarikh_kelulusan',
    ];

    protected function casts(): array
    {
        return [
            'tarikh_program' => 'date',
            'tarikh_tamat' => 'date',
            'tarikh_diperlukan' => 'date',
            'tarikh_hantar' => 'datetime',
            'tarikh_kelulusan' => 'datetime',
            'jenis_permohonan' => 'array',
            'butiran_fotografi' => 'array',
            'butiran_poster' => 'array',
            'butiran_video' => 'array',
            'lampiran' => 'array',
            'pengesahan_pemohon' => 'boolean',
        ];
    }

    public function pemohon()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pelulus()
    {
        return $this->belongsTo(User::class, 'diluluskan_oleh');
    }

    public function pegawaiBertugas()
    {
        return $this->belongsTo(User::class, 'diluluskan_oleh');
    }

    // Accessors & Mutators for compatibility
    public function getJawatanPemohonAttribute()
    {
        return $this->attributes['jawatan'] ?? null;
    }

    public function setJawatanPemohonAttribute($value)
    {
        $this->attributes['jawatan'] = $value;
    }

    public function getBahagianUnitAttribute()
    {
        return $this->attributes['bahagian_unit_jajahan'] ?? null;
    }

    public function setBahagianUnitAttribute($value)
    {
        $this->attributes['bahagian_unit_jajahan'] = $value;
    }

    public function getKeutamaanAttribute()
    {
        return $this->attributes['tahap_keutamaan'] ?? 'Biasa';
    }

    public function setKeutamaanAttribute($value)
    {
        $this->attributes['tahap_keutamaan'] = $value;
    }

    public function getCatatanAdminAttribute()
    {
        return $this->attributes['catatan_unit_media'] ?? null;
    }

    public function setCatatanAdminAttribute($value)
    {
        $this->attributes['catatan_unit_media'] = $value;
    }

    public function getCatatanKeperluanAttribute()
    {
        return $this->attributes['butiran_lain'] ?? null;
    }

    public function setCatatanKeperluanAttribute($value)
    {
        $this->attributes['butiran_lain'] = $value;
    }

    public function getKeperluanFotografiAttribute()
    {
        return $this->butiran_fotografi;
    }

    public function setKeperluanFotografiAttribute($value)
    {
        $this->attributes['butiran_fotografi'] = is_array($value) ? json_encode($value) : $value;
    }

    public function getKeperluanPosterAttribute()
    {
        return $this->butiran_poster;
    }

    public function setKeperluanPosterAttribute($value)
    {
        $this->attributes['butiran_poster'] = is_array($value) ? json_encode($value) : $value;
    }

    public function getKeperluanVideoAttribute()
    {
        return $this->butiran_video;
    }

    public function setKeperluanVideoAttribute($value)
    {
        $this->attributes['butiran_video'] = is_array($value) ? json_encode($value) : $value;
    }

    public function setPerakuanAttribute($value)
    {
        $this->attributes['pengesahan_pemohon'] = (bool)$value;
    }

    public static function generateNoRujukan(): string
    {
        $year = date('Y');
        $month = date('m');
        $count = static::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count() + 1;

        return sprintf('MEDIA/%s/%s/%04d', $year, $month, $count);
    }

    // Helper untuk warna status
    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'Diluluskan' => [
                'bg' => 'bg-emerald-50 text-emerald-800 border-emerald-300',
                'dot' => 'bg-emerald-500',
                'label' => 'Diluluskan',
                'icon' => 'fa-circle-check',
            ],
            'Menunggu Kelulusan' => [
                'bg' => 'bg-amber-50 text-amber-800 border-amber-300',
                'dot' => 'bg-amber-500',
                'label' => 'Menunggu Kelulusan',
                'icon' => 'fa-clock',
            ],
            'Perlu Pembetulan' => [
                'bg' => 'bg-sky-50 text-sky-800 border-sky-300',
                'dot' => 'bg-sky-500',
                'label' => 'Perlu Pembetulan',
                'icon' => 'fa-circle-exclamation',
            ],
            'Ditolak' => [
                'bg' => 'bg-rose-50 text-rose-800 border-rose-300',
                'dot' => 'bg-rose-500',
                'label' => 'Ditolak',
                'icon' => 'fa-circle-xmark',
            ],
            'Selesai' => [
                'bg' => 'bg-purple-50 text-purple-800 border-purple-300',
                'dot' => 'bg-purple-500',
                'label' => 'Selesai',
                'icon' => 'fa-flag-checkered',
            ],
            'Dibatalkan' => [
                'bg' => 'bg-slate-100 text-slate-700 border-slate-300',
                'dot' => 'bg-slate-400',
                'label' => 'Dibatalkan',
                'icon' => 'fa-ban',
            ],
            default => [
                'bg' => 'bg-slate-50 text-slate-700 border-slate-300',
                'dot' => 'bg-slate-400',
                'label' => $this->status,
                'icon' => 'fa-circle',
            ],
        };
    }

    // Helper untuk tahap keutamaan badge
    public function getKeutamaanBadgeAttribute(): array
    {
        return match ($this->tahap_keutamaan) {
            'Sangat Segera' => [
                'bg' => 'bg-rose-100 text-rose-800 border-rose-300',
                'icon' => 'fa-triangle-exclamation',
                'label' => 'Sangat Segera',
            ],
            'Segera' => [
                'bg' => 'bg-amber-100 text-amber-800 border-amber-300',
                'icon' => 'fa-bolt',
                'label' => 'Segera',
            ],
            default => [
                'bg' => 'bg-slate-100 text-slate-700 border-slate-300',
                'icon' => 'fa-calendar-check',
                'label' => 'Biasa',
            ],
        };
    }

    /**
     * Dapatkan status ketersediaan tarikh:
     * 🟢 Kosong: 0 tempahan aktif
     * 🟡 Sebahagian Ditempah: 1-2 tempahan aktif
     * 🔴 Penuh: >= 3 tempahan aktif
     */
    public static function getSlotStatusForDate(string $dateString): array
    {
        $activeBookings = static::whereDate('tarikh_program', $dateString)
            ->whereIn('status', ['Menunggu Kelulusan', 'Diluluskan', 'Perlu Pembetulan'])
            ->get();

        $count = $activeBookings->count();

        if ($count === 0) {
            return [
                'status' => 'kosong',
                'color' => 'emerald',
                'bg' => 'bg-emerald-500',
                'text' => 'text-emerald-700',
                'badge' => 'Tarikh Kosong',
                'icon' => 'fa-circle',
                'count' => 0,
                'is_full' => false,
                'bookings' => $activeBookings,
            ];
        } elseif ($count <= 2) {
            return [
                'status' => 'sebahagian',
                'color' => 'amber',
                'bg' => 'bg-amber-500',
                'text' => 'text-amber-700',
                'badge' => "Sebahagian Ditempah ($count Slot)",
                'icon' => 'fa-circle',
                'count' => $count,
                'is_full' => false,
                'bookings' => $activeBookings,
            ];
        } else {
            return [
                'status' => 'penuh',
                'color' => 'rose',
                'bg' => 'bg-rose-500',
                'text' => 'text-rose-700',
                'badge' => "Tarikh Penuh ($count Slot)",
                'icon' => 'fa-circle',
                'count' => $count,
                'is_full' => true,
                'bookings' => $activeBookings,
            ];
        }
    }
}
