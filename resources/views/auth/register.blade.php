<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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

        .progress-bar {
            height: 4px;
            background: #e5e7eb;
            border-radius: 2px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            transition: width 0.3s ease, background-color 0.3s ease;
        }
    </style>
</head>
<body>
    <div class="min-h-screen flex items-center justify-center p-4 py-12">
        <div class="max-w-md w-full space-y-8 p-8 login-card rounded-lg">
            <!-- Logo/Icon -->
            <div class="text-center">
                <div class="mx-auto w-12 h-12 rounded flex items-center justify-center mb-4" style="background: var(--primary);">
                    <i class="fas fa-user-plus text-white text-lg"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-1">
                    Create Account
                </h2>
                <p class="text-sm text-gray-600">Join us today and get started</p>
            </div>

            <form class="mt-8 space-y-6" action="{{ route('register') }}" method="POST">
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

                    <!-- Full Name Field -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-id-card text-gray-400 text-sm"></i>
                        </div>
                        <input id="full_name" name="full_name" type="text" required 
                            class="input-field appearance-none block w-full pl-10 pr-3 py-2 border rounded text-gray-900 placeholder-gray-400 text-sm focus:outline-none" 
                            placeholder="Full Name"
                            value="{{ old('full_name') }}">
                    </div>

                    <!-- Email Field -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400 text-sm"></i>
                        </div>
                        <input id="email" name="email" type="email" required 
                            class="input-field appearance-none block w-full pl-10 pr-3 py-2 border rounded text-gray-900 placeholder-gray-400 text-sm focus:outline-none" 
                            placeholder="Email Address"
                            value="{{ old('email') }}">
                    </div>

                    <!-- Password Field -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400 text-sm"></i>
                        </div>
                        <input id="password" name="password" type="password" required 
                            class="input-field appearance-none block w-full pl-10 pr-10 py-2 border rounded text-gray-900 placeholder-gray-400 text-sm focus:outline-none" 
                            placeholder="Password"
                            oninput="checkPasswordStrength()">
                        <button type="button" onclick="togglePassword('password', 'toggleIcon1')" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <i id="toggleIcon1" class="fas fa-eye text-gray-400 hover:text-gray-600 transition text-sm"></i>
                        </button>
                    </div>

                    <!-- Password Strength Indicator -->
                    <div class="space-y-2">
                        <div class="progress-bar">
                            <div id="strengthBar" class="progress-fill" style="width: 0%; background: #e5e7eb;"></div>
                        </div>
                        <p id="strengthText" class="text-xs text-gray-500"></p>
                    </div>

                    <!-- Confirm Password Field -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400 text-sm"></i>
                        </div>
                        <input id="password_confirmation" name="password_confirmation" type="password" required 
                            class="input-field appearance-none block w-full pl-10 pr-10 py-2 border rounded text-gray-900 placeholder-gray-400 text-sm focus:outline-none" 
                            placeholder="Confirm Password">
                        <button type="button" onclick="togglePassword('password_confirmation', 'toggleIcon2')" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <i id="toggleIcon2" class="fas fa-eye text-gray-400 hover:text-gray-600 transition text-sm"></i>
                        </button>
                    </div>
                </div>

                <!-- Terms and Conditions -->
                <div class="flex items-start pt-2">
                    <div class="flex items-center h-5">
                        <input id="terms" name="terms" type="checkbox" required
                            class="h-4 w-4 rounded border-gray-300" style="accent-color: var(--primary);">
                    </div>
                    <div class="ml-3 text-xs">
                        <label for="terms" class="text-gray-600">
                            I agree to the 
                            <a href="#" class="font-medium transition" style="color: var(--primary);">Terms</a> 
                            and 
                            <a href="#" class="font-medium transition" style="color: var(--primary);">Privacy Policy</a>
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" 
                        class="btn-primary group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-semibold rounded text-white focus:outline-none focus:ring-2 focus:ring-offset-2" style="--tw-ring-color: var(--primary);">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fas fa-user-plus text-white opacity-75 text-xs"></i>
                        </span>
                        Create Account
                    </button>
                </div>

                <!-- Login Link -->
                <div class="text-center pt-2">
                    <p class="text-xs text-gray-600">
                        Already have an account? 
                        <a href="{{ route('login') }}" class="font-semibold transition" style="color: var(--primary);">
                            Sign in
                        </a>
                    </p>
                </div>
            </form>

            <!-- Social Register -->
            <div class="pt-4">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-xs">
                        <span class="px-2 bg-white text-gray-500">Or register with</span>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('auth.google') }}" class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                        <i class="fab fa-google text-red-500 mr-2"></i>
                        Google
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle Password Visibility
        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = document.getElementById(iconId);
            
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

        // Password Strength Checker
        function checkPasswordStrength() {
            const password = document.getElementById('password').value;
            const strengthBar = document.getElementById('strengthBar');
            const strengthText = document.getElementById('strengthText');
            
            let strength = 0;
            let feedback = '';
            
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
            if (password.match(/\d/)) strength++;
            if (password.match(/[^a-zA-Z\d]/)) strength++;
            
            switch(strength) {
                case 0:
                case 1:
                    strengthBar.style.width = '25%';
                    strengthBar.style.backgroundColor = '#ef4444';
                    feedback = 'Weak password';
                    break;
                case 2:
                    strengthBar.style.width = '50%';
                    strengthBar.style.backgroundColor = '#f59e0b';
                    feedback = 'Fair password';
                    break;
                case 3:
                    strengthBar.style.width = '75%';
                    strengthBar.style.backgroundColor = '#1e40af';
                    feedback = 'Good password';
                    break;
                case 4:
                    strengthBar.style.width = '100%';
                    strengthBar.style.backgroundColor = '#10b981';
                    feedback = 'Strong password';
                    break;
            }
            
            strengthText.textContent = password.length > 0 ? feedback : '';
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