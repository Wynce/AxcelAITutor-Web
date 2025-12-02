<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatHistory extends Model
{
    use HasFactory;
    
    protected $table = "user_chat_history";

    protected $fillable = ['id','user_id', 'chat_id','user_message', 'response', 'created_at','updated_at','is_deleted','image_path','selected_bot_id'];

}
