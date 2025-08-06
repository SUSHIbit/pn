<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        
        // Just get latest posts - no complex logic
        $feedPosts = Post::with(['user'])
                        ->where('is_published', true)
                        ->whereNotNull('published_at')
                        ->where('published_at', '<=', now())
                        ->orderBy('published_at', 'desc')
                        ->take(5)
                        ->get();
        
        // Get suggested users - simple query
        $suggestedUsers = User::where('id', '!=', $user->id)
                              ->take(5)
                              ->get();
        
        // Simple stats - count directly from Post table instead of using relationship
        $postsCount = Post::where('user_id', $user->id)->where('is_published', true)->count();
        
        $stats = [
            'posts_count' => $postsCount,
            'likes_received' => 0,
            'comments_received' => 0,
        ];

        return view('home.index', compact('user', 'feedPosts', 'suggestedUsers', 'stats'));
    }
}