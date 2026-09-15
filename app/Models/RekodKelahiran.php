<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekodKelahiran extends Model
{
    use HasFactory;

    protected $table = 'rekod_kelahiran';

    protected $fillable = [
        'induk_id',
        'pejantan_id',
        'anak_ternakan_id',
        'pemunya_id',
        'no_tag_sementara',
        'jantina_anak',
        'tarikh_kelahiran',
        'berat_lahir_kg',
        'baka_anak',
        'warna_anak',
        'tanda_badan_anak',
        'status_kelahiran',
        'keadaan_anak',
        'gambar_anak',
        'catatan',
        'didaftar_oleh',
    ];

    protected function casts(): array
    {
        return [
            'tarikh_kelahiran' => 'date',
            'berat_lahir_kg' => 'decimal:2',
        ];
    }

    public function induk()
    {
        return $this->belongsTo(Ternakan::class, 'induk_id');
    }

    public function pejantan()
    {
        return $this->belongsTo(Ternakan::class, 'pejantan_id');
    }

    public function anakTernakan()
    {
        return $this->belongsTo(Ternakan::class, 'anak_ternakan_id');
    }

    public function pemunya()
    {
        return $this->belongsTo(Pemunya::class);
    }

    public function pendaftar()
    {
        return $this->belongsTo(User::class, 'didaftar_oleh');
    }
}
