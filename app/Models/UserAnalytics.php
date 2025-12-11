<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAnalytics extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'chat_count',
        'simulator_views',
        'quiz_attempts',
        'quiz_correct',
        'time_spent_minutes',
        'subjects_studied',
    ];

    protected $casts = [
        'date' => 'date',
        'subjects_studied' => 'array',
    ];

    /**
     * User relationship
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get or create today's analytics for a user
     */
    public static function getOrCreateToday($userId)
    {
        return self::firstOrCreate(
            ['user_id' => $userId, 'date' => now()->toDateString()],
            [
                'chat_count' => 0,
                'simulator_views' => 0,
                'quiz_attempts' => 0,
                'quiz_correct' => 0,
                'time_spent_minutes' => 0,
                'subjects_studied' => [],
            ]
        );
    }

    /**
     * Increment chat count
     */
    public function incrementChats($count = 1)
    {
        $this->increment('chat_count', $count);
    }

    /**
     * Increment simulator views
     */
    public function incrementSimulatorViews($count = 1)
    {
        $this->increment('simulator_views', $count);
    }

    /**
     * Record quiz attempt
     */
    public function recordQuizAttempt($correct = false)
    {
        $this->increment('quiz_attempts');
        if ($correct) {
            $this->increment('quiz_correct');
        }
    }

    /**
     * Add time spent
     */
    public function addTimeSpent($minutes)
    {
        $this->increment('time_spent_minutes', $minutes);
    }

    /**
     * Add subject studied time
     */
    public function addSubjectStudied($subject, $minutes)
    {
        $subjects = $this->subjects_studied ?? [];
        $subjects[$subject] = ($subjects[$subject] ?? 0) + $minutes;
        $this->update(['subjects_studied' => $subjects]);
    }

    /**
     * Scope for date range
     */
    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('date', [$from, $to]);
    }

    /**
     * Get aggregate stats for a user
     */
    public static function getAggregateStats($userId, $days = 30)
    {
        return self::where('user_id', $userId)
            ->where('date', '>=', now()->subDays($days))
            ->selectRaw('
                SUM(chat_count) as total_chats,
                SUM(simulator_views) as total_simulator_views,
                SUM(quiz_attempts) as total_quiz_attempts,
                SUM(quiz_correct) as total_quiz_correct,
                SUM(time_spent_minutes) as total_time_minutes,
                COUNT(*) as active_days
            ')
            ->first();
    }
}