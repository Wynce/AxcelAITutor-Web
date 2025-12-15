<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ChatHistory extends Model
{
    protected $table = 'user_chat_history';
    
    protected $fillable = [
        'user_id', 'bot_id', 'subject', 
        'user_message', 'bot_response', 'tokens_used'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function bot()
    {
        return $this->belongsTo(Bot::class);
    }
}