<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AxcelHack extends Model
{
    protected $fillable = [
        'user_id', 'title', 'description', 'project_url', 'demo_url',
        'thumbnail', 'tags', 'user_type', 'status', 'rejection_reason',
        'reviewed_by', 'reviewed_at', 'likes_count', 'views_count'
    ];

    protected $casts = [
        'tags' => 'array',
        'reviewed_at' => 'datetime',
    ];

    const STATUSES = ['pending', 'approved', 'rejected', 'featured'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->whereIn('status', ['approved', 'featured']);
    }
}