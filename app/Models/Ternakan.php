<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ternakan extends Model
{
    use HasFactory;

    protected $table = 'ternakan';

    protected $fillable = [
        'pemunya_id',
        'no_tag',
        'jenis_ternakan',
        'baka',
        'baka_pejantan',
        'baka_induk',
        'no_tanda_pengenalan_induk',
        'jantina',
        'umur',
        'tarikh_lahir',
        'warna',
        'tanda_badan',
        'tujuan_ternakan',
        'lokasi_kandang',
        'jajahan',
        'daerah',
        'poskod',
        'program',
        'status',
        'status_kelulusan',
        'tarikh_daftar',
        'no_siri_kad_kuning',
        'qr_code',
        'resit_pembayaran',
        'catatan',
        'didaftar_oleh',
        'diluluskan_oleh',
        'tarikh_kelulusan',
    ];

    protected function casts(): array
    {
        return [
            'tarikh_lahir' => 'date',
            'tarikh_daftar' => 'date',
            'tarikh_kelulusan' => 'datetime',
        ];
    }

    public function pemunya()
    {
        return $this->belongsTo(Pemunya::class);
    }

    public function pawahTernakan()
    {
        return $this->hasOne(PawahTernakan::class);
    }

    public function pawahPerjanjian()
    {
        return $this->hasOneThrough(
            PawahPerjanjian::class,
            PawahTernakan::class,
            'ternakan_id',
            'id',
            'id',
            'pawah_perjanjian_id'
        );
    }

    public function rekodKelahiranPawah()
    {
        return $this->hasMany(PawahRekodKelahiran::class, 'ternakan_induk_id');
    }

    public function kelahiranAnak()
    {
        return $this->hasMany(RekodKelahiran::class, 'induk_id');
    }

    public function rekodKelahiranSendiri()
    {
        return $this->hasOne(RekodKelahiran::class, 'anak_ternakan_id');
    }

    public function programKesihatan()
    {
        return $this->hasMany(ProgramKesihatan::class, 'ternakan_id')->orderBy('tarikh_rawatan', 'desc');
    }

    public function pindahMilik()
    {
        return $this->hasMany(PindahMilik::class);
    }

    public function pembatalan()
    {
        return $this->hasMany(PembatalanTernakan::class);
    }

    public function pembatalanTernakan()
    {
        return $this->hasMany(PembatalanTernakan::class);
    }

    public function permitSembelihan()
    {
        return $this->hasMany(PermitSembelihan::class);
    }

    public function pendaftar()
    {
        return $this->belongsTo(User::class, 'didaftar_oleh');
    }

    /**
     * Semak sama ada ternakan telah dibatalkan, mati, atau disembelih
     */
    public function isDibatalkanAtauMatiAtauSembelih(): bool
    {
        $inactiveStatuses = ['batal', 'mati', 'sembelih', 'disembelih', 'tidak aktif', 'pindah', 'ditolak'];
        if (in_array(strtolower((string)$this->status), $inactiveStatuses)) {
            return true;
        }

        if ($this->status_kelulusan === 'Ditolak') {
            return true;
        }

        // Semak rekod pembatalan (Borang C) yang telah disahkan
        if ($this->pembatalanTernakan()->where('status_kelulusan', 'Disahkan')->exists()) {
            return true;
        }

        // Semak rekod permit sembelihan (Borang D) yang telah diluluskan
        if ($this->permitSembelihan()->where('status_kelulusan', 'Diluluskan')->exists()) {
            return true;
        }

        return false;
    }

    /**
     * Semak sama ada ternakan aktif dan boleh menerima rawatan / perubahan
     */
    public function canPerformAction(): bool
    {
        return !$this->isDibatalkanAtauMatiAtauSembelih() && $this->status_kelulusan === 'Diluluskan';
    }

    /**
     * Pemunya Asal Ternakan semasa pendaftaran pertama kali
     */
    public function getPemunyaAsalAttribute()
    {
        $pindahTerawal = $this->pindahMilik()
            ->with('pemunyaAsal')
            ->orderBy('tarikh_pindah', 'asc')
            ->orderBy('id', 'asc')
            ->first();

        if ($pindahTerawal && $pindahTerawal->pemunyaAsal) {
            return $pindahTerawal->pemunyaAsal;
        }

        return $this->pemunya;
    }

    /**
     * Senarai rekod pindah milik yang telah diluluskan, disusun pemunya terbaru di atas sekali (latest first)
     */
    public function getSenaraiPindahMilikTerkiniAttribute()
    {
        return $this->pindahMilik()
            ->with(['pemunyaAsal', 'pemunyaBaru', 'pelulus'])
            ->where('status_kelulusan', 'Diluluskan')
            ->orderBy('tarikh_pindah', 'desc')
            ->orderBy('id', 'desc')
            ->get();
    }
}
