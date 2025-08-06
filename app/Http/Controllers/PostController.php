<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Like;
use App\Models\Follow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $posts = Post::with(['user'])
                    ->withCount(['likes', 'comments'])
                    ->where('is_published', true)
                    ->whereNotNull('published_at')
                    ->where('published_at', '<=', now())
                    ->orderBy('published_at', 'desc')
                    ->paginate(10);

        return view('posts.index', compact('posts'));
    }

    public function create()
    {
        return view('posts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $postData = [];
        $postData['user_id'] = Auth::id();
        $postData['title'] = $request->input('title');
        $postData['content'] = $request->input('content');
        $postData['is_published'] = $request->has('is_published');
        
        if ($request->has('is_published')) {
            $postData['published_at'] = now();
        } else {
            $postData['published_at'] = null;
        }

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . Auth::id() . '.' . $image->getClientOriginalExtension();
            
            if (!file_exists(public_path('uploads/posts'))) {
                mkdir(public_path('uploads/posts'), 0755, true);
            }
            
            $image->move(public_path('uploads/posts'), $imageName);
            $postData['image'] = $imageName;
        }

        $post = Post::create($postData);

        return redirect()->route('posts.show', $post)->with('success', 'Post created successfully!');
    }

    public function show(Post $post)
    {
        if (!$post->is_published && $post->user_id !== Auth::id()) {
            abort(404);
        }

        $post->load(['user']);
        $post->loadCount(['likes', 'comments']);
        
        $comments = $post->comments()
                        ->with('user')
                        ->oldest()
                        ->paginate(10);

        return view('posts.show', compact('post', 'comments'));
    }

    public function edit(Post $post)
    {
        if ($post->user_id !== Auth::id()) {
            abort(403);
        }

        return view('posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        if ($post->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $updateData = [];
        $updateData['title'] = $request->input('title');
        $updateData['content'] = $request->input('content');
        $updateData['is_published'] = $request->has('is_published');

        if ($request->has('is_published') && !$post->is_published) {
            $updateData['published_at'] = now();
        } elseif (!$request->has('is_published')) {
            $updateData['published_at'] = null;
        }

        if ($request->hasFile('image')) {
            if ($post->image && file_exists(public_path('uploads/posts/' . $post->image))) {
                unlink(public_path('uploads/posts/' . $post->image));
            }

            $image = $request->file('image');
            $imageName = time() . '_' . Auth::id() . '.' . $image->getClientOriginalExtension();
            
            if (!file_exists(public_path('uploads/posts'))) {
                mkdir(public_path('uploads/posts'), 0755, true);
            }
            
            $image->move(public_path('uploads/posts'), $imageName);
            $updateData['image'] = $imageName;
        }

        $post->update($updateData);

        return redirect()->route('posts.show', $post)->with('success', 'Post updated successfully!');
    }

    public function destroy(Post $post)
    {
        if ($post->user_id !== Auth::id()) {
            abort(403);
        }

        if ($post->image && file_exists(public_path('uploads/posts/' . $post->image))) {
            unlink(public_path('uploads/posts/' . $post->image));
        }

        $post->delete();

        return redirect()->route('home')->with('success', 'Post deleted successfully!');
    }

    public function myPosts()
    {
        $posts = Post::with(['user'])
                    ->withCount(['likes', 'comments'])
                    ->where('user_id', Auth::id())
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);

        return view('posts.my-posts', compact('posts'));
    }

    /**
     * Show personalized feed based on follows - Fixed to query directly
     */
    public function feed()
    {
        $user = Auth::user();
        
        // Check if user follows anyone - query Follow table directly
        $followingCount = Follow::where('follower_id', $user->id)->count();
        
        if ($followingCount > 0) {
            // Get posts from followed users + own posts
            $followingIds = Follow::where('follower_id', $user->id)
                                 ->pluck('following_id')
                                 ->toArray();
            $followingIds[] = $user->id; // Include own posts
            
            $posts = Post::whereIn('user_id', $followingIds)
                          ->where('is_published', true)
                          ->whereNotNull('published_at')
                          ->where('published_at', '<=', now())
                          ->with(['user'])
                          ->withCount(['likes', 'comments'])
                          ->orderBy('published_at', 'desc')
                          ->paginate(10);
        } else {
            // If not following anyone, show all posts
            $posts = Post::with(['user'])
                        ->withCount(['likes', 'comments'])
                        ->where('is_published', true)
                        ->whereNotNull('published_at')
                        ->where('published_at', '<=', now())
                        ->orderBy('published_at', 'desc')
                        ->paginate(10);
        }

        return view('posts.feed', compact('posts'));
    }

    /**
     * Show liked posts - Fixed to query directly
     */
    public function liked()
    {
        // Get posts that current user has liked - query directly
        $likedPostIds = Like::where('user_id', Auth::id())
                           ->pluck('post_id')
                           ->toArray();

        $posts = Post::whereIn('id', $likedPostIds)
                    ->with(['user'])
                    ->withCount(['likes', 'comments'])
                    ->where('is_published', true)
                    ->whereNotNull('published_at')
                    ->where('published_at', '<=', now())
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);

        return view('posts.liked', compact('posts'));
    }
}