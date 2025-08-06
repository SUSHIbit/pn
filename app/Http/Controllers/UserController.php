<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Post;
use App\Models\Follow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function profile($username = null)
    {
        if (!$username) {
            $user = Auth::user();
        } else {
            $user = User::where('username', $username)->firstOrFail();
        }

        // Get user's posts count manually
        $postsCount = Post::where('user_id', $user->id)
                         ->where('is_published', true)
                         ->count();

        // Get followers and following counts manually
        $followersCount = Follow::where('following_id', $user->id)->count();
        $followingCount = Follow::where('follower_id', $user->id)->count();

        // Add counts to user object
        $user->posts_count = $postsCount;
        $user->followers_count = $followersCount;
        $user->following_count = $followingCount;

        // Get user's recent posts
        $posts = Post::where('user_id', $user->id)
                    ->where('is_published', true)
                    ->whereNotNull('published_at')
                    ->where('published_at', '<=', now())
                    ->with(['user'])
                    ->withCount(['likes', 'comments'])
                    ->orderBy('published_at', 'desc')
                    ->take(6)
                    ->get();

        // Check if current user is following this user
        $isFollowing = false;
        if (Auth::id() !== $user->id) {
            $isFollowing = Follow::where('follower_id', Auth::id())
                                ->where('following_id', $user->id)
                                ->exists();
        }

        return view('users.profile', compact('user', 'posts', 'isFollowing'));
    }

    public function settings()
    {
        $user = Auth::user();
        return view('users.settings', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        // Validate
        $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Prepare update data
        $updateData = [
            'name' => $request->input('name'),
            'bio' => $request->input('bio'),
            'updated_at' => now()
        ];

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar
            if ($user->avatar && file_exists(public_path('uploads/avatars/' . $user->avatar))) {
                unlink(public_path('uploads/avatars/' . $user->avatar));
            }

            // Create directory if it doesn't exist
            if (!file_exists(public_path('uploads/avatars'))) {
                mkdir(public_path('uploads/avatars'), 0755, true);
            }

            // Upload new avatar
            $avatarFile = $request->file('avatar');
            $avatarName = time() . '_' . $userId . '.' . $avatarFile->getClientOriginalExtension();
            $avatarFile->move(public_path('uploads/avatars'), $avatarName);
            
            $updateData['avatar'] = $avatarName;
        }

        // Update using DB query (bypasses Eloquent issues)
        DB::table('users')->where('id', $userId)->update($updateData);

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    /**
     * Show users that current user is following
     */
    public function following($username = null)
    {
        if ($username) {
            $user = User::where('username', $username)->firstOrFail();
        } else {
            $user = Auth::user();
        }

        // Get following user IDs
        $followingIds = Follow::where('follower_id', $user->id)
                             ->pluck('following_id')
                             ->toArray();

        // Get the users with counts
        $users = User::whereIn('id', $followingIds)
                    ->withCount(['posts' => function($query) {
                        $query->where('is_published', true);
                    }])
                    ->paginate(20);

        // Add followers/following counts manually
        foreach ($users as $followUser) {
            $followUser->followers_count = Follow::where('following_id', $followUser->id)->count();
            $followUser->following_count = Follow::where('follower_id', $followUser->id)->count();
        }

        return view('users.following', compact('users', 'user'));
    }

    /**
     * Show users following current user
     */
    public function followers($username = null)
    {
        if ($username) {
            $user = User::where('username', $username)->firstOrFail();
        } else {
            $user = Auth::user();
        }

        // Get follower user IDs
        $followerIds = Follow::where('following_id', $user->id)
                            ->pluck('follower_id')
                            ->toArray();

        // Get the users with counts
        $users = User::whereIn('id', $followerIds)
                    ->withCount(['posts' => function($query) {
                        $query->where('is_published', true);
                    }])
                    ->paginate(20);

        // Add followers/following counts manually
        foreach ($users as $followerUser) {
            $followerUser->followers_count = Follow::where('following_id', $followerUser->id)->count();
            $followerUser->following_count = Follow::where('follower_id', $followerUser->id)->count();
        }

        return view('users.followers', compact('users', 'user'));
    }

    /**
     * Show suggested users to follow
     */
    public function discover()
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
                          ->paginate(20);

        // Add followers count manually
        foreach ($suggestions as $suggestion) {
            $suggestion->followers_count = Follow::where('following_id', $suggestion->id)->count();
        }

        return view('users.discover', compact('suggestions'));
    }
}