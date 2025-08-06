<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'image',
        'published_at',
        'is_published',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_published' => 'boolean',
    ];

    protected $dates = [
        'deleted_at',
        'published_at',
    ];

    /**
     * Get the user who created this post
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all comments for this post
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get all likes for this post
     */
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    /**
     * Get users who liked this post
     */
    public function likedByUsers()
    {
        return $this->belongsToMany(User::class, 'likes')->withTimestamps();
    }

    /**
     * Get the image URL for this post
     */
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('uploads/posts/' . $this->image);
        }
        return null;
    }

    /**
     * Get excerpt from content
     */
    public function getExcerptAttribute()
    {
        if (strlen($this->content) > 150) {
            return substr($this->content, 0, 150) . '...';
        }
        return $this->content;
    }

    /**
     * Get likes count
     */
    public function getLikesCountAttribute()
    {
        return $this->likes()->count();
    }

    /**
     * Get comments count
     */
    public function getCommentsCountAttribute()
    {
        return $this->comments()->count();
    }

    /**
     * Check if a user has liked this post
     */
    public function isLikedBy(User $user)
    {
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    /**
     * Scope for published posts
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true)
                    ->whereNotNull('published_at')
                    ->where('published_at', '<=', now());
    }

    /**
     * Scope for latest posts
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('published_at', 'desc');
    }

    /**
     * Scope for posts with engagement data
     */
    public function scopeWithEngagement($query)
    {
        return $query->withCount(['likes', 'comments']);
    }

    /**
     * Get formatted publish date
     */
    public function getFormattedPublishDateAttribute()
    {
        if ($this->published_at) {
            return $this->published_at->format('M j, Y g:i A');
        }
        return $this->created_at->format('M j, Y g:i A');
    }

    /**
     * Get time ago format
     */
    public function getTimeAgoAttribute()
    {
        $date = $this->published_at ?? $this->created_at;
        return $date->diffForHumans();
    }
}