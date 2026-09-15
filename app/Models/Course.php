<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'code',
        'category',
        'description',
        'trainer_name',
        'start_date',
        'end_date',
        'time',
        'location',
        'jajahan',
        'capacity',
        'registered_count',
        'fee',
        'status',
        'cover_image',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'fee' => 'decimal:2',
        ];
    }

    public function applications()
    {
        return $this->hasMany(CourseApplication::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
