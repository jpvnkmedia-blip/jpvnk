<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoriPinjaman extends Model
{
    use HasFactory;

    protected $table = 'inventori_pinjaman';

    protected $fillable = [
        'inventori_item_id',
        'user_id',
        'kuantiti',
        'tujuan_pinjaman',
        'tarikh_pinjam',
        'tarikh_jangka_pulang',
        'tarikh_pulang_sebenar',
        'keadaan_semasa_pinjam',
        'keadaan_semasa_pulang',
        'status',
        'disahkan_oleh',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tarikh_pinjam' => 'date',
            'tarikh_jangka_pulang' => 'date',
            'tarikh_pulang_sebenar' => 'date',
        ];
    }

    public function item()
    {
        return $this->belongsTo(InventoriItem::class, 'inventori_item_id');
    }

    public function peminjam()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pengesah()
    {
        return $this->belongsTo(User::class, 'disahkan_oleh');
    }
}
