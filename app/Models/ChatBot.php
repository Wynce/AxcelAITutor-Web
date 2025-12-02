<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatBot extends Model
{
    use HasFactory;

    protected $table 	= 'chatbots';
    protected $fillable = [
        'bot_name', 'bot_type', 'base_bot', 'prompt', 'knowledge_base', 'greeting_message', 
        'bot_bio', 'public_access', 'bot_recommendations', 'show_prompt', 'bot_profile','is_default'
    ];
}
