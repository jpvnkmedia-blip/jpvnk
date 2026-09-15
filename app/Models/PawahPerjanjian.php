<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PawahPerjanjian extends Model
{
    use HasFactory;

    protected $table = 'pawah_perjanjian';

    protected $fillable = [
        'user_id',
        'no_perjanjian',
        'nama_program',
        'jenis_pawah',
        'jenis_ternakan_sedia_ada',
        'bilangan_ternakan_sedia_ada',
        'tarikh_mula',
        'tarikh_tamat',
        'tempoh_tahun',
        'bilangan_induk',
        'syarat_pemulangan',
        'jajahan',
        'status',
        'pegawai_penyelia',
        'catatan',
        'dokumen_perjanjian',
    ];

    protected function casts(): array
    {
        return [
            'tarikh_mula' => 'date',
            'tarikh_tamat' => 'date',
        ];
    }

    public function peserta()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pawahTernakan()
    {
        return $this->hasMany(PawahTernakan::class);
    }

    public function ternakanList()
    {
        return $this->belongsToMany(Ternakan::class, 'pawah_ternakan', 'pawah_perjanjian_id', 'ternakan_id')
                    ->withPivot('id', 'status_induk', 'tarikh_serahan', 'catatan')
                    ->withTimestamps();
    }

    public function rekodKelahiran()
    {
        return $this->hasMany(PawahRekodKelahiran::class);
    }

    public function rekodKesihatan()
    {
        return $this->hasMany(PawahRekodKesihatan::class);
    }

    public function penyelesaian()
    {
        return $this->hasOne(PawahPenyelesaian::class);
    }

    /**
     * Dapatkan senarai ternakan pemohon/peserta yang didaftarkan di dalam sistem EPTR (Kad Kuning)
     */
    public function getTernakanPemohonEptrAttribute()
    {
        if (!$this->peserta) {
            return collect();
        }

        $pemunyaIds = Pemunya::where('user_id', $this->peserta->id)
            ->orWhere(function ($q) {
                if (!empty($this->peserta->ic_number)) {
                    $q->where('no_kp', $this->peserta->ic_number);
                }
            })
            ->pluck('id');

        if ($pemunyaIds->isEmpty()) {
            return collect();
        }

        return Ternakan::whereIn('pemunya_id', $pemunyaIds)
            ->with('pemunya')
            ->latest()
            ->get();
    }

    /**
     * Dapatkan maklumat terperinci permohonan, ternakan sedia ada, dan fasiliti tapak
     */
    public function getButiranPermohonanAttribute(): array
    {
        $catatan = (string)$this->catatan;
        $isPermohonanAwam = str_contains($this->nama_program, 'Permohonan Awam') || str_contains($this->no_perjanjian, 'PW-MOHON');

        $data = [
            'pengalaman' => null,
            'jenis_ternakan_sedia_ada' => $this->jenis_ternakan_sedia_ada,
            'bilangan_ternakan_sedia_ada' => $this->bilangan_ternakan_sedia_ada ?? 0,
            'keluasan_ragut' => null,
            'jenis_kandang' => null,
            'sumber_makanan' => null,
            'catatan_tambahan' => null,
            'is_permohonan_awam' => $isPermohonanAwam,
        ];

        if (preg_match('/Pengalaman Menternak:\s*([^|]+)/i', $catatan, $m)) {
            $data['pengalaman'] = trim($m[1]);
        }
        if (preg_match('/Keluasan Padang Ragut:\s*([^|]+)/i', $catatan, $m)) {
            $data['keluasan_ragut'] = trim($m[1]);
        }
        if (preg_match('/Jenis Kandang:\s*([^|]+)/i', $catatan, $m)) {
            $data['jenis_kandang'] = trim($m[1]);
        }
        if (preg_match('/Sumber Makanan:\s*([^|]+)/i', $catatan, $m)) {
            $data['sumber_makanan'] = trim($m[1]);
        }
        if (preg_match('/Catatan Tambahan:\s*(.+)$/i', $catatan, $m)) {
            $data['catatan_tambahan'] = trim($m[1]);
        } elseif (!$isPermohonanAwam && !empty($catatan) && !str_contains($catatan, '|')) {
            $data['catatan_tambahan'] = $catatan;
        }

        return $data;
    }
}
