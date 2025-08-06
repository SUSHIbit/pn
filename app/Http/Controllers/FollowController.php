<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Post;
use App\Models\Follow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Toggle follow/unfollow a user
     */
    public function toggle(User $user)
    {
        $currentUser = Auth::user();

        // Can't follow yourself
        if ($currentUser->id === $user->id) {
            return response()->json(['error' => 'You cannot follow yourself'], 422);
        }

        // Check if already following - query Follow table directly
        $existingFollow = Follow::where('follower_id', $currentUser->id)
                               ->where('following_id', $user->id)
                               ->first();

        if ($existingFollow) {
            // Unfollow the user - delete follow record
            $existingFollow->delete();
            $following = false;
            $message = 'Unfollowed ' . $user->name;
        } else {
            // Follow the user - create follow record
            Follow::create([
                'follower_id' => $currentUser->id,
                'following_id' => $user->id,
            ]);
            $following = true;
            $message = 'Following ' . $user->name;
        }

        // Get updated counts - count directly from Follow table
        $followersCount = Follow::where('following_id', $user->id)->count();
        $followingCount = Follow::where('follower_id', $user->id)->count();

        return response()->json([
            'following' => $following,
            'followers_count' => $followersCount,
            'following_count' => $followingCount,
            'message' => $message
        ]);
    }

    /**
     * Get users that the current user is following
     */
    public function following()
    {
        // Get following user IDs
        $followingIds = Follow::where('follower_id', Auth::id())
                             ->pluck('following_id')
                             ->toArray();

        // Get the users with counts
        $users = User::whereIn('id', $followingIds)
                    ->withCount(['posts' => function($query) {
                        $query->where('is_published', true);
                    }])
                    ->paginate(20);

        // Add followers/following counts manually
        foreach ($users as $user) {
            $user->followers_count = Follow::where('following_id', $user->id)->count();
            $user->following_count = Follow::where('follower_id', $user->id)->count();
        }

        return view('users.following', compact('users'));
    }

    /**
     * Get users that follow the current user
     */
    public function followers()
    {
        // Get follower user IDs
        $followerIds = Follow::where('following_id', Auth::id())
                            ->pluck('follower_id')
                            ->toArray();

        // Get the users with counts
        $users = User::whereIn('id', $followerIds)
                    ->withCount(['posts' => function($query) {
                        $query->where('is_published', true);
                    }])
                    ->paginate(20);

        // Add followers/following counts manually
        foreach ($users as $user) {
            $user->followers_count = Follow::where('following_id', $user->id)->count();
            $user->following_count = Follow::where('follower_id', $user->id)->count();
        }

        return view('users.followers', compact('users'));
    }

    /**
     * Get suggested users to follow
     */
    public function suggestions()
    {
        $currentUser = Auth::user();
        
        // Get users current user is already following
        $followingIds = Follow::where('follower_id', $currentUser->id)
                             ->pluck('following_id')
                             ->toArray();
        $followingIds[] = $currentUser->id; // Exclude self

        $suggestions = User::whereNotIn('id', $followingIds)
                          ->withCount(['posts' => function($query) {
                              $query->where('is_published', true);
                          }])
                          ->having('posts_count', '>', 0)
                          ->orderBy('posts_count', 'desc')
                          ->limit(10)
                          ->get();

        // Add followers count manually
        foreach ($suggestions as $user) {
            $user->followers_count = Follow::where('following_id', $user->id)->count();
        }

        return view('users.suggestions', compact('suggestions'));
    }

    /**
     * Get personalized feed based on following
     */
    public function feed()
    {
        $currentUser = Auth::user();
        
        // Get users current user is following
        $followingIds = Follow::where('follower_id', $currentUser->id)
                             ->pluck('following_id')
                             ->toArray();
        $followingIds[] = $currentUser->id; // Include own posts

        $posts = Post::whereIn('user_id', $followingIds)
                    ->where('is_published', true)
                    ->whereNotNull('published_at')
                    ->where('published_at', '<=', now())
                    ->with(['user'])
                    ->withCount(['likes', 'comments'])
                    ->orderBy('published_at', 'desc')
                    ->paginate(10);

        return view('posts.feed', compact('posts'));
    }
}