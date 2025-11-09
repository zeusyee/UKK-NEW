<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Manajemen Proyek - Kelola Pekerjaan Anda Dengan Efisien</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #1e40af; /* blue-800 */
            --primary-dark: #1e3a8a; /* blue-900 */
        }
        
        .bg-primary {
            background-color: var(--primary);
        }
        
        .text-primary {
            color: var(--primary);
        }
        
        .hover\:bg-primary:hover {
            background-color: var(--primary);
        }
        
        html {
            scroll-behavior: smooth;
        }
        
        .hero-gradient {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        }
        
        .feature-card {
            transition: all 0.3s ease;
        }
        
        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(30, 64, 175, 0.1);
        }
    </style>
</head>
<body class="bg-white">
    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 bg-white shadow-md z-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-3">
                    <div class="bg-primary text-white w-10 h-10 rounded-lg flex items-center justify-center font-bold">
                        PM
                    </div>
                    <span class="font-bold text-lg text-gray-800">ProjectHub</span>
                </div>
                
                <div class="hidden md:flex items-center space-x-6">
                    <a href="#features" class="text-gray-600 hover:text-primary transition-colors">Fitur</a>
                    <a href="#stats" class="text-gray-600 hover:text-primary transition-colors">Statistik</a>
                    <a href="#pricing" class="text-gray-600 hover:text-primary transition-colors">Cara Kerja</a>
                    <a href="{{ route('login') }}" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-opacity-90 transition-all">
                        Masuk
                    </a>
                </div>

                <div class="md:hidden">
                    <a href="{{ route('login') }}" class="px-4 py-2 bg-primary text-white rounded-lg text-sm hover:bg-opacity-90">
                        Masuk
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-gradient text-white pt-32 pb-20 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <!-- Left Side - Text -->
                <div>
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight">
                        Kelola <span class="text-yellow-300">Proyek</span> Anda Dengan Mudah
                    </h1>
                    <p class="text-lg md:text-xl text-blue-100 mb-8">
                        Platform manajemen proyek modern dirancang untuk menyederhanakan alur kerja Anda, meningkatkan produktivitas tim, dan menyelesaikan proyek tepat waktu.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('register') }}" class="px-8 py-3 bg-yellow-300 text-blue-900 font-bold rounded-lg hover:bg-yellow-200 transition-all text-center shadow-lg hover:shadow-xl transform hover:scale-105">
                            <i class="fas fa-rocket mr-2"></i>Mulai Gratis
                        </a>
                        <a href="{{ route('login') }}" class="px-8 py-3 bg-white bg-opacity-20 text-white font-bold rounded-lg hover:bg-opacity-30 transition-all text-center border-2 border-white">
                            <i class="fas fa-arrow-right mr-2"></i>Masuk
                        </a>
                    </div>
                </div>

                <!-- Right Side - Illustration -->
                <div class="hidden md:block">
                    <div class="bg-white bg-opacity-10 rounded-xl p-8 backdrop-blur-sm border border-white border-opacity-20">
                        <div class="space-y-4">
                            <div class="flex items-center space-x-3 p-3 bg-white bg-opacity-10 rounded-lg">
                                <div class="w-12 h-12 bg-yellow-300 rounded-lg flex items-center justify-center text-blue-900">
                                    <i class="fas fa-tasks text-xl font-bold"></i>
                                </div>
                                <div class="text-white">
                                    <p class="font-semibold">Manajemen Tugas</p>
                                    <p class="text-sm text-blue-100">Atur semua pekerjaan Anda</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3 p-3 bg-white bg-opacity-10 rounded-lg">
                                <div class="w-12 h-12 bg-green-300 rounded-lg flex items-center justify-center text-blue-900">
                                    <i class="fas fa-users text-xl font-bold"></i>
                                </div>
                                <div class="text-white">
                                    <p class="font-semibold">Kolaborasi Tim</p>
                                    <p class="text-sm text-blue-100">Bekerja bersama dengan lancar</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3 p-3 bg-white bg-opacity-10 rounded-lg">
                                <div class="w-12 h-12 bg-purple-300 rounded-lg flex items-center justify-center text-blue-900">
                                    <i class="fas fa-chart-line text-xl font-bold"></i>
                                </div>
                                <div class="text-white">
                                    <p class="font-semibold">Pelacakan Kemajuan</p>
                                    <p class="text-sm text-blue-100">Pantau pembaruan real-time</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 px-4 sm:px-6 lg:px-8 bg-gray-50">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Fitur Unggulan</h2>
                <p class="text-lg text-gray-600">Semua yang Anda butuhkan untuk mengelola proyek secara efisien</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="feature-card bg-white rounded-xl p-8 shadow-md border border-gray-100">
                    <div class="bg-primary text-white w-16 h-16 rounded-lg flex items-center justify-center mb-4 text-2xl">
                        <i class="fas fa-kanban"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Papan Kanban</h3>
                    <p class="text-gray-600">
                        Visualisasikan alur kerja Anda dengan papan Kanban interaktif. Seret dan lepas tugas dengan mudah untuk mengelola prioritas.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="feature-card bg-white rounded-xl p-8 shadow-md border border-gray-100">
                    <div class="bg-primary text-white w-16 h-16 rounded-lg flex items-center justify-center mb-4 text-2xl">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Manajemen Subtask</h3>
                    <p class="text-gray-600">
                        Pecahkan tugas kompleks menjadi subtask yang dapat dikelola. Lacak kemajuan dengan presisi dan pastikan tidak ada yang terlewat.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="feature-card bg-white rounded-xl p-8 shadow-md border border-gray-100">
                    <div class="bg-primary text-white w-16 h-16 rounded-lg flex items-center justify-center mb-4 text-2xl">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Kolaborasi Tim</h3>
                    <p class="text-gray-600">
                        Undang anggota tim, tetapkan tugas, dan berkolaborasi secara real-time. Jaga semua orang tetap berada di halaman yang sama.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="feature-card bg-white rounded-xl p-8 shadow-md border border-gray-100">
                    <div class="bg-primary text-white w-16 h-16 rounded-lg flex items-center justify-center mb-4 text-2xl">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Pelacakan Waktu</h3>
                    <p class="text-gray-600">
                        Timer bawaan untuk melacak waktu yang dihabiskan untuk tugas. Bandingkan jam perkiraan vs. jam aktual untuk perencanaan yang lebih baik.
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="feature-card bg-white rounded-xl p-8 shadow-md border border-gray-100">
                    <div class="bg-primary text-white w-16 h-16 rounded-lg flex items-center justify-center mb-4 text-2xl">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Analitik Kemajuan</h3>
                    <p class="text-gray-600">
                        Dapatkan wawasan terperinci dengan pelacakan kemajuan, tingkat penyelesaian, dan dasbor analitik proyek.
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="feature-card bg-white rounded-xl p-8 shadow-md border border-gray-100">
                    <div class="bg-primary text-white w-16 h-16 rounded-lg flex items-center justify-center mb-4 text-2xl">
                        <i class="fas fa-lock"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Akses Berbasis Peran</h3>
                    <p class="text-gray-600">
                        Kelola izin dengan peran admin, pemimpin, dan anggota. Kontrol siapa yang dapat melakukan apa di seluruh proyek Anda.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section id="stats" class="py-20 px-4 sm:px-6 lg:px-8 bg-white">
        <div class="max-w-6xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Stat 1 -->
                <div class="text-center p-6 bg-blue-50 rounded-xl border border-blue-100">
                    <div class="text-4xl font-bold text-primary mb-2">{{ $stats['total_users'] }}</div>
                    <p class="text-gray-600 font-semibold">Pengguna Aktif</p>
                    <p class="text-sm text-gray-500 mt-2">Tim berkolaborasi bersama</p>
                </div>

                <!-- Stat 2 -->
                <div class="text-center p-6 bg-green-50 rounded-xl border border-green-100">
                    <div class="text-4xl font-bold text-green-700 mb-2">{{ $stats['total_projects'] }}</div>
                    <p class="text-gray-600 font-semibold">Total Proyek</p>
                    <p class="text-sm text-gray-500 mt-2">Proyek dikelola dengan sukses</p>
                </div>

                <!-- Stat 3 -->
                <div class="text-center p-6 bg-purple-50 rounded-xl border border-purple-100">
                    <div class="text-4xl font-bold text-purple-700 mb-2">{{ $stats['completed_projects'] }}</div>
                    <p class="text-gray-600 font-semibold">Selesai</p>
                    <p class="text-sm text-gray-500 mt-2">Proyek disampaikan tepat waktu</p>
                </div>

                <!-- Stat 4 -->
                <div class="text-center p-6 bg-orange-50 rounded-xl border border-orange-100">
                    <div class="text-4xl font-bold text-orange-700 mb-2">{{ $stats['active_projects'] }}</div>
                    <p class="text-gray-600 font-semibold">Sedang Berlangsung</p>
                    <p class="text-sm text-gray-500 mt-2">Proyek yang sedang aktif</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="pricing" class="py-20 px-4 sm:px-6 lg:px-8 bg-gray-50">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Cara Kerjanya</h2>
                <p class="text-lg text-gray-600">Mulai dalam 4 langkah sederhana</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Step 1 -->
                <div class="relative">
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 bg-primary text-white rounded-full flex items-center justify-center text-2xl font-bold mb-4">
                            1
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2 text-center">Buat Akun</h3>
                        <p class="text-gray-600 text-center text-sm">Daftar dan atur akun Anda dalam hitungan menit</p>
                    </div>
                    <div class="hidden md:block absolute top-8 left-[50%] w-[calc(100%+2rem)] h-1 bg-gray-300 -z-10" style="left: calc(50% + 2rem); width: calc(100% + 1rem);"></div>
                </div>

                <!-- Step 2 -->
                <div class="relative">
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 bg-primary text-white rounded-full flex items-center justify-center text-2xl font-bold mb-4">
                            2
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2 text-center">Buat Proyek</h3>
                        <p class="text-gray-600 text-center text-sm">Siapkan proyek pertama Anda dan organisir pekerjaan Anda</p>
                    </div>
                    <div class="hidden md:block absolute top-8 left-[50%] w-[calc(100%+2rem)] h-1 bg-gray-300 -z-10" style="left: calc(50% + 2rem); width: calc(100% + 1rem);"></div>
                </div>

                <!-- Step 3 -->
                <div class="relative">
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 bg-primary text-white rounded-full flex items-center justify-center text-2xl font-bold mb-4">
                            3
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2 text-center">Undang Tim</h3>
                        <p class="text-gray-600 text-center text-sm">Tambahkan anggota tim dan tetapkan tugas kepada mereka</p>
                    </div>
                    <div class="hidden md:block absolute top-8 left-[50%] w-[calc(100%+2rem)] h-1 bg-gray-300 -z-10" style="left: calc(50% + 2rem); width: calc(100% + 1rem);"></div>
                </div>

                <!-- Step 4 -->
                <div class="flex flex-col items-center">
                    <div class="w-16 h-16 bg-primary text-white rounded-full flex items-center justify-center text-2xl font-bold mb-4">
                        4
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2 text-center">Lacak & Kirim</h3>
                    <p class="text-gray-600 text-center text-sm">Pantau kemajuan dan serahkan proyek tepat waktu</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="hero-gradient text-white py-20 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-6">Siap Mengubah Manajemen Proyek Anda?</h2>
            <p class="text-lg text-blue-100 mb-8">
                Bergabunglah dengan ribuan tim yang sudah menggunakan ProjectHub untuk memberikan proyek secara lebih efisien.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}" class="px-8 py-3 bg-yellow-300 text-blue-900 font-bold rounded-lg hover:bg-yellow-200 transition-all shadow-lg hover:shadow-xl transform hover:scale-105">
                    <i class="fas fa-user-plus mr-2"></i>Buat Akun Gratis
                </a>
                <a href="{{ route('login') }}" class="px-8 py-3 bg-white bg-opacity-20 text-white font-bold rounded-lg hover:bg-opacity-30 transition-all border-2 border-white">
                    <i class="fas fa-sign-in-alt mr-2"></i>Masuk
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <!-- Brand -->
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="bg-primary text-white w-8 h-8 rounded-lg flex items-center justify-center text-sm font-bold">
                            PM
                        </div>
                        <span class="font-bold text-white">ProjectHub</span>
                    </div>
                    <p class="text-sm">Sederhanakan alur kerja Anda dan tingkatkan produktivitas tim.</p>
                </div>

                <!-- Links -->
                <div>
                    <h4 class="text-white font-semibold mb-4">Produk</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#features" class="hover:text-white transition-colors">Fitur</a></li>
                        <li><a href="#stats" class="hover:text-white transition-colors">Statistik</a></li>
                        <li><a href="#pricing" class="hover:text-white transition-colors">Cara Kerja</a></li>
                    </ul>
                </div>

                <!-- Company -->
                <div>
                    <h4 class="text-white font-semibold mb-4">Perusahaan</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-white transition-colors">Tentang Kami</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Kontak</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Blog</a></li>
                    </ul>
                </div>

                <!-- Legal -->
                <div>
                    <h4 class="text-white font-semibold mb-4">Hukum</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-white transition-colors">Kebijakan Privasi</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Syarat Layanan</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Kebijakan Cookie</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 pt-8">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <p class="text-sm mb-4 md:mb-0">&copy; 2025 ProjectHub. Semua hak dilindungi.</p>
                    <div class="flex space-x-6">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors"><i class="fab fa-facebook text-xl"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors"><i class="fab fa-twitter text-xl"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors"><i class="fab fa-linkedin text-xl"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
