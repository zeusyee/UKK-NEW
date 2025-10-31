@extends('layouts.admin')

@section('title', 'Project History')

@section('content')
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-history text-purple-600 mr-3"></i>
                    Riwayat Proyek
                </h1>
                <p class="text-gray-600 mt-2">Semua proyek yang telah diselesaikan</p>
            </div>
            <a href="{{ route('admin.projects.index') }}" class="inline-flex items-center bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg shadow-lg transition-all duration-300">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali ke Proyek Aktif
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Total Completed Projects -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-green-100 text-sm font-medium mb-1">Total Proyek Selesai</p>
                    <p class="text-4xl font-bold mb-2">{{ $completedProjects->count() }}</p>
                    <p class="text-green-100 text-xs">Dari total {{ $totalProjects }} proyek</p>
                </div>
                <div class="bg-white bg-opacity-20 p-4 rounded-lg">
                    <i class="fas fa-check-circle text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- This Month -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-blue-100 text-sm font-medium mb-1">Selesai Bulan Ini</p>
                    <p class="text-4xl font-bold mb-2">{{ $completedThisMonth }}</p>
                    <p class="text-blue-100 text-xs">{{ now()->format('F Y') }}</p>
                </div>
                <div class="bg-white bg-opacity-20 p-4 rounded-lg">
                    <i class="fas fa-calendar-check text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- Average Duration -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-purple-100 text-sm font-medium mb-1">Rata-rata Durasi</p>
                    <p class="text-4xl font-bold mb-2">{{ $averageDuration }}</p>
                    <p class="text-purple-100 text-xs">Hari pengerjaan</p>
                </div>
                <div class="bg-white bg-opacity-20 p-4 rounded-lg">
                    <i class="fas fa-hourglass-half text-3xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Completed Projects List -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-purple-500 to-indigo-600 p-6">
            <h2 class="text-xl font-bold text-white flex items-center">
                <i class="fas fa-list mr-3"></i>
                Daftar Proyek yang Telah Diselesaikan
            </h2>
        </div>

        <div class="p-6">
            @if($completedProjects->isEmpty())
                <div class="text-center py-12">
                    <i class="fas fa-history text-gray-300 text-6xl mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">Belum Ada Riwayat</h3>
                    <p class="text-gray-500">Belum ada proyek yang diselesaikan</p>
                </div>
            @else
                <!-- Search and Filter -->
                <div class="mb-6 flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <div class="relative">
                            <input type="text" id="searchInput" placeholder="Cari nama proyek..." 
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                        </div>
                    </div>
                    <select id="sortBy" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="newest">Terbaru</option>
                        <option value="oldest">Terlama</option>
                        <option value="duration">Durasi Terpanjang</option>
                        <option value="members">Anggota Terbanyak</option>
                    </select>
                </div>

                <div class="space-y-4" id="projectsList">
                    @foreach($completedProjects as $project)
                        <div class="project-item border-l-4 border-green-500 bg-gradient-to-r from-green-50 to-white rounded-r-lg p-5 hover:shadow-xl transition-all duration-300" 
                             data-name="{{ strtolower($project->project_name) }}"
                             data-date="{{ $project->completed_at ? $project->completed_at->timestamp : 0 }}"
                             data-duration="{{ $project->created_at && $project->completed_at ? $project->created_at->diffInDays($project->completed_at) : 0 }}"
                             data-members="{{ $project->members->count() }}">
                            
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
                                            </div>
                                            @if($project->description)
                                                <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $project->description }}</p>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Project Details Grid -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
                                        <div class="bg-white rounded-lg p-3 border border-gray-200">
                                            <div class="flex items-center gap-2 mb-1">
                                                <i class="fas fa-user text-blue-500"></i>
                                                <span class="text-xs font-semibold text-gray-600">Dibuat Oleh</span>
                                            </div>
                                            <p class="text-sm font-medium text-gray-800">{{ $project->creator->full_name }}</p>
                                            <p class="text-xs text-gray-500">{{ $project->created_at->format('d M Y') }}</p>
                                        </div>

                                        <div class="bg-white rounded-lg p-3 border border-gray-200">
                                            <div class="flex items-center gap-2 mb-1">
                                                <i class="fas fa-user-check text-green-500"></i>
                                                <span class="text-xs font-semibold text-gray-600">Diselesaikan Oleh</span>
                                            </div>
                                            <p class="text-sm font-medium text-gray-800">{{ $project->completedBy ? $project->completedBy->full_name : '-' }}</p>
                                            <p class="text-xs text-gray-500">{{ $project->completed_at ? $project->completed_at->format('d M Y') : '-' }}</p>
                                        </div>

                                        <div class="bg-white rounded-lg p-3 border border-gray-200">
                                            <div class="flex items-center gap-2 mb-1">
                                                <i class="fas fa-hourglass-half text-purple-500"></i>
                                                <span class="text-xs font-semibold text-gray-600">Durasi Pengerjaan</span>
                                            </div>
                                            @php
                                                $duration = $project->created_at && $project->completed_at 
                                                    ? $project->created_at->diffInDays($project->completed_at) 
                                                    : 0;
                                            @endphp
                                            <p class="text-sm font-medium text-gray-800">{{ $duration }} Hari</p>
                                            <p class="text-xs text-gray-500">{{ $project->completed_at ? $project->completed_at->diffForHumans() : '-' }}</p>
                                        </div>

                                        <div class="bg-white rounded-lg p-3 border border-gray-200">
                                            <div class="flex items-center gap-2 mb-1">
                                                <i class="fas fa-users text-orange-500"></i>
                                                <span class="text-xs font-semibold text-gray-600">Anggota Tim</span>
                                            </div>
                                            <p class="text-sm font-medium text-gray-800">{{ $project->members->count() }} Orang</p>
                                            <div class="flex -space-x-2 mt-1">
                                                @foreach($project->members->take(5) as $member)
                                                    <div class="h-6 w-6 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 border-2 border-white flex items-center justify-center text-white text-xs font-bold"
                                                         title="{{ $member->user->full_name ?? '' }}">
                                                        {{ strtoupper(substr($member->user->full_name ?? 'U', 0, 1)) }}
                                                    </div>
                                                @endforeach
                                                @if($project->members->count() > 5)
                                                    <div class="h-6 w-6 rounded-full bg-gray-400 border-2 border-white flex items-center justify-center text-white text-xs font-bold">
                                                        +{{ $project->members->count() - 5 }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Timeline -->
                                    <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg p-4 border border-blue-200">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-2">
                                                <i class="fas fa-flag-checkered text-blue-500"></i>
                                                <span class="text-sm font-semibold text-gray-700">Timeline</span>
                                            </div>
                                        </div>
                                        <div class="mt-3 flex items-center">
                                            <div class="flex-1">
                                                <div class="flex items-center justify-between mb-1">
                                                    <span class="text-xs text-gray-600">Mulai: {{ $project->created_at->format('d M Y') }}</span>
                                                    <span class="text-xs text-gray-600">Selesai: {{ $project->completed_at ? $project->completed_at->format('d M Y') : '-' }}</span>
                                                </div>
                                                <div class="w-full bg-gradient-to-r from-blue-200 to-green-200 rounded-full h-2"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex flex-row lg:flex-col gap-2 lg:w-32">
                                    <a href="{{ route('admin.projects.show', $project) }}" 
                                       class="flex-1 lg:flex-none bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-center font-medium transition-all duration-300 flex items-center justify-center gap-2">
                                        <i class="fas fa-eye"></i>
                                        <span class="text-sm">Detail</span>
                                    </a>
                                    <button class="flex-1 lg:flex-none bg-green-100 hover:bg-green-200 text-green-700 px-4 py-2 rounded-lg text-center font-medium transition-all duration-300 flex items-center justify-center gap-2">
                                        <i class="fas fa-trophy"></i>
                                        <span class="text-sm">Selesai</span>
                                    </button>
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
        const sortBy = document.getElementById('sortBy');
        const projectsList = document.getElementById('projectsList');
        const projectItems = Array.from(document.querySelectorAll('.project-item'));

        function filterAndSort() {
            const searchTerm = searchInput.value.toLowerCase();
            const sortValue = sortBy.value;

            // Filter
            const filtered = projectItems.filter(item => {
                const name = item.dataset.name;
                return name.includes(searchTerm);
            });

            // Sort
            filtered.sort((a, b) => {
                switch(sortValue) {
                    case 'newest':
                        return parseInt(b.dataset.date) - parseInt(a.dataset.date);
                    case 'oldest':
                        return parseInt(a.dataset.date) - parseInt(b.dataset.date);
                    case 'duration':
                        return parseInt(b.dataset.duration) - parseInt(a.dataset.duration);
                    case 'members':
                        return parseInt(b.dataset.members) - parseInt(a.dataset.members);
                    default:
                        return 0;
                }
            });

            // Hide all items
            projectItems.forEach(item => item.style.display = 'none');

            // Show filtered and sorted items
            filtered.forEach(item => {
                item.style.display = 'block';
                projectsList.appendChild(item);
            });
        }

        searchInput?.addEventListener('input', filterAndSort);
        sortBy?.addEventListener('change', filterAndSort);
    </script>
@endsection
