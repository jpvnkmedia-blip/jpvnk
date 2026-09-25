<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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
        'Pendaftaran Ternakan (EPTR)' => 'Pendaftaran Ternakan (EPTR)',
        'Pelesenan & Permit (EPU)' => 'Pelesenan & Permit (EPU)',
        'Skim Pawah & Ternakan' => 'Skim Pawah & Ternakan',
        'Khidmat Rawatan & Klinikal' => 'Khidmat Rawatan & Klinikal',
        'Program & Kursus Ternakan' => 'Program & Kursus Ternakan',
        'Pengurusan Stor & Inventori' => 'Pengurusan Stor & Inventori',
        'Tempahan Kenderaan & Media' => 'Tempahan Kenderaan & Media',
        'Pengurusan Pengguna & Pentadbiran' => 'Pengurusan Pengguna & Pentadbiran',
        'Lawatan Lapangan' => 'Lawatan Lapangan',
        'Mesyuarat / Perbincangan' => 'Mesyuarat / Perbincangan',
        'Pemeriksaan & Audit Premis' => 'Pemeriksaan & Audit Premis',
        'Penguatkuasaan & Kawalan' => 'Penguatkuasaan & Kawalan',
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
     * Dapatkan tarikh diformatkan
     */
    public function getTarikhFormattedAttribute(): string
    {
        return $this->tarikh ? Carbon::parse($this->tarikh)->format('d/m/Y') : '-';
    }

    /**
     * Dapatkan label kategori
     */
    public function getKategoriLabelAttribute(): string
    {
        return self::KATEGORI_LIST[$this->kategori_aktiviti] ?? ($this->kategori_aktiviti ?: 'Tugas Pentadbiran');
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
            'Pendaftaran Ternakan (EPTR)' => 'fa-solid fa-tags text-emerald-600',
            'Pelesenan & Permit (EPU)' => 'fa-solid fa-certificate text-blue-600',
            'Skim Pawah & Ternakan' => 'fa-solid fa-cow text-amber-600',
            'Khidmat Rawatan & Klinikal' => 'fa-solid fa-stethoscope text-rose-600',
            'Program & Kursus Ternakan' => 'fa-solid fa-graduation-cap text-teal-600',
            'Pengurusan Stor & Inventori' => 'fa-solid fa-boxes-stacked text-indigo-600',
            'Tempahan Kenderaan & Media' => 'fa-solid fa-car-side text-sky-600',
            'Pengurusan Pengguna & Pentadbiran' => 'fa-solid fa-users-gear text-purple-600',
            'Lawatan Lapangan' => 'fa-solid fa-person-walking-luggage text-emerald-600',
            'Mesyuarat / Perbincangan' => 'fa-solid fa-handshake text-blue-600',
            'Pemeriksaan & Audit Premis' => 'fa-solid fa-clipboard-check text-purple-600',
            'Penguatkuasaan & Kawalan' => 'fa-solid fa-shield-halved text-indigo-600',
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

    /**
     * Perekodan automatik aktiviti / tindakan admin merentasi semua modul JPVNK
     */
    public static function catatAktiviti(array $data): ?self
    {
        try {
            $user = auth()->user();
            $jajahan = $data['jajahan'] ?? ($user->jajahan ?? 'Pasir Puteh');
            if (empty($jajahan) || $jajahan === 'Semua') {
                $jajahan = 'Pasir Puteh';
            }
            
            $noBil = self::generateNoBil($jajahan);

            return self::create([
                'no_bil' => $noBil,
                'tajuk_aktiviti' => $data['tajuk_aktiviti'] ?? 'Aktiviti Pentadbiran',
                'kategori_aktiviti' => $data['kategori_aktiviti'] ?? 'Tugas Pentadbiran',
                'maklumat_aktiviti' => $data['maklumat_aktiviti'] ?? '-',
                'tarikh' => $data['tarikh'] ?? Carbon::now()->toDateString(),
                'masa_mula' => $data['masa_mula'] ?? Carbon::now()->format('h:i A'),
                'masa_selesai' => $data['masa_selesai'] ?? null,
                'lokasi' => $data['lokasi'] ?? ('Pejabat Perkhidmatan Veterinar Jajahan ' . $jajahan),
                'jajahan' => $jajahan,
                'nama_pegawai' => $data['nama_pegawai'] ?? ($user ? ($user->name . ' (' . ($user->role_label ?? $user->role) . ')') : 'Sistem JPVNK'),
                'keutamaan' => $data['keutamaan'] ?? 'Biasa',
                'status' => $data['status'] ?? 'Selesai',
                'tindakan_susulan' => $data['tindakan_susulan'] ?? null,
                'lampiran' => $data['lampiran'] ?? null,
                'pegawai_id' => $user ? $user->id : null,
                'created_by' => $user ? $user->id : null,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Gagal mencatat log Action List: ' . $e->getMessage());
            return null;
        }
    }
}
