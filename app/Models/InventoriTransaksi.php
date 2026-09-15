<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoriTransaksi extends Model
{
    use HasFactory;

    protected $table = 'inventori_transaksi';

    protected $fillable = [
        'inventori_item_id',
        'jenis_transaksi',
        'kuantiti',
        'penerima_atau_pembekal',
        'rujukan_dokumen',
        'baki_selepas',
        'dikendalikan_oleh',
        'catatan',
    ];

    public function item()
    {
        return $this->belongsTo(InventoriItem::class, 'inventori_item_id');
    }

    public function pengendali()
    {
        return $this->belongsTo(User::class, 'dikendalikan_oleh');
    }
}
