<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    use HasFactory;

    // Specify the table name
    protected $table = 'settings';

    // Define the fillable columns
    protected $fillable = [
        'logo', 
        'name', 
        'base_bot', 
        'prompt', 
        'knowledge_base', 
        'greeting_message', 
        'suggest_replies', 
        'bio', 
        'public_access'
    ];
}
