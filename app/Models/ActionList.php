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
        'kod_dokumen',
        'no_bil',
        'jajahan',
        'tarikh',
        'masa_pendaftaran',
        'kategori_pelanggan',
        'nama_pelanggan',
        'no_kp',
        'alamat',
        'mukim',
        'poskod',
        'daerah',
        'telefon',
        'no_rujukan',
        'user_id',
        'catatan_perkhidmatan_dipohon',
        'nama_pegawai',
        'masa_pegawai',
        'masa_temujanji_mula',
        'masa_temujanji_hingga',
        'maklumat_pelanggan_berlainan',
        'maklumat_tambahan',
        'lampiran_peta',
        'perkhidmatan_diberi',
        'keterangan_pembedahan',
        'keterangan_projek',
        'keterangan_lain',
        'jenis_ternakan',
        'jenis_ternakan_lain',
        'bil_ternakan',
        'bil_yang_ada',
        'laporan',
        'penggunaan_ubat',
        'tandatangan_pelanggan_nama',
        'tandatangan_pelanggan_tarikh',
        'tandatangan_pelanggan_masa',
        'kepuasan_pelanggan',
        'cadangan_pelanggan',
        'bayaran',
        'no_resit',
        'pengesahan_ulasan_pegawai',
        'status',
        'pegawai_id',
        'temujanji_id',
        'created_by',
    ];

    protected $casts = [
        'tarikh' => 'date',
        'tandatangan_pelanggan_tarikh' => 'date',
        'perkhidmatan_diberi' => 'array',
        'jenis_ternakan' => 'array',
        'bayaran' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pegawai()
    {
        return $this->belongsTo(User::class, 'pegawai_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function temujanji()
    {
        return $this->belongsTo(KlinikTemujanji::class, 'temujanji_id');
    }

    public function scopeByJajahan($query, $jajahan)
    {
        if ($jajahan && $jajahan !== 'Semua') {
            return $query->where('jajahan', $jajahan);
        }
        return $query;
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

        return sprintf("AL/%s/%s/%04d", $shortJajahan, $year, $count);
    }
}
