<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembatalanTernakan extends Model
{
    use HasFactory;

    protected $table = 'pembatalan_ternakan';

    protected $fillable = [
        'ternakan_id',
        'jenis_batal',
        'tarikh_peristiwa',
        'sebab',
        'no_laporan_polis',
        'dokumen_sokongan',
        'destinasi_pindah_keluar',
        'status_kelulusan',
        'disahkan_oleh',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tarikh_peristiwa' => 'date',
        ];
    }

    public function ternakan()
    {
        return $this->belongsTo(Ternakan::class);
    }

    public function pengesah()
    {
        return $this->belongsTo(User::class, 'disahkan_oleh');
    }
}
