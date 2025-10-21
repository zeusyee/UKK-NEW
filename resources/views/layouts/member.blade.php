<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Dashboard - @yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="bg-blue-800 text-white w-64 space-y-6 py-7 px-2 absolute inset-y-0 left-0 transform -translate-x-full md:relative md:translate-x-0 transition duration-200 ease-in-out">
            <div class="flex items-center space-x-2 px-4">
                <span class="text-2xl font-extrabold">Member Panel</span>
            </div>

            <nav class="mt-10">
                <a href="{{ route('member.dashboard') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-blue-700 hover:text-white">
                    <i class="fas fa-home mr-2"></i>Dashboard
                </a>
                <form method="POST" action="{{ route('logout') }}" class="block">
                    @csrf
                    <button type="submit" class="w-full text-left py-2.5 px-4 rounded transition duration-200 hover:bg-blue-700 hover:text-white">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </button>
                </form>
            </nav>
        </div>

        <!-- Content -->
        <div class="flex-1">
            <!-- Top Navigation -->
            <div class="bg-white shadow-md">
                <div class="container mx-auto px-6 py-4">
                    <div class="flex items-center justify-between">
                        <button class="md:hidden text-gray-500 hover:text-gray-600">
                            <i class="fas fa-bars text-2xl"></i>
                        </button>
                        <div class="text-right">
                            <span class="text-gray-800">{{ Auth::user()->name }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="container mx-auto px-6 py-8">
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <script>
        // Mobile menu toggle
        document.querySelector('button').addEventListener('click', () => {
            const sidebar = document.querySelector('.bg-blue-800');
            sidebar.classList.toggle('-translate-x-full');
        });
    </script>
</body>
</html>