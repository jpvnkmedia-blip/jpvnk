<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramKesihatan extends Model
{
    use HasFactory;

    protected $table = 'program_kesihatan';

    protected $fillable = [
        'ternakan_id',
        'no_rujukan_kesihatan',
        'jenis_program',
        'nama_vaksin_atau_ubat',
        'tarikh_rawatan',
        'tarikh_ulangan_dos',
        'berat_semasa_kg',
        'suhu_badan_celsius',
        'status_kesihatan',
        'diagnosis_atau_tujuan',
        'tindakan_rawatan',
        'dos_diberikan',
        'pegawai_pemeriksa',
        'jajahan',
        'lokasi_pemeriksaan',
        'catatan_dan_syor',
        'dokumen_lampiran',
        'didaftar_oleh',
    ];

    protected function casts(): array
    {
        return [
            'tarikh_rawatan' => 'date',
            'tarikh_ulangan_dos' => 'date',
            'berat_semasa_kg' => 'decimal:2',
            'suhu_badan_celsius' => 'decimal:1',
        ];
    }

    public function ternakan()
    {
        return $this->belongsTo(Ternakan::class);
    }

    public function pendaftar()
    {
        return $this->belongsTo(User::class, 'didaftar_oleh');
    }
}
