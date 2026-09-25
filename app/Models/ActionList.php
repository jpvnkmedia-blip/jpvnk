<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ActionList extends Model
{
    use HasFactory;

    protected $table = 'action_lists';

    protected $fillable = [
        // Medan Utama Dairi / Log Aktiviti Admin
        'tajuk_aktiviti',
        'kategori_aktiviti',
        'maklumat_aktiviti',
        'tarikh',
        'masa_mula',
        'masa_selesai',
        'lokasi',
        'jajahan',
        'nama_pegawai',
        'keutamaan',
        'status',
        'tindakan_susulan',
        'lampiran',
        'pegawai_id',
        'created_by',

        // Keserasian Medan Tambahan
        'kod_dokumen',
        'no_bil',
        'nama_pelanggan',
        'no_kp',
        'telefon',
        'alamat',
        'mukim',
        'poskod',
        'daerah',
        'no_rujukan',
        'user_id',
        'catatan_perkhidmatan_dipohon',
        'laporan',
        'bayaran',
    ];

    protected $casts = [
        'tarikh' => 'date',
        'bayaran' => 'decimal:2',
    ];

    public const KATEGORI_LIST = [
        'Lawatan Lapangan' => 'Lawatan Lapangan',
        'Mesyuarat / Perbincangan' => 'Mesyuarat / Perbincangan',
        'Pemeriksaan & Audit Premis' => 'Pemeriksaan & Audit Premis',
        'Khidmat Rawatan & Klinikal' => 'Khidmat Rawatan & Klinikal',
        'Pemantauan Projek & Pawah' => 'Pemantauan Projek & Pawah',
        'Penguatkuasaan & Kawalan' => 'Penguatkuasaan & Kawalan',
        'Program / Kursus / Latihan' => 'Program / Kursus / Latihan',
        'Tugas Pentadbiran' => 'Tugas Pentadbiran',
        'Lain-lain' => 'Lain-lain',
    ];

    public const STATUS_LIST = [
        'Selesai' => 'Selesai',
        'Dalam Tindakan' => 'Dalam Tindakan',
        'Perancangan' => 'Perancangan',
        'Ditangguhkan' => 'Ditangguhkan',
        'Dibatalkan' => 'Dibatalkan',
    ];

    public const KEUTAMAAN_LIST = [
        'Biasa' => 'Biasa',
        'Tinggi' => 'Tinggi',
        'Segera' => 'Segera',
    ];

    public function pegawai()
    {
        return $this->belongsTo(User::class, 'pegawai_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeByJajahan($query, $jajahan)
    {
        if ($jajahan && $jajahan !== 'Semua') {
            return $query->where('jajahan', $jajahan);
        }
        return $query;
    }

    /**
     * Dapatkan warna badge bagi status
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'Selesai' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            'Dalam Tindakan' => 'bg-amber-100 text-amber-800 border-amber-300',
            'Perancangan' => 'bg-blue-100 text-blue-800 border-blue-300',
            'Ditangguhkan' => 'bg-purple-100 text-purple-800 border-purple-300',
            'Dibatalkan' => 'bg-rose-100 text-rose-800 border-rose-300',
            default => 'bg-slate-100 text-slate-700 border-slate-300',
        };
    }

    /**
     * Dapatkan warna badge bagi keutamaan
     */
    public function getKeutamaanBadgeClassAttribute(): string
    {
        return match ($this->keutamaan) {
            'Segera' => 'bg-rose-100 text-rose-800 border-rose-300',
            'Tinggi' => 'bg-orange-100 text-orange-800 border-orange-300',
            default => 'bg-slate-100 text-slate-700 border-slate-300',
        };
    }

    /**
     * Dapatkan ikon bagi kategori aktiviti
     */
    public function getKategoriIconAttribute(): string
    {
        return match ($this->kategori_aktiviti) {
            'Lawatan Lapangan' => 'fa-solid fa-person-walking-luggage text-emerald-600',
            'Mesyuarat / Perbincangan' => 'fa-solid fa-handshake text-blue-600',
            'Pemeriksaan & Audit Premis' => 'fa-solid fa-clipboard-check text-purple-600',
            'Khidmat Rawatan & Klinikal' => 'fa-solid fa-stethoscope text-rose-600',
            'Pemantauan Projek & Pawah' => 'fa-solid fa-cow text-amber-600',
            'Penguatkuasaan & Kawalan' => 'fa-solid fa-shield-halved text-indigo-600',
            'Program / Kursus / Latihan' => 'fa-solid fa-graduation-cap text-teal-600',
            'Tugas Pentadbiran' => 'fa-solid fa-file-invoice text-slate-600',
            default => 'fa-solid fa-thumbtack text-slate-600',
        };
    }

    public static function generateNoBil(?string $jajahan = null): string
    {
        $year = date('Y');
        $shortJajahan = match ($jajahan) {
            'Pasir Puteh' => 'PP',
            'Kota Bharu' => 'KB',
            'Bachok' => 'BCK',
            'Pasir Mas' => 'PM',
            'Tumpat' => 'TPT',
            'Machang' => 'MCH',
            'Tanah Merah' => 'TM',
            'Kuala Krai' => 'KK',
            'Gua Musang' => 'GM',
            'Jeli' => 'JLI',
            default => 'PPVJ',
        };

        $count = self::whereYear('created_at', $year)
            ->when($jajahan, fn($q) => $q->where('jajahan', $jajahan))
            ->count() + 1;

        return sprintf("ACT/%s/%s/%04d", $shortJajahan, $year, $count);
    }
}
