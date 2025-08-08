<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hashtag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get posts that use this hashtag
     */
    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_hashtags')->withTimestamps();
    }

    /**
     * Get published posts that use this hashtag
     */
    public function publishedPosts()
    {
        return $this->belongsToMany(Post::class, 'post_hashtags')
                    ->where('posts.is_published', true)
                    ->whereNotNull('posts.published_at')
                    ->where('posts.published_at', '<=', now())
                    ->withTimestamps();
    }

    /**
     * Get posts count for this hashtag
     */
    public function getPostsCountAttribute()
    {
        return $this->posts()
                   ->where('posts.is_published', true)
                   ->whereNotNull('posts.published_at')
                   ->where('posts.published_at', '<=', now())
                   ->count();
    }

    /**
     * Get the hashtag URL
     */
    public function getUrlAttribute()
    {
        return route('search.hashtag', $this->name);
    }

    /**
     * Get formatted hashtag name with #
     */
    public function getDisplayNameAttribute()
    {
        return "#{$this->name}";
    }

    /**
     * Create hashtag from text - extract and create hashtags
     */
    public static function extractFromText($text)
    {
        // Find all hashtags in text (e.g., #example, #news, etc.)
        preg_match_all('/#([a-zA-Z0-9_]+)/', $text, $matches);
        
        $hashtags = [];
        
        if (!empty($matches[1])) {
            foreach ($matches[1] as $hashtagName) {
                $hashtagName = strtolower($hashtagName);
                
                // Create or find existing hashtag
                $hashtag = self::firstOrCreate([
                    'name' => $hashtagName
                ], [
                    'slug' => \Illuminate\Support\Str::slug($hashtagName)
                ]);
                
                $hashtags[] = $hashtag;
            }
        }
        
        return $hashtags;
    }

    /**
     * Convert text with hashtags to clickable links
     */
    public static function linkify($text)
    {
        return preg_replace_callback(
            '/#([a-zA-Z0-9_]+)/',
            function ($matches) {
                $hashtag = $matches[1];
                $url = route('search.hashtag', strtolower($hashtag));
                return '<a href="' . $url . '" class="hashtag-link" style="color: var(--stone-700); font-weight: 600; text-decoration: none;">#' . $hashtag . '</a>';
            },
            $text
        );
    }

    /**
     * Scope for popular hashtags
     */
    public function scopePopular($query)
    {
        return $query->withCount(['posts' => function($q) {
                        $q->where('is_published', true)
                          ->whereNotNull('published_at')
                          ->where('published_at', '<=', now());
                    }])
                    ->orderBy('posts_count', 'desc');
    }

    /**
     * Scope for trending hashtags (popular in last week)
     */
    public function scopeTrending($query)
    {
        return $query->whereHas('posts', function($q) {
                        $q->where('published_at', '>=', now()->subDays(7))
                          ->where('is_published', true);
                    })
                    ->withCount(['posts' => function($q) {
                        $q->where('published_at', '>=', now()->subDays(7))
                          ->where('is_published', true);
                    }])
                    ->orderBy('posts_count', 'desc');
    }

    /**
     * Get related hashtags (used together with this one)
     */
    public function getRelatedHashtags($limit = 10)
    {
        // Get posts that use this hashtag
        $postIds = $this->posts()->pluck('posts.id');
        
        // Find other hashtags used in those posts
        return self::whereHas('posts', function($q) use ($postIds) {
                      $q->whereIn('posts.id', $postIds);
                  })
                  ->where('id', '!=', $this->id)
                  ->withCount('posts')
                  ->orderBy('posts_count', 'desc')
                  ->take($limit)
                  ->get();
    }
}