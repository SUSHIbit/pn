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