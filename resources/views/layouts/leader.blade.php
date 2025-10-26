<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leader Dashboard - @yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Mobile menu styles */
        .mobile-menu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-in-out;
        }
        .mobile-menu.active {
            max-height: 500px;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex flex-col">
            <!-- Top Navigation Bar -->
            <header class="bg-white shadow-md sticky top-0 z-30">
                <div class="px-4 sm:px-6 py-3">
                    <div class="flex items-center justify-between">
                        <!-- Logo & Mobile Menu Toggle -->
                        <div class="flex items-center space-x-4">
                            <!-- Mobile Menu Toggle -->
                            <button id="mobile-menu-toggle" class="md:hidden text-gray-600 hover:text-gray-900 focus:outline-none">
                                <i class="fas fa-bars text-2xl"></i>
                            </button>
                            
                            <!-- Logo/Branding -->
                            <a href="{{ route('leader.dashboard') }}" class="flex items-center space-x-2">
                                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-2 shadow-lg">
                                    <i class="fas fa-crown text-white text-xl"></i>
                                </div>
                                <div class="hidden sm:block">
                                    <h1 class="text-lg font-bold text-gray-800">Leader Portal</h1>
                                    <p class="text-xs text-gray-500">Project Management</p>
                                </div>
                            </a>
                        </div>

                        <!-- Desktop Navigation Links -->
                        <nav class="hidden md:flex items-center space-x-1">
                            <a href="{{ route('leader.dashboard') }}" 
                               class="flex items-center px-4 py-2 rounded-lg transition-colors {{ request()->routeIs('leader.dashboard') ? 'bg-green-100 text-green-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                                <i class="fas fa-home mr-2"></i>
                                <span>Dashboard</span>
                            </a>

                            @php
                                $user = Auth::user();
                                $userProject = \App\Models\ProjectMember::where('user_id', $user->user_id)
                                    ->whereIn('role', ['admin', 'leader'])
                                    ->with('project')
                                    ->first();
                            @endphp

                            @if($userProject && $userProject->project)
                                <a href="{{ route('leader.project.details', $userProject->project) }}" 
                                   class="flex items-center px-4 py-2 rounded-lg transition-colors {{ request()->routeIs('leader.project.*') || request()->routeIs('leader.card.*') ? 'bg-green-100 text-green-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                                    <i class="fas fa-folder-open mr-2"></i>
                                    <span>My Project</span>
                                </a>

                                <a href="{{ route('leader.project.monitoring', $userProject->project) }}" 
                                   class="flex items-center px-4 py-2 rounded-lg transition-colors {{ request()->routeIs('leader.project.monitoring') ? 'bg-green-100 text-green-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                                    <i class="fas fa-chart-line mr-2"></i>
                                    <span>Monitoring</span>
                                </a>
                            @endif

                            <a href="{{ route('leader.review.index') }}" 
                               class="flex items-center px-4 py-2 rounded-lg transition-colors {{ request()->routeIs('leader.review.*') ? 'bg-green-100 text-green-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                                <i class="fas fa-clipboard-check mr-2"></i>
                                <span>Review Tasks</span>
                        @php
                            $user = Auth::user();
                            $projectIds = \App\Models\ProjectMember::where('user_id', $user->user_id)
                                ->whereIn('role', ['admin', 'leader'])
                                ->pluck('project_id');
                            $reviewCount = \App\Models\Subtask::whereHas('card.board.project', function($query) use ($projectIds) {
                                $query->whereIn('project_id', $projectIds);
                            })->where('status', 'review')->count();
                        @endphp
                        @if($reviewCount > 0)
                                    <span class="ml-2 px-2 py-0.5 text-xs bg-red-500 text-white rounded-full animate-pulse">{{ $reviewCount }}</span>
                        @endif
                </a>
            </nav>

                        <!-- User Profile & Actions -->
                        <div class="flex items-center space-x-3">
                            <!-- User Info & Profile Dropdown -->
                            <div class="relative group">
                                <button class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors">
                                    <div class="text-right hidden sm:block">
                                        <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->full_name }}</p>
                                        <span class="inline-block px-2 py-0.5 text-xs bg-green-100 text-green-800 rounded-full font-medium">
                                            <i class="fas fa-crown mr-1"></i>Leader
                                        </span>
                                    </div>
                                    <div class="h-10 w-10 rounded-full bg-green-500 flex items-center justify-center text-white font-semibold">
                                        {{ strtoupper(substr(Auth::user()->full_name, 0, 1)) }}
                                    </div>
                                </button>

                                <!-- Profile Dropdown -->
                                <div class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                                    <div class="py-2">
                                        <div class="px-4 py-3 border-b border-gray-200">
                                            <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->full_name }}</p>
                                            <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                                        </div>
                                        <a href="{{ route('leader.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-tachometer-alt mr-2 text-gray-500"></i>Dashboard
                                        </a>
                                        <div class="border-t border-gray-200 my-2"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </button>
                </form>
            </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile Navigation Menu -->
                    <div id="mobile-nav-menu" class="mobile-menu md:hidden border-t border-gray-200 mt-3">
                        <div class="py-3 space-y-2">
                            <a href="{{ route('leader.dashboard') }}" 
                               class="flex items-center px-4 py-2 rounded-lg transition-colors {{ request()->routeIs('leader.dashboard') ? 'bg-green-100 text-green-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                                <i class="fas fa-home mr-3"></i>
                                <span>Dashboard</span>
                            </a>

                            @php
                                $userMobile = Auth::user();
                                $userProjectMobile = \App\Models\ProjectMember::where('user_id', $userMobile->user_id)
                                    ->whereIn('role', ['admin', 'leader'])
                                    ->with('project')
                                    ->first();
                            @endphp

                            @if($userProjectMobile && $userProjectMobile->project)
                                <a href="{{ route('leader.project.details', $userProjectMobile->project) }}" 
                                   class="flex items-center px-4 py-2 rounded-lg transition-colors {{ request()->routeIs('leader.project.*') || request()->routeIs('leader.card.*') ? 'bg-green-100 text-green-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                                    <i class="fas fa-folder-open mr-3"></i>
                                    <span>My Project</span>
                                </a>

                                <a href="{{ route('leader.project.monitoring', $userProjectMobile->project) }}" 
                                   class="flex items-center px-4 py-2 rounded-lg transition-colors {{ request()->routeIs('leader.project.monitoring') ? 'bg-green-100 text-green-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                                    <i class="fas fa-chart-line mr-3"></i>
                                    <span>Monitoring</span>
                                </a>
                            @endif

                            <a href="{{ route('leader.review.index') }}" 
                               class="flex items-center px-4 py-2 rounded-lg transition-colors {{ request()->routeIs('leader.review.*') ? 'bg-green-100 text-green-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                                <i class="fas fa-clipboard-check mr-3"></i>
                                <span>Review Tasks</span>
                                @php
                                    $user = Auth::user();
                                    $projectIds = \App\Models\ProjectMember::where('user_id', $user->user_id)
                                        ->whereIn('role', ['admin', 'leader'])
                                        ->pluck('project_id');
                                    $reviewCount = \App\Models\Subtask::whereHas('card.board.project', function($query) use ($projectIds) {
                                        $query->whereIn('project_id', $projectIds);
                                    })->where('status', 'review')->count();
                                @endphp
                                @if($reviewCount > 0)
                                    <span class="ml-auto px-2 py-0.5 text-xs bg-red-500 text-white rounded-full">{{ $reviewCount }}</span>
                                @endif
                            </a>

                            <div class="border-t border-gray-200 pt-2 mt-2">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center w-full px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                        <i class="fas fa-sign-out-alt mr-3"></i>
                                        <span>Logout</span>
                        </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl mx-auto w-full">
                @if(session('success'))
                    <div class="alert-notification bg-green-100 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded-lg shadow-md mb-4 flex items-center justify-between" role="alert">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle mr-3 text-xl"></i>
                            <span class="font-medium">{{ session('success') }}</span>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert-notification bg-red-100 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded-lg shadow-md mb-4 flex items-center justify-between" role="alert">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle mr-3 text-xl"></i>
                            <span class="font-medium">{{ session('error') }}</span>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-red-700 hover:text-red-900">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif

                @yield('content')
            </main>
    </div>

    <script>
        // Mobile menu toggle (navbar)
        const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
        const mobileNavMenu = document.getElementById('mobile-nav-menu');

        mobileMenuToggle?.addEventListener('click', () => {
            mobileNavMenu.classList.toggle('active');
            
            // Toggle icon
            const icon = mobileMenuToggle.querySelector('i');
            if (mobileNavMenu.classList.contains('active')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });

        // Auto-hide alerts
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert-notification');
            alerts.forEach(function(alert) {
                alert.style.opacity = '0';
                alert.style.transform = 'translateX(-10px)';
                alert.style.transition = 'all 0.3s ease-in-out';
                
                setTimeout(() => {
                    alert.style.opacity = '1';
                    alert.style.transform = 'translateX(0)';
                }, 10);

                setTimeout(function() {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateX(-10px)';
                    setTimeout(function() {
                        alert.remove();
                    }, 300);
                }, 5000);
            });
        });
    </script>
</body>
</html>
