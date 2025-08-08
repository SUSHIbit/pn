<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Models\Follow;
use App\Models\Hashtag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Main search page
     */
    public function index(Request $request)
    {
        $query = $request->get('q');
        $type = $request->get('type', 'all'); // all, posts, users, hashtags

        $results = [
            'posts' => collect(),
            'users' => collect(),
            'hashtags' => collect(),
        ];

        if (!empty($query)) {
            switch ($type) {
                case 'posts':
                    $results['posts'] = $this->searchPosts($query);
                    break;
                case 'users':
                    $results['users'] = $this->searchUsers($query);
                    break;
                case 'hashtags':
                    $results['hashtags'] = $this->searchHashtags($query);
                    break;
                default:
                    $results['posts'] = $this->searchPosts($query, 5);
                    $results['users'] = $this->searchUsers($query, 5);
                    $results['hashtags'] = $this->searchHashtags($query, 5);
                    break;
            }
        }

        $totalResults = $results['posts']->count() + $results['users']->count() + $results['hashtags']->count();
        $trending = $this->getTrending();

        return view('search.index', compact('query', 'type', 'results', 'totalResults', 'trending'));
    }

    /**
     * Search posts
     */
    private function searchPosts($query, $limit = null)
    {
        $posts = Post::where('is_published', true)
                    ->whereNotNull('published_at')
                    ->where('published_at', '<=', now())
                    ->where(function($q) use ($query) {
                        $q->where('title', 'LIKE', "%{$query}%")
                          ->orWhere('content', 'LIKE', "%{$query}%");
                    })
                    ->with(['user'])
                    ->withCount(['likes', 'comments'])
                    ->orderBy('published_at', 'desc');

        if ($limit) {
            $posts = $posts->take($limit);
        } else {
            $posts = $posts->paginate(10);
        }

        return $posts->get();
    }

    /**
     * Search users
     */
    private function searchUsers($query, $limit = null)
    {
        $users = User::where(function($q) use ($query) {
                        $q->where('name', 'LIKE', "%{$query}%")
                          ->orWhere('username', 'LIKE', "%{$query}%")
                          ->orWhere('bio', 'LIKE', "%{$query}%");
                    })
                    ->withCount(['posts' => function($q) {
                        $q->where('is_published', true);
                    }]);

        if ($limit) {
            $users = $users->take($limit)->get();
        } else {
            $users = $users->paginate(20)->items();
        }

        // Add followers/following counts manually
        foreach ($users as $user) {
            $user->followers_count = Follow::where('following_id', $user->id)->count();
            $user->following_count = Follow::where('follower_id', $user->id)->count();
        }

        return collect($users);
    }

    /**
     * Search hashtags
     */
    private function searchHashtags($query, $limit = null)
    {
        $hashtags = Hashtag::where('name', 'LIKE', "%{$query}%")
                          ->withCount('posts')
                          ->orderBy('posts_count', 'desc');

        if ($limit) {
            return $hashtags->take($limit)->get();
        } else {
            return $hashtags->paginate(20)->items();
        }
    }

    /**
     * Get trending hashtags and popular content
     */
    private function getTrending()
    {
        // Get trending hashtags (most used in last 7 days)
        $trendingHashtags = Hashtag::whereHas('posts', function($q) {
                                      $q->where('published_at', '>=', now()->subDays(7));
                                  })
                                  ->withCount(['posts' => function($q) {
                                      $q->where('published_at', '>=', now()->subDays(7));
                                  }])
                                  ->orderBy('posts_count', 'desc')
                                  ->take(10)
                                  ->get();

        // Get popular posts (most liked in last 7 days)
        $popularPosts = Post::where('is_published', true)
                           ->where('published_at', '>=', now()->subDays(7))
                           ->withCount(['likes'])
                           ->with(['user'])
                           ->having('likes_count', '>', 0)
                           ->orderBy('likes_count', 'desc')
                           ->take(5)
                           ->get();

        // Get active users (most posts in last 30 days)
        $activeUsers = User::whereHas('posts', function($q) {
                              $q->where('published_at', '>=', now()->subDays(30))
                                ->where('is_published', true);
                          })
                          ->withCount(['posts' => function($q) {
                              $q->where('published_at', '>=', now()->subDays(30))
                                ->where('is_published', true);
                          }])
                          ->orderBy('posts_count', 'desc')
                          ->take(5)
                          ->get();

        // Add follower counts for active users
        foreach ($activeUsers as $user) {
            $user->followers_count = Follow::where('following_id', $user->id)->count();
        }

        return [
            'hashtags' => $trendingHashtags,
            'posts' => $popularPosts,
            'users' => $activeUsers,
        ];
    }

    /**
     * Show hashtag page
     */
    public function hashtag($hashtag)
    {
        $hashtagModel = Hashtag::where('name', $hashtag)->firstOrFail();
        
        $posts = $hashtagModel->posts()
                             ->where('is_published', true)
                             ->whereNotNull('published_at')
                             ->where('published_at', '<=', now())
                             ->with(['user'])
                             ->withCount(['likes', 'comments'])
                             ->orderBy('published_at', 'desc')
                             ->paginate(10);

        $relatedHashtags = Hashtag::where('name', '!=', $hashtag)
                                 ->withCount('posts')
                                 ->orderBy('posts_count', 'desc')
                                 ->take(10)
                                 ->get();

        return view('search.hashtag', compact('hashtagModel', 'posts', 'relatedHashtags'));
    }

    /**
     * AJAX search suggestions
     */
    public function suggestions(Request $request)
    {
        $query = $request->get('q');
        
        if (empty($query) || strlen($query) < 2) {
            return response()->json([]);
        }

        $suggestions = [];

        // User suggestions
        $users = User::where(function($q) use ($query) {
                        $q->where('name', 'LIKE', "{$query}%")
                          ->orWhere('username', 'LIKE', "{$query}%");
                    })
                    ->take(5)
                    ->get(['id', 'name', 'username', 'avatar']);

        foreach ($users as $user) {
            $suggestions[] = [
                'type' => 'user',
                'name' => $user->name,
                'username' => $user->username,
                'avatar' => $user->avatar_url,
                'url' => route('user.profile', $user->username),
            ];
        }

        // Hashtag suggestions
        $hashtags = Hashtag::where('name', 'LIKE', "{$query}%")
                          ->withCount('posts')
                          ->orderBy('posts_count', 'desc')
                          ->take(5)
                          ->get();

        foreach ($hashtags as $hashtag) {
            $suggestions[] = [
                'type' => 'hashtag',
                'name' => "#{$hashtag->name}",
                'count' => $hashtag->posts_count,
                'url' => route('search.hashtag', $hashtag->name),
            ];
        }

        return response()->json($suggestions);
    }

    /**
     * Advanced search page
     */
    public function advanced()
    {
        return view('search.advanced');
    }

    /**
     * Process advanced search
     */
    public function advancedSearch(Request $request)
    {
        $request->validate([
            'keywords' => 'nullable|string|max:255',
            'author' => 'nullable|string|max:255',
            'hashtags' => 'nullable|string|max:255',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $query = Post::where('is_published', true)
                    ->whereNotNull('published_at')
                    ->where('published_at', '<=', now());

        // Keywords search
        if ($request->filled('keywords')) {
            $keywords = $request->input('keywords');
            $query->where(function($q) use ($keywords) {
                $q->where('title', 'LIKE', "%{$keywords}%")
                  ->orWhere('content', 'LIKE', "%{$keywords}%");
            });
        }

        // Author search
        if ($request->filled('author')) {
            $author = $request->input('author');
            $query->whereHas('user', function($q) use ($author) {
                $q->where('name', 'LIKE', "%{$author}%")
                  ->orWhere('username', 'LIKE', "%{$author}%");
            });
        }

        // Hashtags search
        if ($request->filled('hashtags')) {
            $hashtags = explode(',', $request->input('hashtags'));
            $hashtags = array_map('trim', $hashtags);
            
            $query->whereHas('hashtags', function($q) use ($hashtags) {
                $q->whereIn('name', $hashtags);
            });
        }

        // Date range
        if ($request->filled('date_from')) {
            $query->where('published_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->where('published_at', '<=', $request->input('date_to') . ' 23:59:59');
        }

        $posts = $query->with(['user'])
                      ->withCount(['likes', 'comments'])
                      ->orderBy('published_at', 'desc')
                      ->paginate(10);

        return view('search.results', compact('posts', 'request'));
    }
}