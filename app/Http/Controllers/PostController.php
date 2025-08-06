<?php

namespace App\Http\Controllers;

use App\Models\Post;
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
        $posts = Post::with('user')
                    ->where('is_published', true)
                    ->orderBy('created_at', 'desc')
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

        // Create the post data array
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

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . Auth::id() . '.' . $image->getClientOriginalExtension();
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

        $post->load('user');
        return view('posts.show', compact('post'));
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

        // Create update data array
        $updateData = [];
        $updateData['title'] = $request->input('title');
        $updateData['content'] = $request->input('content');
        $updateData['is_published'] = $request->has('is_published');

        // Handle published_at
        if ($request->has('is_published') && !$post->is_published) {
            $updateData['published_at'] = now();
        } elseif (!$request->has('is_published')) {
            $updateData['published_at'] = null;
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($post->image && file_exists(public_path('uploads/posts/' . $post->image))) {
                unlink(public_path('uploads/posts/' . $post->image));
            }

            $image = $request->file('image');
            $imageName = time() . '_' . Auth::id() . '.' . $image->getClientOriginalExtension();
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
        $posts = Post::where('user_id', Auth::id())
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);

        return view('posts.my-posts', compact('posts'));
    }
}