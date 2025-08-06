<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Toggle like on a post
     */
    public function toggle(Post $post)
    {
        $user = Auth::user();

        // Check if post exists and is published (or user owns it)
        if (!$post->is_published && $post->user_id !== $user->id) {
            return response()->json(['error' => 'Post not found'], 404);
        }

        // Check if user already liked this post - query Like table directly
        $existingLike = Like::where('user_id', $user->id)
                           ->where('post_id', $post->id)
                           ->first();

        if ($existingLike) {
            // Unlike the post - delete the like record
            $existingLike->delete();
            $liked = false;
        } else {
            // Like the post - create new like record
            Like::create([
                'user_id' => $user->id,
                'post_id' => $post->id,
            ]);
            $liked = true;
        }

        // Get updated like count - count directly from Like table
        $likesCount = Like::where('post_id', $post->id)->count();

        return response()->json([
            'liked' => $liked,
            'likes_count' => $likesCount,
            'message' => $liked ? 'Post liked!' : 'Post unliked!'
        ]);
    }

    /**
     * Get users who liked a post
     */
    public function getUsers(Post $post)
    {
        $likes = Like::where('post_id', $post->id)
                     ->with('user:id,name,username,avatar')
                     ->orderBy('created_at', 'desc')
                     ->paginate(20);

        return response()->json($likes);
    }

    /**
     * Get current user's liked posts
     */
    public function myLikes()
    {
        // Get posts that current user has liked - query directly
        $likedPostIds = Like::where('user_id', Auth::id())
                           ->pluck('post_id')
                           ->toArray();

        $posts = Post::whereIn('id', $likedPostIds)
                    ->with(['user'])
                    ->where('is_published', true)
                    ->whereNotNull('published_at')
                    ->where('published_at', '<=', now())
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);

        return view('posts.liked', compact('posts'));
    }
}