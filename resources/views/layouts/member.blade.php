<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member - @yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .nav-link {
            position: relative;
            transition: all 0.3s ease;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 3px;
            background: linear-gradient(to right, #3B82F6, #1D4ED8);
            transition: width 0.3s ease;
            border-radius: 2px;
        }
        .nav-link:hover::after,
        .nav-link.active::after {
            width: 100%;
        }
        .nav-link.active {
            color: #1D4ED8;
            font-weight: 600;
        }
        .dropdown-menu {
            display: none;
            opacity: 0;
            transform: translateY(-10px);
            transition: all 0.3s ease;
        }
        .dropdown-menu.show {
            display: block;
            opacity: 1;
            transform: translateY(0);
        }
        @media (max-width: 768px) {
            .mobile-menu {
                max-height: 0;
                overflow: hidden;
                transition: max-height 0.3s ease;
            }
            .mobile-menu.show {
                max-height: 500px;
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation Bar -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo and Brand -->
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-lg p-2 shadow-md">
                            <i class="fas fa-user-circle text-white text-2xl"></i>
                        </div>
                        <span class="ml-3 text-xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                            Member Panel
                        </span>
                    </div>
                    
                    <!-- Desktop Navigation Links -->
                    <div class="hidden md:ml-10 md:flex md:space-x-8">
                        <a href="{{ route('member.dashboard') }}" 
                           class="nav-link inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 {{ request()->routeIs('member.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-home mr-2"></i>
                            Dashboard
                        </a>
                        <a href="{{ route('member.my-tasks') }}" 
                           class="nav-link inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 {{ request()->routeIs('member.my-tasks') ? 'active' : '' }}">
                            <i class="fas fa-tasks mr-2"></i>
                            My Tasks
                        </a>
                    </div>
                </div>

                <!-- Right Side: User Info & Actions -->
                <div class="hidden md:flex md:items-center md:space-x-4">
                    <!-- User Profile Dropdown -->
                    <div class="relative" id="user-dropdown">
                        <button type="button" 
                                class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500"
                                onclick="toggleDropdown()">
                            <div class="text-right">
                                <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->full_name }}</p>
                                <span class="inline-block px-2 py-0.5 text-xs bg-blue-100 text-blue-800 rounded-full font-medium">
                                    <i class="fas fa-user mr-1"></i>Member
                                </span>
                            </div>
                            <i class="fas fa-chevron-down text-gray-500 text-sm"></i>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div class="dropdown-menu absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border border-gray-200 py-1">
                            <div class="px-4 py-3 border-b border-gray-200">
                                <p class="text-sm font-medium text-gray-900">{{ Auth::user()->full_name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                            </div>
                            <form method="POST" action="{{ route('logout') }}" class="py-1">
                                @csrf
                                <button type="submit" 
                                        class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors flex items-center">
                                    <i class="fas fa-sign-out-alt mr-2"></i>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Mobile menu button -->
                <div class="flex items-center md:hidden">
                    <button type="button" 
                            class="inline-flex items-center justify-center p-2 rounded-lg text-gray-700 hover:text-blue-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500"
                            onclick="toggleMobileMenu()">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div class="mobile-menu md:hidden bg-white border-t border-gray-200" id="mobile-menu">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="{{ route('member.dashboard') }}" 
                   class="block px-3 py-2 rounded-lg text-base font-medium {{ request()->routeIs('member.dashboard') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-100 hover:text-blue-600' }}">
                    <i class="fas fa-home mr-2"></i>
                    Dashboard
                </a>
                <a href="{{ route('member.my-tasks') }}" 
                   class="block px-3 py-2 rounded-lg text-base font-medium {{ request()->routeIs('member.my-tasks') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-100 hover:text-blue-600' }}">
                    <i class="fas fa-tasks mr-2"></i>
                    My Tasks
                </a>
            </div>
            
            <!-- Mobile User Info -->
            <div class="pt-4 pb-3 border-t border-gray-200">
                <div class="px-4 mb-3">
                    <p class="text-sm font-medium text-gray-900">{{ Auth::user()->full_name }}</p>
                    <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                    <span class="inline-block mt-2 px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full font-medium">
                        <i class="fas fa-user mr-1"></i>Member
                    </span>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="px-2">
                    @csrf
                    <button type="submit" 
                            class="w-full text-left px-3 py-2 rounded-lg text-base font-medium text-red-600 hover:bg-red-50 transition-colors flex items-center">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
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

    <script>
        // Toggle mobile menu
        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('show');
        }

        // Toggle user dropdown
        function toggleDropdown() {
            const dropdown = document.querySelector('.dropdown-menu');
            dropdown.classList.toggle('show');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('user-dropdown');
            if (dropdown && !dropdown.contains(event.target)) {
                document.querySelector('.dropdown-menu')?.classList.remove('show');
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
