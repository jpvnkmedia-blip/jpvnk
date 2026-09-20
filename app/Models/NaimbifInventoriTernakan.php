<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NaimbifInventoriTernakan extends Model
{
    use HasFactory;

    protected $table = 'naimbif_inventori_ternakan';

    protected $fillable = [
        'naimbif_permohonan_id',
        'baka',
        'nama_baka_lain',
        'betina_anak',
        'betina_dara',
        'betina_induk',
        'jantan_anak',
        'jantan_pejantan',
        'jumlah_baka',
    ];

    protected $casts = [
        'betina_anak' => 'integer',
        'betina_dara' => 'integer',
        'betina_induk' => 'integer',
        'jantan_anak' => 'integer',
        'jantan_pejantan' => 'integer',
        'jumlah_baka' => 'integer',
    ];

    public function permohonan(): BelongsTo
    {
        return $this->belongsTo(NaimbifPermohonan::class, 'naimbif_permohonan_id');
    }

    public function getBetinaTotalAttribute(): int
    {
        return $this->betina_anak + $this->betina_dara + $this->betina_induk;
    }

    public function getJantanTotalAttribute(): int
    {
        return $this->jantan_anak + $this->jantan_pejantan;
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->baka === 'LAIN-LAIN' && !empty($this->nama_baka_lain)) {
            return 'LAIN-LAIN (' . $this->nama_baka_lain . ')';
        }
        return $this->baka;
    }
}
