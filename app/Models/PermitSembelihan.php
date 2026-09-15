<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermitSembelihan extends Model
{
    use HasFactory;

    protected $table = 'permit_sembelihan';

    protected $fillable = [
        'no_permit',
        'no_rujukan_skv',
        'no_rujukan_karkas',
        'pemunya_id',
        'jenis_ternakan',
        'ternakan_id',
        'tujuan_sembelih',
        'is_musim_korban',
        'hari_korban_percuma',
        'tarikh_sembelih',
        'tarikh_mula',
        'tarikh_tamat',
        'no_kenderaan',
        'lokasi_sembelih',
        'nama_premis_sembelih',
        'alamat_premis_sembelih',
        'alamat_1',
        'kuantiti_karkas_1',
        'alamat_2',
        'kuantiti_karkas_2',
        'alamat_3',
        'kuantiti_karkas_3',
        'senarai_ternakan',
        'no_resit_bayaran',
        'resit_pembayaran',
        'kadar_bayaran',
        'status_kelulusan',
        'diluluskan_oleh',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tarikh_sembelih' => 'date',
            'tarikh_mula' => 'date',
            'tarikh_tamat' => 'date',
            'is_musim_korban' => 'boolean',
            'senarai_ternakan' => 'array',
            'kadar_bayaran' => 'decimal:2',
        ];
    }

    public function pemunya()
    {
        return $this->belongsTo(Pemunya::class);
    }

    public function ternakan()
    {
        return $this->belongsTo(Ternakan::class);
    }

    public function pelulus()
    {
        return $this->belongsTo(User::class, 'diluluskan_oleh');
    }

    /**
     * Dapatkan senarai ternakan SKV (maksimum 7 baris untuk biasa, 10 baris untuk musim korban)
     */
    public function getSenaraiTernakanListAttribute()
    {
        if (!empty($this->senarai_ternakan) && is_array($this->senarai_ternakan)) {
            return collect($this->senarai_ternakan);
        }

        if ($this->ternakan) {
            return collect([[
                'ternakan_id' => $this->ternakan_id,
                'jantina' => $this->ternakan->jantina === 'Jantan' ? 'J' : 'B',
                'no_id_ternakan' => $this->ternakan->no_tag ?? 'ID-' . $this->ternakan->id,
                'no_siri_kad_pendaftaran' => $this->ternakan->no_siri_kad_kuning ?? '-',
                'tarikh_sembelihan' => $this->tarikh_sembelih ? $this->tarikh_sembelih->format('Y-m-d') : date('Y-m-d'),
                'tempat_sembelihan' => $this->lokasi_sembelih ?? ($this->alamat_premis_sembelih ?? 'Rumah Sembelih'),
                'no_kn_haiwan_16' => '16/' . date('Y') . '/' . str_pad($this->id, 4, '0', STR_PAD_LEFT),
                'kuantiti_karkas' => '1 Ekor',
            ]]);
        }

        return collect();
    }

    /**
     * Had baris maksimum yang dibenarkan (7 untuk biasa, 10 untuk Hari Raya Korban)
     */
    public function getMaxRowsAttribute(): int
    {
        return $this->is_musim_korban ? 10 : 7;
    }

    /**
     * Semak sama ada tempoh sah laku 7 hari telah tamat
     */
    public function isExpired(): bool
    {
        if (!$this->tarikh_tamat) {
            return false;
        }
        return \Carbon\Carbon::now()->startOfDay()->gt($this->tarikh_tamat);
    }
}
