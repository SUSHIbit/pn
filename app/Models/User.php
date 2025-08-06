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

    /**
     * Get all posts for this user
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Get published posts for this user
     */
    public function publishedPosts()
    {
        return $this->hasMany(Post::class)->where('is_published', true)->latest('published_at');
    }

    /**
     * Get all comments by this user
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get all likes by this user
     */
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    /**
     * Get posts liked by this user
     */
    public function likedPosts()
    {
        return $this->belongsToMany(Post::class, 'likes')->withTimestamps();
    }

    /**
     * Get users this user is following
     */
    public function following()
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'following_id')->withTimestamps();
    }

    /**
     * Get users following this user
     */
    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'following_id', 'follower_id')->withTimestamps();
    }

    /**
     * Check if this user is following another user
     */
    public function isFollowing(User $user)
    {
        return $this->following()->where('following_id', $user->id)->exists();
    }

    /**
     * Check if this user is followed by another user
     */
    public function isFollowedBy(User $user)
    {
        return $this->followers()->where('follower_id', $user->id)->exists();
    }

    /**
     * Follow another user
     */
    public function follow(User $user)
    {
        if ($this->id === $user->id) {
            return false; // Can't follow yourself
        }

        return $this->following()->syncWithoutDetaching([$user->id]);
    }

    /**
     * Unfollow another user
     */
    public function unfollow(User $user)
    {
        return $this->following()->detach($user->id);
    }

    /**
     * Check if user has liked a post
     */
    public function hasLiked(Post $post)
    {
        return $this->likes()->where('post_id', $post->id)->exists();
    }

    /**
     * Like a post
     */
    public function like(Post $post)
    {
        return $this->likes()->firstOrCreate(['post_id' => $post->id]);
    }

    /**
     * Unlike a post
     */
    public function unlike(Post $post)
    {
        return $this->likes()->where('post_id', $post->id)->delete();
    }

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

    /**
     * Get posts count for this user
     */
    public function getPostsCountAttribute()
    {
        return $this->posts()->where('is_published', true)->count();
    }

    /**
     * Get followers count
     */
    public function getFollowersCountAttribute()
    {
        return $this->followers()->count();
    }

    /**
     * Get following count
     */
    public function getFollowingCountAttribute()
    {
        return $this->following()->count();
    }

    /**
     * Get feed posts (posts from users this user follows + own posts)
     */
    public function feedPosts()
    {
        $followingIds = $this->following()->pluck('users.id')->toArray();
        $followingIds[] = $this->id; // Include own posts

        return Post::whereIn('user_id', $followingIds)
                  ->where('is_published', true)
                  ->whereNotNull('published_at')
                  ->where('published_at', '<=', now())
                  ->with(['user', 'likes', 'comments'])
                  ->latest('published_at');
    }
}