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
</head>
<body>
    <div class="app-container">
        <!-- Left Sidebar - Improved -->
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
                        <a href="#" class="nav-link">
                            <i class="fas fa-compass"></i>
                            <span class="collapse-hide">Explore</span>
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
                        <a href="#" class="nav-link">
                            <i class="fas fa-plus-circle"></i>
                            <span class="collapse-hide">Create Post</span>
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
    </script>
</body>
</html>