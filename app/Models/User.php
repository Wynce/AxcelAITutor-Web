<?php

namespace App\Models;
use Illuminate\Contracts\Auth\MustVerifyEmail;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use DB;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        "first_name",
        "last_name",
        "birth_year",
        "country",
        'image',
        'is_deleted',
        'status',
        'email_verified_at',
        'login_type',
        'bot_id',
        'is_first_login',
        'last_active_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
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

    /**
     * Get chat history for this user
     */
    public function chatHistory()
    {
        return $this->hasMany(ChatHistory::class);
    }

    /**
     * Get full name attribute
     */
    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Scope for active users
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')->where('is_deleted', false);
    }

    /**
     * Scope for non-deleted users
     */
    public function scopeNotDeleted($query)
    {
        return $query->where('is_deleted', false);
    }

    /**
     * Scope for filtering by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public static function get_users_count(){
        $users = DB::table('users')
                 ->where('is_deleted','!=',1)
                 ->where('status','=','active')
                 ->get();

        $users_count = count($users);
        return $users_count;
    } 
}
