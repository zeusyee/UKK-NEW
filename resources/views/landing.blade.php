<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mana-Pro – Sistem Manajemen Proyek</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        .float-animation {
            animation: float 3s ease-in-out infinite;
        }
        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-50px); }
            to { opacity: 1; transform: translateX(0); }
        }
        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(50px); }
            to { opacity: 1; transform: translateX(0); }
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .slide-in-left { animation: slideInLeft 0.8s ease-out forwards; }
        .slide-in-right { animation: slideInRight 0.8s ease-out forwards; }
        .fade-in-up { animation: fadeInUp 0.8s ease-out forwards; }
        .gradient-text {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        html {
            scroll-behavior: smooth;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-blue-50 overflow-x-hidden">
    
    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white/90 backdrop-blur-lg shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <div class="bg-blue-800 text-white w-10 h-10 rounded-lg flex items-center justify-center font-bold">
                        MP
                    </div>
                    <div>
                        <h1 class="text-xl font-black bg-gradient-to-r from-blue-800 to-blue-600 bg-clip-text text-transparent">MANA-PRO</h1>
                        <p class="text-xs text-gray-600 font-medium">Manajemen Proyek</p>
                    </div>
                </div>

                <!-- Mobile menu button -->
                <button id="mobile-menu-btn" class="md:hidden text-gray-600 hover:text-blue-800">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>

                <!-- Desktop menu -->
                <div class="hidden md:flex items-center gap-4">
                    <a href="#tentang" class="text-gray-600 hover:text-blue-800 font-medium transition">Tentang</a>
                    <a href="#fitur" class="text-gray-600 hover:text-blue-800 font-medium transition">Fitur</a>
                    <a href="#" class="px-5 py-2 bg-gradient-to-r from-blue-800 to-blue-600 text-white rounded-lg font-semibold hover:shadow-lg transition-all">
                        Masuk
                    </a>
                    <a href="#" class="px-5 py-2 border-2 border-blue-800 text-blue-800 rounded-lg font-semibold hover:bg-blue-50 transition">
                        Daftar
                    </a>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div id="mobile-menu" class="hidden md:hidden border-t border-gray-200">
            <div class="px-4 py-4 space-y-3">
                <a href="#tentang" class="block text-gray-600 hover:text-blue-800 font-medium">Tentang</a>
                <a href="#fitur" class="block text-gray-600 hover:text-blue-800 font-medium">Fitur</a>
                <a href="#" class="block text-center px-5 py-2 bg-gradient-to-r from-blue-800 to-blue-600 text-white rounded-lg font-semibold">
                    Masuk
                </a>
                <a href="#" class="block text-center px-5 py-2 border-2 border-blue-800 text-blue-800 rounded-lg font-semibold">
                    Daftar
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative min-h-screen flex items-center justify-center pt-16 overflow-hidden">
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-40 -left-40 w-80 h-80 bg-blue-200 rounded-full mix-blend-multiply filter blur-xl opacity-30 animate-pulse"></div>
            <div class="absolute -bottom-40 -right-40 w-80 h-80 bg-blue-300 rounded-full mix-blend-multiply filter blur-xl opacity-30 animate-pulse" style="animation-delay: 1s;"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-blue-100 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse" style="animation-delay: 2s;"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-20 relative z-10">
            <div class="grid md:grid-cols-2 gap-8 md:gap-12 items-center">
                <div class="slide-in-left text-center md:text-left">
                    <div class="inline-block px-4 py-2 bg-blue-100 text-blue-800 rounded-full text-xs sm:text-sm font-semibold mb-4 md:mb-6">
                        ✨ Sistem Manajemen Proyek Modern
                    </div>
                    <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 mb-4 md:mb-6 leading-tight">
                        Kelola <span class="gradient-text">Proyek</span> Anda Dengan Mudah
                    </h1>
                    <p class="text-base sm:text-lg md:text-xl text-gray-700 mb-6 md:mb-8 leading-relaxed">
                        Platform manajemen proyek modern dirancang untuk menyederhanakan alur kerja Anda, meningkatkan produktivitas tim, dan menyelesaikan proyek tepat waktu.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3 md:gap-4 justify-center md:justify-start">
                        <a href="#" class="px-6 sm:px-8 py-3 sm:py-4 bg-gradient-to-r from-blue-800 to-blue-600 text-white rounded-xl font-bold text-base sm:text-lg shadow-lg hover:shadow-xl hover:scale-105 transition-all flex items-center justify-center gap-2">
                            <i class="fas fa-rocket"></i>
                            Mulai Gratis
                        </a>
                        <a href="#cara-kerja" class="px-6 sm:px-8 py-3 sm:py-4 bg-white border-2 border-blue-300 text-gray-700 rounded-xl font-bold text-base sm:text-lg hover:border-blue-600 hover:text-blue-800 transition-all text-center">
                            Pelajari Lebih Lanjut
                        </a>
                    </div>
                    
                    <div class="grid grid-cols-3 gap-3 sm:gap-6 mt-8 md:mt-12">
                        <div>
                            <p class="text-xl sm:text-2xl md:text-3xl font-bold text-blue-800">50+</p>
                            <p class="text-gray-600 text-xs sm:text-sm">Pengguna Aktif</p>
                        </div>
                        <div>
                            <p class="text-xl sm:text-2xl md:text-3xl font-bold text-blue-800">100+</p>
                            <p class="text-gray-600 text-xs sm:text-sm">Proyek Selesai</p>
                        </div>
                        <div>
                            <p class="text-xl sm:text-2xl md:text-3xl font-bold text-blue-800">24/7</p>
                            <p class="text-gray-600 text-xs sm:text-sm">Dukungan</p>
                        </div>
                    </div>
                </div>

                <div class="slide-in-right hidden md:block">
                    <div class="relative float-animation">
                        <div class="bg-gradient-to-br from-blue-800 to-blue-600 rounded-3xl p-8 shadow-2xl">
                            <div class="bg-white rounded-2xl p-6 space-y-4">
                                <div class="flex items-center gap-3 border-b pb-4">
                                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-tasks text-blue-800 text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-800">Proyek Website</p>
                                        <p class="text-sm text-gray-500">Tim Development</p>
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Status</span>
                                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">Dalam Progress</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Progress</span>
                                        <span class="text-sm font-semibold text-gray-800">75%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-800 h-2 rounded-full" style="width: 75%"></div>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Deadline</span>
                                        <span class="text-sm text-gray-800">15 Desember 2025</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Tentang Section -->
    <section id="tentang" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 fade-in-up">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Tentang Mana-Pro</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Mana-Pro adalah platform manajemen proyek yang dirancang untuk membantu tim berkolaborasi secara efisien, melacak kemajuan, dan menyelesaikan proyek tepat waktu.
                </p>
            </div>

            <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-6 md:gap-8">
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-8 hover:shadow-xl transition-all fade-in-up">
                    <div class="w-14 h-14 bg-gradient-to-r from-blue-800 to-blue-600 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-check-circle text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Mudah & Cepat</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Buat proyek dan kelola tugas hanya dalam hitungan menit. Interface intuitif memudahkan seluruh tim.
                    </p>
                </div>

                <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl p-8 hover:shadow-xl transition-all fade-in-up" style="animation-delay: 0.2s;">
                    <div class="w-14 h-14 bg-gradient-to-r from-blue-800 to-cyan-600 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-users text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Kolaborasi Tim</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Undang anggota tim, tetapkan tugas, dan berkolaborasi secara real-time dalam satu platform.
                    </p>
                </div>

                <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-8 hover:shadow-xl transition-all fade-in-up" style="animation-delay: 0.4s;">
                    <div class="w-14 h-14 bg-gradient-to-r from-green-600 to-emerald-500 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-chart-line text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Pelacakan Kemajuan</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Pantau progress proyek dengan dashboard analitik dan dapatkan insight terperinci.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Fitur Section -->
    <section id="fitur" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Fitur Unggulan</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Semua yang Anda butuhkan untuk mengelola proyek secara efisien
                </p>
            </div>

            <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-6 md:gap-8">
                <div class="bg-white rounded-2xl p-8 shadow-md hover:shadow-xl transition-all">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-blue-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Papan Kanban</h3>
                            <p class="text-gray-600">Visualisasikan alur kerja dengan papan Kanban interaktif. Drag & drop untuk mengelola prioritas.</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-8 shadow-md hover:shadow-xl transition-all">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-check-circle text-blue-800 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Manajemen Subtask</h3>
                            <p class="text-gray-600">Pecahkan tugas kompleks menjadi subtask yang dapat dikelola dengan mudah.</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-8 shadow-md hover:shadow-xl transition-all">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-users text-blue-800 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Kolaborasi Tim</h3>
                            <p class="text-gray-600">Undang anggota tim, tetapkan tugas, dan berkolaborasi secara real-time.</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-8 shadow-md hover:shadow-xl transition-all">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-hourglass-half text-blue-800 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Pelacakan Waktu</h3>
                            <p class="text-gray-600">Timer bawaan untuk melacak waktu yang dihabiskan untuk setiap tugas.</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-8 shadow-md hover:shadow-xl transition-all">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-chart-bar text-blue-800 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Analitik Kemajuan</h3>
                            <p class="text-gray-600">Dapatkan wawasan terperinci dengan dashboard analitik proyek.</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-8 shadow-md hover:shadow-xl transition-all">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-lock text-blue-800 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Akses Berbasis Peran</h3>
                            <p class="text-gray-600">Kelola izin dengan peran admin, pemimpin, dan anggota tim.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-r from-blue-800 via-blue-700 to-blue-600">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl md:text-5xl font-bold text-white mb-6">
                Siap Mengubah Manajemen Proyek Anda?
            </h2>
            <p class="text-xl text-blue-100 mb-10 leading-relaxed">
                Bergabunglah dengan ribuan tim yang sudah menggunakan Mana-Pro untuk menyelesaikan proyek lebih efisien.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="#" class="px-10 py-4 bg-white text-blue-800 rounded-xl font-bold text-lg shadow-lg hover:shadow-2xl hover:scale-105 transition-all">
                    <i class="fas fa-rocket mr-2"></i>
                    Mulai Gratis
                </a>
                <a href="#" class="px-10 py-4 bg-blue-900 text-white rounded-xl font-bold text-lg border-2 border-white hover:bg-blue-950 transition-all">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Masuk ke Akun
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <div class="md:col-span-2">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="bg-blue-800 text-white w-8 h-8 rounded-lg flex items-center justify-center text-sm font-bold">
                            MP
                        </div>
                        <h3 class="text-xl font-black bg-gradient-to-r from-blue-400 to-blue-300 bg-clip-text text-transparent">MANA-PRO</h3>
                    </div>
                    <p class="text-gray-400 mb-4">
                        Platform manajemen proyek modern untuk menyederhanakan alur kerja, meningkatkan produktivitas tim, dan menyelesaikan proyek tepat waktu.
                    </p>
                </div>

                <div>
                    <h4 class="text-white font-bold mb-4">Menu</h4>
                    <ul class="space-y-2">
                        <li><a href="#tentang" class="hover:text-blue-400 transition">Tentang</a></li>
                        <li><a href="#fitur" class="hover:text-blue-400 transition">Fitur</a></li>
                        <li><a href="#" class="hover:text-blue-400 transition">Masuk</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-white font-bold mb-4">Kontak</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><i class="fas fa-envelope mr-2"></i>info@manapro.id</li>
                        <li><i class="fas fa-phone mr-2"></i>(021) 1234-5678</li>
                        <li><i class="fas fa-map-marker-alt mr-2"></i>Jakarta, Indonesia</li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 pt-8">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <p class="text-gray-500 mb-4 md:mb-0">© 2025 Mana-Pro. Semua hak dilindungi.</p>
                    <div class="flex space-x-6">
                        <a href="#" class="text-gray-400 hover:text-blue-400 transition"><i class="fab fa-facebook text-xl"></i></a>
                        <a href="#" class="text-gray-400 hover:text-blue-400 transition"><i class="fab fa-twitter text-xl"></i></a>
                        <a href="#" class="text-gray-400 hover:text-blue-400 transition"><i class="fab fa-linkedin text-xl"></i></a>
                        <a href="#" class="text-gray-400 hover:text-blue-400 transition"><i class="fab fa-instagram text-xl"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    // Close mobile menu if open
                    document.getElementById('mobile-menu').classList.add('hidden');
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Mobile menu toggle
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        
        if (mobileMenuBtn && mobileMenu) {
            mobileMenuBtn.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden');
            });
        }

        // Intersection Observer for animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -100px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        document.querySelectorAll('.fade-in-up').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            el.style.transition = 'opacity 0.8s ease-out, transform 0.8s ease-out';
            observer.observe(el);
        });
    </script>
</body>
</html>