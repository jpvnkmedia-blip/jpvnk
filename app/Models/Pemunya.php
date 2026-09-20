<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemunya extends Model
{
    use HasFactory;

    protected $table = 'pemunya';

    protected $fillable = [
        'user_id',
        'nama',
        'no_kp',
        'no_telefon',
        'alamat',
        'jajahan',
        'daerah',
        'poskod',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ternakan()
    {
        return $this->hasMany(Ternakan::class);
    }

    public function permitSembelihan()
    {
        return $this->hasMany(PermitSembelihan::class);
    }

    public function naimbifPermohonan()
    {
        return $this->hasMany(NaimbifPermohonan::class);
    }
}
