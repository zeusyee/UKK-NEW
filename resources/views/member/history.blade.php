@extends('layouts.member')

@section('title', 'Project History')

@section('content')
    <!-- Page Header -->
    <div class="mb-6 sm:mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-history text-purple-600 mr-3"></i>
                    Riwayat Proyek Saya
                </h1>
                <p class="text-gray-600 mt-2 text-sm sm:text-base">Semua proyek yang pernah saya kerjakan dan telah diselesaikan</p>
            </div>
            <a href="{{ route('member.dashboard') }}" class="inline-flex items-center bg-blue-500 hover:bg-blue-600 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg shadow-lg transition-all duration-300 text-sm sm:text-base">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6 mb-6 sm:mb-8">
        <!-- Total Completed Projects -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-4 sm:p-6 text-white">
            <div class="flex flex-col">
                <p class="text-green-100 text-xs sm:text-sm font-medium mb-1">Proyek Selesai</p>
                <p class="text-3xl sm:text-4xl font-bold">{{ $completedProjects->count() }}</p>
                <p class="text-green-100 text-xs mt-1">
                    <i class="fas fa-trophy mr-1"></i>Completed
                </p>
            </div>
        </div>

        <!-- Total Tasks -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-4 sm:p-6 text-white">
            <div class="flex flex-col">
                <p class="text-blue-100 text-xs sm:text-sm font-medium mb-1">Total Tugas</p>
                <p class="text-3xl sm:text-4xl font-bold">{{ $totalTasks }}</p>
                <p class="text-blue-100 text-xs mt-1">
                    <i class="fas fa-tasks mr-1"></i>Tasks
                </p>
            </div>
        </div>

        <!-- Tasks Completed -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-4 sm:p-6 text-white">
            <div class="flex flex-col">
                <p class="text-purple-100 text-xs sm:text-sm font-medium mb-1">Tugas Selesai</p>
                <p class="text-3xl sm:text-4xl font-bold">{{ $completedTasks }}</p>
                <p class="text-purple-100 text-xs mt-1">
                    <i class="fas fa-check-circle mr-1"></i>Done
                </p>
            </div>
        </div>

        <!-- Completion Rate -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-4 sm:p-6 text-white">
            <div class="flex flex-col">
                <p class="text-orange-100 text-xs sm:text-sm font-medium mb-1">Tingkat Selesai</p>
                <p class="text-3xl sm:text-4xl font-bold">{{ $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0 }}%</p>
                <p class="text-orange-100 text-xs mt-1">
                    <i class="fas fa-percentage mr-1"></i>Success Rate
                </p>
            </div>
        </div>
    </div>

    <!-- Completed Projects List -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-purple-500 to-indigo-600 p-4 sm:p-6">
            <h2 class="text-lg sm:text-xl font-bold text-white flex items-center">
                <i class="fas fa-list mr-3"></i>
                Daftar Proyek yang Telah Diselesaikan
            </h2>
        </div>

        <div class="p-4 sm:p-6">
            @if($completedProjects->isEmpty())
                <div class="text-center py-8 sm:py-12">
                    <i class="fas fa-history text-gray-300 text-5xl sm:text-6xl mb-4"></i>
                    <h3 class="text-lg sm:text-xl font-semibold text-gray-700 mb-2">Belum Ada Riwayat</h3>
                    <p class="text-sm sm:text-base text-gray-500">Anda belum memiliki proyek yang diselesaikan</p>
                </div>
            @else
                <!-- Search Bar -->
                <div class="mb-4 sm:mb-6">
                    <div class="relative">
                        <input type="text" id="searchInput" placeholder="Cari nama proyek..." 
                               class="w-full pl-10 pr-4 py-2 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <i class="fas fa-search absolute left-3 top-2.5 sm:top-3 text-gray-400"></i>
                    </div>
                </div>

                <div class="space-y-4" id="projectsList">
                    @foreach($completedProjects as $project)
                        @php
                            $myRole = $project->members->where('user_id', Auth::id())->first();
                            $duration = $project->created_at && $project->completed_at 
                                ? $project->created_at->diffInDays($project->completed_at) 
                                : 0;
                        @endphp
                        
                        <div class="project-item border-l-4 border-green-500 bg-gradient-to-r from-green-50 to-white rounded-r-lg p-4 sm:p-5 hover:shadow-xl transition-all duration-300"
                             data-name="{{ strtolower($project->project_name) }}">
                            
                            <div class="flex flex-col gap-4">
                                <!-- Header -->
                                <div>
                                    <div class="flex items-start gap-2 mb-2 flex-wrap">
                                        <h3 class="text-lg sm:text-xl font-bold text-gray-800 flex-1">{{ $project->project_name }}</h3>
                                        <span class="px-2.5 py-1 text-xs bg-green-100 text-green-700 rounded-full font-bold whitespace-nowrap">
                                            <i class="fas fa-check-circle mr-1"></i>SELESAI
                                        </span>
                                    </div>
                                    @if($myRole)
                                        <span class="inline-block px-2.5 py-1 text-xs {{ $myRole->role === 'leader' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }} rounded-full font-semibold">
                                            <i class="fas {{ $myRole->role === 'leader' ? 'fa-crown' : 'fa-user' }} mr-1"></i>
                                            {{ ucfirst($myRole->role) }}
                                        </span>
                                    @endif
                                </div>

                                @if($project->description)
                                    <p class="text-sm text-gray-600 line-clamp-2">{{ $project->description }}</p>
                                @endif

                                <!-- Stats Grid -->
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="bg-white rounded-lg p-3 border border-gray-200">
                                        <p class="text-xs text-gray-500 mb-1">
                                            <i class="fas fa-calendar-plus mr-1"></i>Mulai
                                        </p>
                                        <p class="text-sm font-semibold text-gray-800">{{ $project->created_at->format('d M Y') }}</p>
                                    </div>

                                    <div class="bg-white rounded-lg p-3 border border-gray-200">
                                        <p class="text-xs text-gray-500 mb-1">
                                            <i class="fas fa-calendar-check mr-1"></i>Selesai
                                        </p>
                                        <p class="text-sm font-semibold text-gray-800">{{ $project->completed_at ? $project->completed_at->format('d M Y') : '-' }}</p>
                                    </div>

                                    <div class="bg-white rounded-lg p-3 border border-gray-200">
                                        <p class="text-xs text-gray-500 mb-1">
                                            <i class="fas fa-hourglass-half mr-1"></i>Durasi
                                        </p>
                                        <p class="text-sm font-semibold text-gray-800">{{ $duration }} Hari</p>
                                    </div>

                                    <div class="bg-white rounded-lg p-3 border border-gray-200">
                                        <p class="text-xs text-gray-500 mb-1">
                                            <i class="fas fa-users mr-1"></i>Tim
                                        </p>
                                        <p class="text-sm font-semibold text-gray-800">{{ $project->members->count() }} Orang</p>
                                    </div>
                                </div>

                                <!-- Additional Info -->
                                <div class="border-t border-gray-200 pt-3">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs sm:text-sm text-gray-600">
                                        <p>
                                            <i class="fas fa-user text-blue-500 mr-1"></i>
                                            <span class="font-medium">Dibuat:</span> {{ $project->creator->full_name }}
                                        </p>
                                        <p>
                                            <i class="fas fa-user-check text-green-500 mr-1"></i>
                                            <span class="font-medium">Diselesaikan:</span> {{ $project->completedBy ? $project->completedBy->full_name : '-' }}
                                        </p>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2">
                                        <i class="fas fa-clock mr-1"></i>
                                        Diselesaikan {{ $project->completed_at ? $project->completed_at->diffForHumans() : '-' }}
                                    </p>
                                </div>

                                <!-- Achievement Badge -->
                                <div class="flex items-center justify-center sm:justify-end">
                                    <div class="bg-gradient-to-br from-green-400 to-green-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 shadow-md">
                                        <i class="fas fa-trophy text-lg"></i>
                                        <span class="font-bold text-sm">Completed</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <script>
        // Search functionality
        const searchInput = document.getElementById('searchInput');
        const projectItems = document.querySelectorAll('.project-item');

        searchInput?.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            projectItems.forEach(item => {
                const name = item.dataset.name;
                if (name.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    </script>
@endsection
