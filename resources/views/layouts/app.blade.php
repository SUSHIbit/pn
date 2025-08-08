<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'Social News') }}</title>
    
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    @stack('styles')
</head>
<body>
    <div class="app-container">
        <!-- Left Sidebar - Updated -->
        <aside class="left-sidebar" id="leftSidebar">
            <button class="sidebar-toggle" onclick="toggleLeftSidebar()" title="Toggle Navigation">
                <i class="fas fa-bars"></i>
            </button>
            
            <div class="brand">
                <h1 class="collapse-hide">{{ config('app.name', 'Social News') }}</h1>
            </div>
            
            <nav>
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                            <i class="fas fa-home"></i>
                            <span class="collapse-hide">Home</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('search') }}" class="nav-link {{ request()->routeIs('search*') ? 'active' : '' }}">
                            <i class="fas fa-search"></i>
                            <span class="collapse-hide">Explore</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('posts.feed') }}" class="nav-link {{ request()->routeIs('posts.feed') ? 'active' : '' }}">
                            <i class="fas fa-stream"></i>
                            <span class="collapse-hide">Feed</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('posts.index') }}" class="nav-link {{ request()->routeIs('posts.index') ? 'active' : '' }}">
                            <i class="fas fa-newspaper"></i>
                            <span class="collapse-hide">Latest</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('posts.my') }}" class="nav-link {{ request()->routeIs('posts.my') ? 'active' : '' }}">
                            <i class="fas fa-file-alt"></i>
                            <span class="collapse-hide">My Posts</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('posts.liked') }}" class="nav-link {{ request()->routeIs('posts.liked') ? 'active' : '' }}">
                            <i class="fas fa-heart"></i>
                            <span class="collapse-hide">Liked</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('users.discover') }}" class="nav-link {{ request()->routeIs('users.discover') || request()->routeIs('users.following') || request()->routeIs('users.followers') ? 'active' : '' }}">
                            <i class="fas fa-users"></i>
                            <span class="collapse-hide">People</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-bell"></i>
                            <span class="collapse-hide">Notifications</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('profile') }}" class="nav-link {{ request()->routeIs('profile') || request()->routeIs('user.profile') ? 'active' : '' }}">
                            <i class="fas fa-user"></i>
                            <span class="collapse-hide">Profile</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('posts.create') }}" class="nav-link {{ request()->routeIs('posts.create') ? 'active' : '' }}">
                            <i class="fas fa-plus-circle"></i>
                            <span class="collapse-hide">Create</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('settings') }}" class="nav-link {{ request()->routeIs('settings') ? 'active' : '' }}">
                            <i class="fas fa-cog"></i>
                            <span class="collapse-hide">Settings</span>
                        </a>
                    </li>
                </ul>
            </nav>
            
            <!-- Quick Search in Sidebar -->
            <div class="sidebar-search collapse-hide" style="margin-bottom: 20px; padding: 0 4px;">
                <form action="{{ route('search') }}" method="GET">
                    <div style="position: relative;">
                        <input type="text" name="q" placeholder="Quick search..." 
                               style="width: 100%; padding: 8px 12px 8px 35px; border: 1px solid var(--stone-300); border-radius: 20px; font-size: 14px; background-color: var(--stone-50);">
                        <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--stone-500); font-size: 12px;"></i>
                    </div>
                </form>
            </div>
            
            <!-- Trending Hashtags (Mini Widget) -->
            <div class="sidebar-trending collapse-hide" style="margin-bottom: 20px;">
                <h4 style="font-size: 14px; color: var(--stone-700); margin-bottom: 10px; padding: 0 4px;">
                    <i class="fas fa-fire" style="color: #f59e0b; margin-right: 6px;"></i>
                    Trending
                </h4>
                <div id="trending-hashtags" style="display: flex; flex-direction: column; gap: 4px;">
                    <!-- Dynamic trending hashtags will be loaded here -->
                </div>
            </div>
            
            <!-- Profile Section - Fixed Overflow -->
            <div class="profile-section">
                <img src="{{ auth()->user()->avatar_url }}" alt="Profile" class="user-avatar">
                <div class="profile-info collapse-hide">
                    <div class="profile-name">{{ auth()->user()->display_name }}</div>
                    <div class="profile-username">{{ '@' . auth()->user()->username }}</div>
                </div>
            </div>
            
            <!-- Logout Button -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="collapse-hide">Logout</span>
                </button>
            </form>
        </aside>

        <!-- Main Content - Expanded -->
        <main class="main-content" id="mainContent">
            @yield('content')
        </main>
    </div>
    
    <!-- JavaScript -->
    <script>
        function toggleLeftSidebar() {
            const sidebar = document.getElementById('leftSidebar');
            const mainContent = document.getElementById('mainContent');
            
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        }

        // Load trending hashtags
        document.addEventListener('DOMContentLoaded', function() {
            loadTrendingHashtags();
        });

        function loadTrendingHashtags() {
            // Simulated trending hashtags - in real implementation, fetch from API
            const trendingHashtags = [
                { name: 'technology', count: 45 },
                { name: 'news', count: 38 },
                { name: 'lifestyle', count: 29 },
                { name: 'business', count: 22 },
                { name: 'health', count: 18 }
            ];

            const container = document.getElementById('trending-hashtags');
            
            trendingHashtags.slice(0, 5).forEach(hashtag => {
                const link = document.createElement('a');
                link.href = `/hashtag/${hashtag.name}`;
                link.style.cssText = `
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 6px 8px;
                    color: var(--stone-600);
                    text-decoration: none;
                    border-radius: 6px;
                    font-size: 12px;
                    transition: all 0.2s;
                `;
                
                link.innerHTML = `
                    <span>#${hashtag.name}</span>
                    <span style="color: var(--stone-400);">${hashtag.count}</span>
                `;
                
                link.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = 'var(--stone-100)';
                });
                
                link.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = 'transparent';
                });
                
                container.appendChild(link);
            });
        }
    </script>

    @stack('scripts')
</body>
</html>