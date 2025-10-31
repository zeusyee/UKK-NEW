@extends('layouts.leader')

@section('title', 'Project History')

@section('content')
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-history text-purple-600 mr-3"></i>
                    Riwayat Proyek Saya
                </h1>
                <p class="text-gray-600 mt-2">Semua proyek yang pernah saya pimpin dan telah diselesaikan</p>
            </div>
            <a href="{{ route('leader.dashboard') }}" class="inline-flex items-center bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg transition-all duration-300">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali ke Dashboard
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Total Completed Projects -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-green-100 text-sm font-medium mb-1">Proyek Selesai</p>
                    <p class="text-4xl font-bold">{{ $completedProjects->count() }}</p>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                    <i class="fas fa-trophy text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- As Leader -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-blue-100 text-sm font-medium mb-1">Sebagai Leader</p>
                    <p class="text-4xl font-bold">{{ $asLeader }}</p>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                    <i class="fas fa-crown text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- As Admin -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-purple-100 text-sm font-medium mb-1">Sebagai Admin</p>
                    <p class="text-4xl font-bold">{{ $asAdmin }}</p>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                    <i class="fas fa-shield-alt text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Success Rate -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-orange-100 text-sm font-medium mb-1">Total Proyek</p>
                    <p class="text-4xl font-bold">{{ $asLeader + $asAdmin }}</p>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                    <i class="fas fa-clipboard-list text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Completed Projects List -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-purple-500 to-indigo-600 p-6">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-list mr-3"></i>
                    Daftar Proyek yang Telah Diselesaikan
                </h2>
            </div>
        </div>

        <div class="p-6">
            @if($completedProjects->isEmpty())
                <div class="text-center py-12">
                    <i class="fas fa-history text-gray-300 text-6xl mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">Belum Ada Riwayat</h3>
                    <p class="text-gray-500">Anda belum memiliki proyek yang diselesaikan</p>
                </div>
            @else
                <!-- Search Bar -->
                <div class="mb-6">
                    <div class="relative">
                        <input type="text" id="searchInput" placeholder="Cari nama proyek..." 
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
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
                        
                        <div class="project-item border-l-4 border-green-500 bg-gradient-to-r from-green-50 to-white rounded-r-lg p-5 hover:shadow-xl transition-all duration-300"
                             data-name="{{ strtolower($project->project_name) }}">
                            
                            <div class="flex flex-col lg:flex-row gap-4">
                                <!-- Main Content -->
                                <div class="flex-1">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-2 flex-wrap">
                                                <h3 class="text-xl font-bold text-gray-800">{{ $project->project_name }}</h3>
                                                <span class="px-3 py-1 text-xs bg-green-100 text-green-700 rounded-full font-bold">
                                                    <i class="fas fa-check-circle mr-1"></i>SELESAI
                                                </span>
                                                @if($myRole)
                                                    <span class="px-3 py-1 text-xs {{ $myRole->role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }} rounded-full font-bold">
                                                        <i class="fas {{ $myRole->role === 'admin' ? 'fa-shield-alt' : 'fa-crown' }} mr-1"></i>
                                                        {{ ucfirst($myRole->role) }}
                                                    </span>
                                                @endif
                                            </div>
                                            @if($project->description)
                                                <p class="text-sm text-gray-600 mb-3">{{ $project->description }}</p>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Project Details -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                        <div class="bg-white rounded-lg p-4 border border-gray-200">
                                            <div class="grid grid-cols-2 gap-3">
                                                <div>
                                                    <p class="text-xs text-gray-500 mb-1">
                                                        <i class="fas fa-calendar-plus mr-1"></i>Mulai
                                                    </p>
                                                    <p class="text-sm font-semibold text-gray-800">{{ $project->created_at->format('d M Y') }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-xs text-gray-500 mb-1">
                                                        <i class="fas fa-calendar-check mr-1"></i>Selesai
                                                    </p>
                                                    <p class="text-sm font-semibold text-gray-800">{{ $project->completed_at ? $project->completed_at->format('d M Y') : '-' }}</p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="bg-white rounded-lg p-4 border border-gray-200">
                                            <div class="grid grid-cols-2 gap-3">
                                                <div>
                                                    <p class="text-xs text-gray-500 mb-1">
                                                        <i class="fas fa-hourglass-half mr-1"></i>Durasi
                                                    </p>
                                                    <p class="text-sm font-semibold text-gray-800">{{ $duration }} Hari</p>
                                                </div>
                                                <div>
                                                    <p class="text-xs text-gray-500 mb-1">
                                                        <i class="fas fa-users mr-1"></i>Tim
                                                    </p>
                                                    <p class="text-sm font-semibold text-gray-800">{{ $project->members->count() }} Orang</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Additional Info -->
                                    <div class="flex items-center gap-4 flex-wrap text-sm text-gray-600">
                                        <span>
                                            <i class="fas fa-user text-blue-500 mr-1"></i>
                                            Dibuat: {{ $project->creator->full_name }}
                                        </span>
                                        <span>
                                            <i class="fas fa-user-check text-green-500 mr-1"></i>
                                            Diselesaikan: {{ $project->completedBy ? $project->completedBy->full_name : '-' }}
                                        </span>
                                        <span>
                                            <i class="fas fa-clock text-purple-500 mr-1"></i>
                                            {{ $project->completed_at ? $project->completed_at->diffForHumans() : '-' }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Action Badge -->
                                <div class="flex items-center justify-center">
                                    <div class="bg-gradient-to-br from-green-400 to-green-600 text-white px-6 py-4 rounded-xl text-center shadow-lg">
                                        <i class="fas fa-trophy text-3xl mb-2"></i>
                                        <p class="text-sm font-bold">Completed</p>
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
