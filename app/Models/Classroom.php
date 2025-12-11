<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Classroom extends Model
{
    protected $fillable = [
        'teacher_id', 'name', 'description', 'invite_code',
        'subject', 'status', 'max_students'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($classroom) {
            if (empty($classroom->invite_code)) {
                $classroom->invite_code = self::generateUniqueCode();
            }
        });
    }

    public static function generateUniqueCode(): string
    {
        do {
            $code = strtoupper(Str::random(6));
        } while (self::where('invite_code', $code)->exists());
        
        return $code;
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'classroom_students')
            ->withPivot('status', 'joined_at')
            ->withTimestamps();
    }

    public function activeStudents()
    {
        return $this->students()->wherePivot('status', 'active');
    }
}