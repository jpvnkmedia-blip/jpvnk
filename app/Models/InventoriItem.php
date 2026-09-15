<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoriItem extends Model
{
    use HasFactory;

    protected $table = 'inventori_items';

    protected $fillable = [
        'kod_item',
        'nama_item',
        'jenis_stor',
        'kategori',
        'unit',
        'kuantiti_semasa',
        'kuantiti_minimum',
        'harga_seunit',
        'no_batch',
        'tarikh_luput',
        'suhu_simpanan',
        'pembekal_utama',
        'lokasi_rak',
        'jajahan',
        'status',
        'deskripsi',
    ];

    protected function casts(): array
    {
        return [
            'harga_seunit' => 'decimal:2',
            'tarikh_luput' => 'date',
        ];
    }

    public function transaksi()
    {
        return $this->hasMany(InventoriTransaksi::class);
    }

    public function pinjaman()
    {
        return $this->hasMany(InventoriPinjaman::class);
    }

    public function permohonan()
    {
        return $this->hasMany(InventoriPermohonan::class);
    }

    public function isStorUbat(): bool
    {
        return $this->jenis_stor === 'ubat';
    }

    public function isStorPejabat(): bool
    {
        return $this->jenis_stor === 'pejabat';
    }

    public function isExpired(): bool
    {
        return $this->tarikh_luput && $this->tarikh_luput->isPast();
    }

    public function isExpiringSoon(int $days = 60): bool
    {
        return $this->tarikh_luput && !$this->isExpired() && $this->tarikh_luput->diffInDays(now()) <= $days;
    }

    public function updateStatusStock(): void
    {
        if ($this->isExpired()) {
            $this->status = 'Luput';
        } elseif ($this->kuantiti_semasa <= 0) {
            $this->status = 'Habis Stok';
        } elseif ($this->kuantiti_semasa <= $this->kuantiti_minimum) {
            $this->status = 'Stok Rendah';
        } else {
            $this->status = 'Mencukupi';
        }
        $this->save();
    }
}
