<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - @yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            transition: width 0.3s ease-in-out, transform 0.3s ease-in-out;
            overflow: hidden;
        }
        .sidebar-minimized {
            width: 4.5rem;
        }
        .sidebar-expanded {
            width: 16rem;
        }
        .menu-text {
            transition: opacity 0.2s ease-in-out, visibility 0.2s ease-in-out;
            white-space: nowrap;
        }
        .menu-text-hidden {
            opacity: 0;
            visibility: hidden;
            width: 0;
            display: none;
        }
        .sidebar-minimized .menu-text {
            display: none;
        }
        .logo-expanded {
            transition: opacity 0.2s ease-in-out, visibility 0.2s ease-in-out;
        }
        .sidebar-minimized .logo-expanded {
            display: none;
        }
        .logo-minimized {
            display: none;
            transition: opacity 0.2s ease-in-out, visibility 0.2s ease-in-out;
        }
        .sidebar-minimized .logo-minimized {
            display: flex;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar sidebar-expanded bg-gradient-to-b from-gray-800 to-gray-900 text-white fixed md:relative inset-y-0 left-0 z-50 shadow-2xl transform -translate-x-full md:translate-x-0">
            <!-- Sidebar Header -->
            <div class="p-4 border-b border-gray-700">
                <!-- Expanded Logo -->
                <div class="logo-expanded flex items-center justify-between">
                    <div class="flex items-center space-x-3 overflow-hidden">
                        <div class="bg-purple-600 rounded-lg p-2 flex-shrink-0">
                            <i class="fas fa-shield-alt text-2xl"></i>
                        </div>
                        <span id="sidebar-title" class="menu-text text-xl font-bold whitespace-nowrap">Admin</span>
                    </div>
                    <button id="sidebar-toggle" class="hidden md:block text-white hover:bg-gray-700 p-2 rounded-lg transition-colors flex-shrink-0">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
                
                <!-- Minimized Logo (Hamburger Icon) -->
                <div class="logo-minimized justify-center items-center">
                    <button id="sidebar-toggle-minimized" class="text-white hover:bg-gray-700 p-2 rounded-lg transition-colors">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="mt-6 px-3">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center py-3 px-4 mb-2 rounded-lg transition-all duration-200 hover:bg-gray-700 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700 shadow-lg' : '' }}">
                    <div class="flex items-center justify-center w-8">
                        <i class="fas fa-home text-xl"></i>
                    </div>
                    <span class="menu-text ml-4">Dashboard</span>
                </a>
                <a href="{{ route('admin.projects.index') }}" class="flex items-center py-3 px-4 mb-2 rounded-lg transition-all duration-200 hover:bg-gray-700 {{ request()->routeIs('admin.projects.*') ? 'bg-gray-700 shadow-lg' : '' }}">
                    <div class="flex items-center justify-center w-8">
                        <i class="fas fa-project-diagram text-xl"></i>
                    </div>
                    <span class="menu-text ml-4">Projects</span>
                </a>
                <a href="{{ route('admin.users.index') }}" class="flex items-center py-3 px-4 mb-2 rounded-lg transition-all duration-200 hover:bg-gray-700 {{ request()->routeIs('admin.users.*') ? 'bg-gray-700 shadow-lg' : '' }}">
                    <div class="flex items-center justify-center w-8">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                    <span class="menu-text ml-4">Users</span>
                </a>
                <a href="{{ route('admin.monitoring.index') }}" class="flex items-center py-3 px-4 mb-2 rounded-lg transition-all duration-200 hover:bg-gray-700 {{ request()->routeIs('admin.monitoring.*') ? 'bg-gray-700 shadow-lg' : '' }}">
                    <div class="flex items-center justify-center w-8">
                        <i class="fas fa-chart-line text-xl"></i>
                    </div>
                    <span class="menu-text ml-4">Monitoring</span>
                </a>
            </nav>

            <!-- Logout Button -->
            <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-700">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center w-full py-3 px-4 rounded-lg transition-all duration-200 hover:bg-red-600 bg-red-500">
                        <div class="flex items-center justify-center w-8">
                            <i class="fas fa-sign-out-alt text-xl"></i>
                        </div>
                        <span class="menu-text ml-4">Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Overlay for mobile -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden md:hidden"></div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-h-screen">
            <!-- Top Navigation Bar -->
            <header class="bg-white shadow-md sticky top-0 z-30">
                <div class="px-4 sm:px-6 py-4">
                    <div class="flex items-center justify-between">
                        <button id="mobile-menu-toggle" class="md:hidden text-gray-600 hover:text-gray-900 focus:outline-none">
                            <i class="fas fa-bars text-2xl"></i>
                        </button>
                        
                        <div class="flex items-center space-x-4">
                            <!-- Project Completion Notification -->
                            @php
                                $readyProjects = \App\Models\Project::with('boards.cards')
                                    ->where('status', 'active')
                                    ->get()
                                    ->filter(function($project) {
                                        return $project->isReadyToComplete();
                                    });
                            @endphp
                            
                            @if($readyProjects->count() > 0)
                                <a href="{{ route('admin.projects.index') }}" 
                                   class="relative flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-green-50 transition-colors group">
                                    <div class="relative">
                                        <i class="fas fa-trophy text-green-600 text-xl group-hover:scale-110 transition-transform"></i>
                                        <span class="absolute -top-2 -right-2 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-500 rounded-full animate-pulse">
                                            {{ $readyProjects->count() }}
                                        </span>
                                    </div>
                                    <div class="hidden md:block">
                                        <p class="text-xs font-semibold text-green-700">Projects Ready</p>
                                        <p class="text-xs text-green-600">Click to complete</p>
                                    </div>
                                </a>
                            @endif
                            
                            <div class="text-right">
                                <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->full_name }}</p>
                                <span class="inline-block px-2 py-1 text-xs bg-purple-100 text-purple-800 rounded-full font-medium">
                                    <i class="fas fa-shield-alt mr-1"></i>Admin
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
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
    </div>

    <script>
        // Sidebar functionality
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebarToggleMinimized = document.getElementById('sidebar-toggle-minimized');
        const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
        const sidebarOverlay = document.getElementById('sidebar-overlay');
        const menuTexts = document.querySelectorAll('.menu-text');
        const sidebarTitle = document.getElementById('sidebar-title');

        // Load sidebar state from localStorage
        const sidebarState = localStorage.getItem('sidebarMinimized') === 'true';
        if (sidebarState && window.innerWidth >= 768) {
            minimizeSidebar();
        }

        // Desktop toggle (expanded state)
        sidebarToggle?.addEventListener('click', () => {
            if (sidebar.classList.contains('sidebar-expanded')) {
                minimizeSidebar();
                localStorage.setItem('sidebarMinimized', 'true');
            } else {
                expandSidebar();
                localStorage.setItem('sidebarMinimized', 'false');
            }
        });

        // Desktop toggle (minimized state)
        sidebarToggleMinimized?.addEventListener('click', () => {
            if (sidebar.classList.contains('sidebar-minimized')) {
                expandSidebar();
                localStorage.setItem('sidebarMinimized', 'false');
            } else {
                minimizeSidebar();
                localStorage.setItem('sidebarMinimized', 'true');
            }
        });

        // Mobile toggle
        mobileMenuToggle?.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
            sidebarOverlay.classList.toggle('hidden');
        });

        // Close mobile sidebar when clicking overlay
        sidebarOverlay?.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
            sidebarOverlay.classList.add('hidden');
        });

        function minimizeSidebar() {
            sidebar.classList.remove('sidebar-expanded');
            sidebar.classList.add('sidebar-minimized');
            menuTexts.forEach(text => text.classList.add('menu-text-hidden'));
            sidebarTitle.classList.add('menu-text-hidden');
        }

        function expandSidebar() {
            sidebar.classList.remove('sidebar-minimized');
            sidebar.classList.add('sidebar-expanded');
            menuTexts.forEach(text => text.classList.remove('menu-text-hidden'));
            sidebarTitle.classList.remove('menu-text-hidden');
        }

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
