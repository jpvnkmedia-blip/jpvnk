<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoriPermohonan extends Model
{
    use HasFactory;

    protected $table = 'inventori_permohonan';

    protected $fillable = [
        'no_permohonan',
        'user_id',
        'inventori_item_id',
        'jenis_stor',
        'kuantiti_dimohon',
        'kuantiti_diluluskan',
        'unit_bahagian',
        'tujuan_permohonan',
        'tarikh_diperlukan',
        'status',
        'catatan_pemohon',
        'catatan_pegawai',
        'disahkan_oleh',
        'tarikh_kelulusan',
    ];

    protected $casts = [
        'tarikh_diperlukan' => 'date',
        'tarikh_kelulusan' => 'datetime',
        'kuantiti_dimohon' => 'integer',
        'kuantiti_diluluskan' => 'integer',
    ];

    // Hubungan
    public function pemohon()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function item()
    {
        return $this->belongsTo(InventoriItem::class, 'inventori_item_id');
    }

    public function pelulus()
    {
        return $this->belongsTo(User::class, 'disahkan_oleh');
    }

    // Helper Methods
    public function isPending(): bool
    {
        return $this->status === 'Menunggu Kelulusan';
    }

    public function isApproved(): bool
    {
        return $this->status === 'Diluluskan';
    }

    public function isRejected(): bool
    {
        return $this->status === 'Ditolak';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'Telah Diambil / Diserahkan';
    }

    public function isStorPejabat(): bool
    {
        return $this->jenis_stor === 'pejabat';
    }

    public function isStorUbat(): bool
    {
        return $this->jenis_stor === 'ubat';
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'Menunggu Kelulusan' => 'bg-amber-100 text-amber-800 border-amber-200',
            'Diluluskan' => 'bg-blue-100 text-blue-800 border-blue-200',
            'Telah Diambil / Diserahkan' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'Ditolak' => 'bg-rose-100 text-rose-800 border-rose-200',
            default => 'bg-slate-100 text-slate-800 border-slate-200',
        };
    }
}
