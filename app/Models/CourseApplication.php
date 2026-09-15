<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'user_id',
        'registration_number',
        'status',
        'certificate_number',
        'certificate_issued_at',
        'rejection_reason',
        'feedback',
        'rating',
    ];

    protected function casts(): array
    {
        return [
            'certificate_issued_at' => 'date',
        ];
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
