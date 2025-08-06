<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\FollowController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect root to login if not authenticated, or home if authenticated
Route::get('/', function () {
    return auth()->check() ? redirect()->route('home') : redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    
    // Google OAuth Routes
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Home
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    
    // User Routes
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::get('/profile/{username}', [UserController::class, 'profile'])->name('user.profile');
    Route::get('/settings', [UserController::class, 'settings'])->name('settings');
    Route::post('/settings/profile', [UserController::class, 'updateProfile'])->name('profile.update');
    
    // User Social Routes
    Route::get('/users/discover', [UserController::class, 'discover'])->name('users.discover');
    Route::get('/users/following', [UserController::class, 'following'])->name('users.following');
    Route::get('/users/{username}/following', [UserController::class, 'following'])->name('users.user.following');
    Route::get('/users/followers', [UserController::class, 'followers'])->name('users.followers');
    Route::get('/users/{username}/followers', [UserController::class, 'followers'])->name('users.user.followers');
    
    // Follow/Unfollow Routes
    Route::post('/users/{user}/follow', [FollowController::class, 'toggle'])->name('users.follow');
    
    // Post Routes
    Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
    Route::get('/posts/feed', [PostController::class, 'feed'])->name('posts.feed');
    Route::get('/posts/liked', [PostController::class, 'liked'])->name('posts.liked');
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/my-posts', [PostController::class, 'myPosts'])->name('posts.my');
    Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
    
    // Like Routes
    Route::post('/posts/{post}/like', [LikeController::class, 'toggle'])->name('posts.like');
    Route::get('/posts/{post}/likes', [LikeController::class, 'getUsers'])->name('posts.likes');
    
    // Comment Routes
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    Route::get('/posts/{post}/comments', [CommentController::class, 'getComments'])->name('comments.get');
});