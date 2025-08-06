<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'avatar',
        'bio',
        'username',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public static function generateUsername($name)
    {
        $username = strtolower(str_replace(' ', '', $name));
        $originalUsername = $username;
        $counter = 1;

        while (self::where('username', $username)->exists()) {
            $username = $originalUsername . $counter;
            $counter++;
        }

        return $username;
    }

    public function getDisplayNameAttribute()
    {
        return $this->name ?: $this->username;
    }

    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('uploads/avatars/' . $this->avatar);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->display_name) . '&background=78716c&color=fff&size=100';
    }
}