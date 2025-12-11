<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use DB;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    const TYPE_STUDENT = 'student';
    const TYPE_PARENT = 'parent';
    const TYPE_TEACHER = 'teacher';

    const TYPES = [
        self::TYPE_STUDENT,
        self::TYPE_PARENT,
        self::TYPE_TEACHER,
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'first_name',
        'last_name',
        'birth_year',
        'country',
        'image',
        'is_deleted',
        'status',
        'email_verified_at',
        'login_type',
        'bot_id',
        'is_first_login',
        'last_active_at',
        'user_type',
        'parent_id',
        'school_name',
        'grade_level',
        'subjects_teaching',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_active_at' => 'datetime',
            'password' => 'hashed',
            'is_deleted' => 'boolean',
            'is_first_login' => 'boolean',
        ];
    }

    // ========== RELATIONSHIPS ==========

    /**
     * Chat history for this user
     */
    public function chatHistory()
    {
        return $this->hasMany(ChatHistory::class);
    }

    /**
     * Analytics for this user
     */
    public function analytics()
    {
        return $this->hasMany(UserAnalytics::class);
    }

    /**
     * Parent of this student
     */
    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    /**
     * Children (students) of this parent
     */
    public function children()
    {
        return $this->hasMany(User::class, 'parent_id');
    }

    /**
     * Teachers of this student (many-to-many)
     */
    public function teachers()
    {
        return $this->belongsToMany(User::class, 'student_teacher', 'student_id', 'teacher_id')
            ->withPivot('classroom_id', 'subject', 'assigned_at')
            ->withTimestamps();
    }

    /**
     * Students of this teacher (many-to-many)
     */
    public function students()
    {
        return $this->belongsToMany(User::class, 'student_teacher', 'teacher_id', 'student_id')
            ->withPivot('classroom_id', 'subject', 'assigned_at')
            ->withTimestamps();
    }

    /**
     * Classrooms this user belongs to (for students)
     */
    public function classrooms()
    {
        return $this->belongsToMany(Classroom::class, 'classroom_students', 'user_id', 'classroom_id')
            ->withPivot('joined_at')
            ->withTimestamps();
    }

    /**
     * Classrooms owned by this teacher
     */
    public function ownedClassrooms()
    {
        return $this->hasMany(Classroom::class, 'teacher_id');
    }

    // ========== ACCESSORS ==========

    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function getSubjectsTeachingArrayAttribute()
    {
        return $this->subjects_teaching ? explode(',', $this->subjects_teaching) : [];
    }

    // ========== SCOPES ==========

    public function scopeActive($query)
    {
        return $query->where('status', 'active')->where('is_deleted', false);
    }

    public function scopeNotDeleted($query)
    {
        return $query->where('is_deleted', false);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('user_type', $type);
    }

    public function scopeStudents($query)
    {
        return $query->where('user_type', self::TYPE_STUDENT);
    }

    public function scopeParents($query)
    {
        return $query->where('user_type', self::TYPE_PARENT);
    }

    public function scopeTeachers($query)
    {
        return $query->where('user_type', self::TYPE_TEACHER);
    }

    // ========== HELPER METHODS ==========

    public function isStudent(): bool
    {
        return $this->user_type === self::TYPE_STUDENT;
    }

    public function isParent(): bool
    {
        return $this->user_type === self::TYPE_PARENT;
    }

    public function isTeacher(): bool
    {
        return $this->user_type === self::TYPE_TEACHER;
    }

    public function getTotalChats(): int
    {
        return $this->chatHistory()->where('is_deleted', 0)->count();
    }

    public function getAnalyticsSummary($days = 30): array
    {
        $analytics = $this->analytics()
            ->where('date', '>=', now()->subDays($days))
            ->get();

        return [
            'total_chats' => $analytics->sum('chat_count'),
            'total_simulator_views' => $analytics->sum('simulator_views'),
            'total_quiz_attempts' => $analytics->sum('quiz_attempts'),
            'total_quiz_correct' => $analytics->sum('quiz_correct'),
            'total_time_minutes' => $analytics->sum('time_spent_minutes'),
            'quiz_accuracy' => $analytics->sum('quiz_attempts') > 0 
                ? round(($analytics->sum('quiz_correct') / $analytics->sum('quiz_attempts')) * 100, 1) 
                : 0,
        ];
    }

    public static function get_users_count()
    {
        return self::notDeleted()->active()->count();
    }

    public static function getCountByType($type)
    {
        return self::notDeleted()->where('user_type', $type)->count();
    }
}