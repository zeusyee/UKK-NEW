<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard - @yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #1e40af; /* blue-800 */
            --primary-dark: #1e3a8a; /* blue-900 */
            --muted-bg: #f8fafc; /* gray-50 */
            --panel-bg: #ffffff; /* white panels */
        }

        /* minimal transitions */
        .sidebar {
            transition: width 0.25s ease, transform 0.25s ease;
            overflow: hidden;
        }
        .sidebar-minimized {
            width: 5rem;
        }
        .sidebar-expanded {
            width: 17rem;
        }
        .menu-text {
            transition: opacity 0.3s ease-in-out, visibility 0.3s ease-in-out;
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
            transition: opacity 0.3s ease-in-out, visibility 0.3s ease-in-out;
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
        
        /* Sidebar scroll */
        .sidebar-nav {
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.3) transparent;
        }
        .sidebar-nav::-webkit-scrollbar {
            width: 6px;
        }
        .sidebar-nav::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar-nav::-webkit-scrollbar-thumb {
            background-color: rgba(255, 255, 255, 0.3);
            border-radius: 20px;
        }
        .sidebar-nav::-webkit-scrollbar-thumb:hover {
            background-color: rgba(255, 255, 255, 0.5);
        }
        
        /* Center items when minimized */
        .sidebar-minimized .flex.items-center.justify-center {
            justify-content: center !important;
        }
        
        /* Menu item subtle accent using primary color */
        .menu-item {
            position: relative;
            overflow: hidden;
        }
        .menu-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: var(--primary);
            transform: scaleY(0);
            transition: transform 0.18s ease;
        }
        .menu-item.active::before,
        .menu-item:hover::before {
            transform: scaleY(1);
        }
        
        /* Badge pulse animation */
        @keyframes badge-pulse {
            0%, 100% {
                opacity: 1;
                transform: scale(1);
            }
            50% {
                opacity: 0.8;
                transform: scale(1.05);
            }
        }
        .badge-pulse {
            animation: badge-pulse 2s ease-in-out infinite;
        }

        /* helper classes to use single color palette */
        .bg-primary { background-color: var(--primary) !important; }
        .bg-primary-dark { background-color: var(--primary-dark) !important; }
        .text-primary { color: var(--primary) !important; }
        .muted-bg { background-color: var(--muted-bg) !important; }
        .panel-bg { background-color: var(--panel-bg) !important; }
        .no-fancy-shadow { box-shadow: none !important; }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
    <aside id="sidebar" class="sidebar sidebar-expanded text-white fixed inset-y-0 left-0 z-50 transform -translate-x-full md:translate-x-0 flex flex-col bg-primary">
            <!-- Sidebar Header - Fixed -->
            <div class="flex-shrink-0 p-5 border-b border-white border-opacity-20">
                <!-- Expanded Logo -->
                <div class="logo-expanded flex items-center justify-between">
                    <div class="flex items-center space-x-3 overflow-hidden">
                        <div style="background:transparent" class="rounded-xl p-2.5 flex-shrink-0">
                            <i class="fas fa-shield-alt text-2xl"></i>
                        </div>
                        <div class="menu-text">
                            <span class="text-xl font-bold block">Admin Panel</span>
                            <span class="text-xs text-indigo-200">Management System</span>
                        </div>
                    </div>
                    <button id="sidebar-toggle" class="hidden md:block text-white hover:bg-white hover:bg-opacity-10 p-2 rounded-lg transition-all duration-200 flex-shrink-0">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                </div>
                
                <!-- Minimized Logo -->
                <div class="logo-minimized justify-center items-center">
                    <button id="sidebar-toggle-minimized" class="text-white hover:bg-white hover:bg-opacity-10 p-2 rounded-lg transition-all duration-300">
                        <i class="fas fa-chevron-right text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Navigation - Scrollable -->
            <nav class="sidebar-nav flex-1 overflow-y-auto py-6 px-3">
                <div class="space-y-2">
                    <!-- Dashboard -->
                    <a href="{{ route('admin.dashboard') }}" class="menu-item flex items-center py-3.5 px-4 rounded-xl transition-all duration-150 hover:bg-white hover:bg-opacity-10 {{ request()->routeIs('admin.dashboard') ? 'active bg-white bg-opacity-10 no-fancy-shadow' : '' }} group">
                        <div class="flex items-center justify-center w-10 flex-shrink-0">
                            <i class="fas fa-home text-xl group-hover:scale-110 transition-transform duration-200"></i>
                        </div>
                        <span class="menu-text ml-4 font-medium">Dashboard</span>
                        @if(request()->routeIs('admin.dashboard'))
                            <i class="fas fa-circle menu-text ml-auto text-xs text-indigo-300"></i>
                        @endif
                    </a>
                    
                    <!-- Projects -->
                    <a href="{{ route('admin.projects.index') }}" class="menu-item flex items-center py-3.5 px-4 rounded-xl transition-all duration-150 hover:bg-white hover:bg-opacity-10 {{ request()->routeIs('admin.projects.index') || request()->routeIs('admin.projects.create') || request()->routeIs('admin.projects.edit') ? 'active bg-white bg-opacity-10 no-fancy-shadow' : '' }} group">
                        <div class="flex items-center justify-center w-10 flex-shrink-0">
                            <i class="fas fa-project-diagram text-xl group-hover:scale-110 transition-transform duration-300"></i>
                        </div>
                        <span class="menu-text ml-4 font-medium">Proyek</span>
                        @if(request()->routeIs('admin.projects.index') || request()->routeIs('admin.projects.create') || request()->routeIs('admin.projects.edit'))
                            <i class="fas fa-circle menu-text ml-auto text-xs text-indigo-300"></i>
                        @endif
                    </a>
                    
                    <!-- History -->
                    <a href="{{ route('admin.projects.history') }}" class="menu-item flex items-center py-3.5 px-4 rounded-xl transition-all duration-150 hover:bg-white hover:bg-opacity-10 {{ request()->routeIs('admin.projects.history') ? 'active bg-white bg-opacity-10 no-fancy-shadow' : '' }} group">
                        <div class="flex items-center justify-center w-10 flex-shrink-0">
                            <i class="fas fa-history text-xl group-hover:scale-110 transition-transform duration-300"></i>
                        </div>
                        <span class="menu-text ml-4 font-medium">Riwayat</span>
                        @if(request()->routeIs('admin.projects.history'))
                            <i class="fas fa-circle menu-text ml-auto text-xs text-indigo-300"></i>
                        @endif
                    </a>
                    
                    <!-- Users -->
                    <a href="{{ route('admin.users.index') }}" class="menu-item flex items-center py-3.5 px-4 rounded-xl transition-all duration-150 hover:bg-white hover:bg-opacity-10 {{ request()->routeIs('admin.users.*') ? 'active bg-white bg-opacity-10 no-fancy-shadow' : '' }} group">
                        <div class="flex items-center justify-center w-10 flex-shrink-0">
                            <i class="fas fa-users text-xl group-hover:scale-110 transition-transform duration-300"></i>
                        </div>
                        <span class="menu-text ml-4 font-medium">Pengguna</span>
                        @if(request()->routeIs('admin.users.*'))
                            <i class="fas fa-circle menu-text ml-auto text-xs text-indigo-300"></i>
                        @endif
                    </a>
                    
                    <!-- Time Logs -->
                    <a href="{{ route('admin.time-logs.index') }}" class="menu-item flex items-center py-3.5 px-4 rounded-xl transition-all duration-150 hover:bg-white hover:bg-opacity-10 {{ request()->routeIs('admin.time-logs.*') || request()->routeIs('admin.team-productivity') ? 'active bg-white bg-opacity-10 no-fancy-shadow' : '' }} group">
                        <div class="flex items-center justify-center w-10 flex-shrink-0">
                            <i class="fas fa-clock text-xl group-hover:scale-110 transition-transform duration-300"></i>
                        </div>
                        <span class="menu-text ml-4 font-medium">Time Logs</span>
                        @if(request()->routeIs('admin.time-logs.*') || request()->routeIs('admin.team-productivity'))
                            <i class="fas fa-circle menu-text ml-auto text-xs text-indigo-300"></i>
                        @endif
                    </a>
                    
                    <!-- Monitoring -->
                    <a href="{{ route('admin.monitoring.index') }}" class="menu-item flex items-center py-3.5 px-4 rounded-xl transition-all duration-150 hover:bg-white hover:bg-opacity-10 {{ request()->routeIs('admin.monitoring.*') ? 'active bg-white bg-opacity-10 no-fancy-shadow' : '' }} group">
                        <div class="flex items-center justify-center w-10 flex-shrink-0">
                            <i class="fas fa-chart-line text-xl group-hover:scale-110 transition-transform duration-300"></i>
                        </div>
                        <span class="menu-text ml-4 font-medium">Monitoring</span>
                        @if(request()->routeIs('admin.monitoring.*'))
                            <i class="fas fa-circle menu-text ml-auto text-xs text-indigo-300"></i>
                        @endif
                    </a>
                </div>
            </nav>

            <!-- Logout - Fixed Bottom -->
            <div class="flex-shrink-0 border-t" style="border-color: rgba(255,255,255,0.12)">
                
                <!-- Logout Button -->
                <div class="p-4 pt-0">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center justify-center sidebar-expanded:justify-start w-full py-3.5 px-4 rounded-xl transition-all duration-150 bg-white bg-opacity-10 hover:bg-opacity-20 group" title="Keluar" style="backdrop-filter: none;">
                            <div class="flex items-center justify-center w-10 flex-shrink-0">
                                <i class="fas fa-sign-out-alt text-xl group-hover:translate-x-1 transition-transform duration-200"></i>
                            </div>
                            <span class="menu-text ml-4 font-semibold">Keluar</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Overlay for mobile -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden md:hidden transition-opacity duration-300"></div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-h-screen md:ml-0" id="main-content">
            <!-- Top Navigation Bar -->
            <header class="bg-white shadow-md sticky top-0 z-30 border-b border-gray-200">
                <div class="px-4 sm:px-6 py-4">
                    <div class="flex items-center justify-between">
                        <!-- Mobile Menu Button -->
                        <button id="mobile-menu-toggle" class="md:hidden text-gray-600 hover:text-gray-900 focus:outline-none p-2 hover:bg-gray-100 rounded-lg transition-all duration-300">
                            <i class="fas fa-bars text-2xl"></i>
                        </button>
                        
                        <!-- Page Title for Mobile -->
                        <div class="md:hidden">
                            <h1 class="text-lg font-bold text-gray-800">Admin Panel</h1>
                        </div>
                        
                        <!-- Right side content -->
                        <div class="flex items-center space-x-3 sm:space-x-4">
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
                                   class="relative flex items-center space-x-2 px-3 py-2 rounded-xl hover:bg-green-50 transition-all duration-300 group">
                                            <div class="relative">
                                                <i class="fas fa-trophy text-white text-xl group-hover:scale-110 transition-transform duration-200"></i>
                                                <span class="absolute -top-2 -right-2 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white" style="background:var(--primary-dark); border-radius:999px;">{{ $readyProjects->count() }}</span>
                                            </div>
                                            <div class="hidden lg:block">
                                                <p class="text-xs font-semibold text-white">Proyek Siap</p>
                                                <p class="text-xs text-white" style="opacity:0.85">Klik untuk selesaikan</p>
                                            </div>
                                </a>
                            @endif
                            
                            <!-- User Profile -->
                            <div class="flex items-center space-x-3 px-3 py-2 rounded-xl" style="background:transparent;">
                                <div class="hidden sm:block text-right">
                                    <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->full_name }}</p>
                                    <span class="inline-block text-xs text-purple-700 font-medium">
                                        <i class="fas fa-shield-alt mr-1"></i>Administrator
                                    </span>
                                </div>
                                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold" style="background:var(--panel-bg); color:var(--primary); border:2px solid rgba(0,0,0,0.06)">
                                    {{ strtoupper(substr(Auth::user()->full_name, 0, 1)) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8" style="background:var(--muted-bg)">
                @if(session('success'))
                    <div class="alert-notification panel-bg rounded-xl px-4 py-3 mb-4 flex items-center justify-between" role="alert" style="border-left:4px solid var(--primary);">
                        <div class="flex items-center">
                            <div style="background:var(--primary); color:white" class="rounded-full p-2 mr-3">
                                <i class="fas fa-check"></i>
                            </div>
                            <span class="font-medium">{{ session('success') }}</span>
                        </div>
                        <button onclick="this.parentElement.remove()" class="p-1 rounded-lg" style="color:var(--primary);">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert-notification panel-bg rounded-xl px-4 py-3 mb-4 flex items-center justify-between" role="alert" style="border-left:4px solid var(--primary);">
                        <div class="flex items-center">
                            <div style="background:var(--primary); color:white" class="rounded-full p-2 mr-3">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <span class="font-medium">{{ session('error') }}</span>
                        </div>
                        <button onclick="this.parentElement.remove()" class="p-1 rounded-lg" style="color:var(--primary);">
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
        const mainContent = document.getElementById('main-content');
        const menuTexts = document.querySelectorAll('.menu-text');

        // Load sidebar state from localStorage
        const sidebarState = localStorage.getItem('sidebarMinimized') === 'true';
        if (sidebarState && window.innerWidth >= 768) {
            minimizeSidebar();
        } else if (window.innerWidth >= 768) {
            updateMainContentMargin(false);
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
            
            // Add animation to overlay
            if (!sidebarOverlay.classList.contains('hidden')) {
                setTimeout(() => {
                    sidebarOverlay.style.opacity = '1';
                }, 10);
            } else {
                sidebarOverlay.style.opacity = '0';
            }
        });

        // Close mobile sidebar when clicking overlay
        sidebarOverlay?.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
            sidebarOverlay.style.opacity = '0';
            setTimeout(() => {
                sidebarOverlay.classList.add('hidden');
            }, 300);
        });

        function minimizeSidebar() {
            sidebar.classList.remove('sidebar-expanded');
            sidebar.classList.add('sidebar-minimized');
            menuTexts.forEach(text => text.classList.add('menu-text-hidden'));
            updateMainContentMargin(true);
        }

        function expandSidebar() {
            sidebar.classList.remove('sidebar-minimized');
            sidebar.classList.add('sidebar-expanded');
            menuTexts.forEach(text => text.classList.remove('menu-text-hidden'));
            updateMainContentMargin(false);
        }

        function updateMainContentMargin(isMinimized) {
            if (window.innerWidth >= 768) {
                if (isMinimized) {
                    mainContent.style.marginLeft = '5rem';
                } else {
                    mainContent.style.marginLeft = '17rem';
                }
            } else {
                mainContent.style.marginLeft = '0';
            }
        }

        // Handle window resize
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 768) {
                sidebar.classList.remove('-translate-x-full');
                sidebarOverlay.classList.add('hidden');
                const isMinimized = sidebar.classList.contains('sidebar-minimized');
                updateMainContentMargin(isMinimized);
            } else {
                mainContent.style.marginLeft = '0';
            }
        });

        // Auto-hide alerts with smooth animation
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert-notification');
            alerts.forEach(function(alert) {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                alert.style.transition = 'all 0.3s ease-in-out';
                
                setTimeout(() => {
                    alert.style.opacity = '1';
                    alert.style.transform = 'translateY(0)';
                }, 10);

                setTimeout(function() {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-10px)';
                    setTimeout(function() {
                        alert.remove();
                    }, 300);
                }, 5000);
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>
