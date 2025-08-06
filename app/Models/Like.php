<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'post_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who liked the post
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the post that was liked
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Scope for recent likes
     */
    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}