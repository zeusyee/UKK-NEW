<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        :root {
            --primary: #1e40af;
            --primary-dark: #1e3a8a;
        }
        
        * {
            font-family: 'Inter', sans-serif;
        }
        
        body {
            background: #f3f4f6;
            min-height: 100vh;
        }
        
        .login-card {
            background: white;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .input-field {
            transition: all 0.2s ease;
            border-color: #e5e7eb;
        }
        
        .input-field:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.1);
        }
        
        .btn-primary {
            background: var(--primary);
            transition: all 0.2s ease;
        }
        
        .btn-primary:hover {
            background: var(--primary-dark);
        }
        
        .alert-notification {
            animation: slideDown 0.3s ease-out;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="max-w-md w-full space-y-8 p-8 login-card rounded-lg">
            <!-- Logo/Icon -->
            <div class="text-center">
                <div class="mx-auto w-12 h-12 rounded flex items-center justify-center mb-4" style="background: var(--primary);">
                    <i class="fas fa-lock text-white text-lg"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-1">
                    Welcome Back
                </h2>
                <p class="text-sm text-gray-600">Sign in to your account</p>
            </div>

            <form class="mt-8 space-y-6" action="{{ route('login') }}" method="POST">
                @csrf
                
                @if ($errors->any())
                <div class="alert-notification bg-red-50 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-circle text-red-500 mt-1 mr-3 flex-shrink-0"></i>
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li class="text-sm">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <button onclick="this.parentElement.parentElement.remove()" class="text-red-500 hover:text-red-700 ml-4 transition flex-shrink-0">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                @endif

                <div class="space-y-4">
                    <!-- Username Field -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user text-gray-400 text-sm"></i>
                        </div>
                        <input id="username" name="username" type="text" required 
                            class="input-field appearance-none block w-full pl-10 pr-3 py-2 border rounded text-gray-900 placeholder-gray-400 text-sm focus:outline-none" 
                            placeholder="Username"
                            value="{{ old('username') }}">
                    </div>

                    <!-- Password Field -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400 text-sm"></i>
                        </div>
                        <input id="password" name="password" type="password" required 
                            class="input-field appearance-none block w-full pl-10 pr-10 py-2 border rounded text-gray-900 placeholder-gray-400 text-sm focus:outline-none" 
                            placeholder="Password">
                        <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <i id="toggleIcon" class="fas fa-eye text-gray-400 hover:text-gray-600 transition text-sm"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between pt-2">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox" 
                            class="h-4 w-4 rounded border-gray-300 text-blue-800 focus:ring-blue-800" style="accent-color: var(--primary);">
                        <label for="remember" class="ml-2 block text-xs text-gray-700">
                            Remember me
                        </label>
                    </div>
                    <div class="text-xs">
                        <a href="#" class="font-medium transition" style="color: var(--primary);">
                            Forgot password?
                        </a>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" 
                        class="btn-primary group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-semibold rounded text-white focus:outline-none focus:ring-2 focus:ring-offset-2" style="--tw-ring-color: var(--primary);">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fas fa-sign-in-alt text-white opacity-75 text-xs"></i>
                        </span>
                        Sign in
                    </button>
                </div>

                <!-- Register Link -->
                <div class="text-center pt-2">
                    <p class="text-xs text-gray-600">
                        Don't have an account? 
                        <a href="{{ route('register') }}" class="font-semibold transition" style="color: var(--primary);">
                            Create account
                        </a>
                    </p>
                </div>
            </form>

            <!-- Social Login -->
            <div class="pt-4">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle Password Visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // Auto-hide alerts after 5 seconds with fade out animation
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert-notification');
            alerts.forEach(function(alert) {
                // Auto-hide after 5 seconds
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
</body>
</html>