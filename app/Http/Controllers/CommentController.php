<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a newly created comment
     */
    public function store(Request $request, Post $post)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        // Check if post exists and is published (or user owns it)
        if (!$post->is_published && $post->user_id !== Auth::id()) {
            abort(404);
        }

        $comment = Comment::create([
            'user_id' => Auth::id(),
            'post_id' => $post->id,
            'content' => $request->input('content'), // Fixed: use input() method
        ]);

        return redirect()->route('posts.show', $post)
                        ->with('success', 'Comment added successfully!');
    }

    /**
     * Update the specified comment
     */
    public function update(Request $request, Comment $comment)
    {
        // Check if user owns the comment
        if ($comment->user_id !== Auth::id()) {
            abort(403, 'You can only edit your own comments.');
        }

        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $comment->update([
            'content' => $request->input('content'), // Fixed: use input() method
        ]);

        return redirect()->route('posts.show', $comment->post)
                        ->with('success', 'Comment updated successfully!');
    }

    /**
     * Remove the specified comment
     */
    public function destroy(Comment $comment)
    {
        // Check if user owns the comment or owns the post
        if ($comment->user_id !== Auth::id() && $comment->post->user_id !== Auth::id()) {
            abort(403, 'You can only delete your own comments or comments on your posts.');
        }

        $post = $comment->post;
        $comment->delete();

        return redirect()->route('posts.show', $post)
                        ->with('success', 'Comment deleted successfully!');
    }

    /**
     * Get comments for a post (AJAX endpoint)
     */
    public function getComments(Post $post)
    {
        $comments = $post->comments()
                         ->with('user')
                         ->latest()
                         ->paginate(10);

        return response()->json([
            'comments' => $comments->items(),
            'has_more' => $comments->hasMorePages(),
            'next_page' => $comments->nextPageUrl(),
        ]);
    }
}