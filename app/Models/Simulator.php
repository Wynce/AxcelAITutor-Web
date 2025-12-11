<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Simulator extends Model
{
    protected $fillable = [
        'title', 'subject', 'description', 'embed_url', 
        'thumbnail', 'status', 'sort_order', 'created_by'
    ];

    const SUBJECTS = ['Physics', 'Chemistry', 'Biology', 'Mathematics', 'Mandarin'];
    const STATUSES = ['active', 'inactive', 'draft'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeBySubject($query, $subject)
    {
        return $query->where('subject', $subject);
    }
}