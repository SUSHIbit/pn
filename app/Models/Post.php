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
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        // Extract hashtags when saving a post
        static::saved(function ($post) {
            if ($post->wasChanged('content') || $post->wasChanged('title')) {
                $post->extractAndSyncHashtags();
            }
        });
    }

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
     * Get hashtags associated with this post
     */
    public function hashtags()
    {
        return $this->belongsToMany(Hashtag::class, 'post_hashtags')->withTimestamps();
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
     * Get content with hashtags converted to clickable links
     */
    public function getLinkedContentAttribute()
    {
        return Hashtag::linkify(nl2br(e($this->content)));
    }

    /**
     * Get title with hashtags converted to clickable links
     */
    public function getLinkedTitleAttribute()
    {
        return Hashtag::linkify(e($this->title));
    }

    /**
     * Extract hashtags from title and content and sync with database
     */
    public function extractAndSyncHashtags()
    {
        $text = $this->title . ' ' . $this->content;
        $hashtags = Hashtag::extractFromText($text);
        
        // Sync hashtags (this will attach new ones and detach removed ones)
        $this->hashtags()->sync(collect($hashtags)->pluck('id'));
    }

    /**
     * Get search-ready content (title + content combined)
     */
    public function getSearchableContentAttribute()
    {
        return $this->title . ' ' . $this->content;
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
     * Scope for posts by hashtag
     */
    public function scopeByHashtag($query, $hashtag)
    {
        return $query->whereHas('hashtags', function($q) use ($hashtag) {
            $q->where('name', $hashtag);
        });
    }

    /**
     * Scope for popular posts (most liked)
     */
    public function scopePopular($query, $days = 7)
    {
        return $query->where('published_at', '>=', now()->subDays($days))
                    ->withCount('likes')
                    ->having('likes_count', '>', 0)
                    ->orderBy('likes_count', 'desc');
    }

    /**
     * Scope for trending posts (most engagement recently)
     */
    public function scopeTrending($query, $days = 3)
    {
        return $query->where('published_at', '>=', now()->subDays($days))
                    ->withCount(['likes', 'comments'])
                    ->selectRaw('*, (likes_count * 2 + comments_count * 3) as engagement_score')
                    ->orderBy('engagement_score', 'desc');
    }

    /**
     * Scope for full-text search
     */
    public function scopeSearch($query, $searchTerm)
    {
        return $query->where(function($q) use ($searchTerm) {
            $q->where('title', 'LIKE', "%{$searchTerm}%")
              ->orWhere('content', 'LIKE', "%{$searchTerm}%")
              ->orWhereHas('hashtags', function($hq) use ($searchTerm) {
                  $hq->where('name', 'LIKE', "%{$searchTerm}%");
              })
              ->orWhereHas('user', function($uq) use ($searchTerm) {
                  $uq->where('name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('username', 'LIKE', "%{$searchTerm}%");
              });
        });
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