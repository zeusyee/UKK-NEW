@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <!-- Welcome Section -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Dashboard Admin</h1>
        <p class="text-gray-600 mt-2">Selamat datang di panel administrator</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Users Card -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white transform transition-all duration-300 hover:scale-105 hover:shadow-xl">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-blue-100 text-sm font-medium mb-1">Total Pengguna</p>
                    <p class="text-4xl font-bold mb-2">{{ \App\Models\User::count() }}</p>
                    <p class="text-blue-100 text-xs">Terdaftar di sistem</p>
                </div>
                <div class="bg-white bg-opacity-20 p-4 rounded-lg">
                    <i class="fas fa-users text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Projects Card -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white transform transition-all duration-300 hover:scale-105 hover:shadow-xl">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-green-100 text-sm font-medium mb-1">Total Proyek</p>
                    <p class="text-4xl font-bold mb-2">{{ \App\Models\Project::count() }}</p>
                    <p class="text-green-100 text-xs">Proyek aktif</p>
                </div>
                <div class="bg-white bg-opacity-20 p-4 rounded-lg">
                    <i class="fas fa-project-diagram text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- Working Users Card -->
        <div class="bg-gradient-to-br from-yellow-500 to-orange-500 rounded-xl shadow-lg p-6 text-white transform transition-all duration-300 hover:scale-105 hover:shadow-xl">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-yellow-100 text-sm font-medium mb-1">Sedang Bekerja</p>
                    <p class="text-4xl font-bold mb-2">{{ \App\Models\User::where('current_task_status', 'working')->count() }}</p>
                    <p class="text-yellow-100 text-xs">Pengguna aktif</p>
                </div>
                <div class="bg-white bg-opacity-20 p-4 rounded-lg">
                    <i class="fas fa-user-clock text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- Idle Users Card -->
        <div class="bg-gradient-to-br from-gray-500 to-gray-600 rounded-xl shadow-lg p-6 text-white transform transition-all duration-300 hover:scale-105 hover:shadow-xl">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-gray-100 text-sm font-medium mb-1">Tidak Aktif</p>
                    <p class="text-4xl font-bold mb-2">{{ \App\Models\User::where('current_task_status', 'idle')->count() }}</p>
                    <p class="text-gray-100 text-xs">Pengguna idle</p>
                </div>
                <div class="bg-white bg-opacity-20 p-4 rounded-lg">
                    <i class="fas fa-user-slash text-3xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Recent Projects -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-6">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-folder-open mr-3"></i>
                        Proyek Terbaru
                    </h2>
                    <a href="{{ route('admin.projects.index') }}" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300">
                        Lihat Semua
                    </a>
                </div>
            </div>
            <div class="p-6">
                @if(\App\Models\Project::count() > 0)
                    <div class="space-y-4">
                        @foreach(\App\Models\Project::with(['creator', 'members'])->latest()->take(5)->get() as $project)
                            <div class="border-l-4 border-blue-500 bg-gray-50 rounded-r-lg p-4 hover:bg-gray-100 transition-all duration-300">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <a href="{{ route('admin.projects.show', $project) }}" class="text-lg font-semibold text-gray-800 hover:text-blue-600 transition-colors duration-300">
                                                {{ $project->project_name }}
                                            </a>
                                            @if($project->status === 'completed')
                                                <span class="px-2 py-0.5 text-xs bg-green-100 text-green-700 rounded-full font-semibold">
                                                    <i class="fas fa-check-circle mr-1"></i>Selesai
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 text-xs bg-blue-100 text-blue-700 rounded-full font-semibold">
                                                    <i class="fas fa-spinner mr-1"></i>Aktif
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-500 mt-1">
                                            <i class="fas fa-user text-xs mr-1"></i>
                                            Dibuat oleh {{ $project->creator->full_name }}
                                        </p>
                                        <p class="text-xs text-gray-400 mt-1">
                                            <i class="fas fa-clock text-xs mr-1"></i>
                                            {{ $project->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium ml-4">
                                        <i class="fas fa-users text-xs mr-1"></i>
                                        {{ $project->members->count() }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-folder-open text-gray-300 text-5xl mb-3"></i>
                        <p class="text-gray-500">Belum ada proyek</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Users -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-green-500 to-green-600 p-6">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-user-plus mr-3"></i>
                        Pengguna Terbaru
                    </h2>
                    <a href="{{ route('admin.users.index') }}" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300">
                        Lihat Semua
                    </a>
                </div>
            </div>
            <div class="p-6">
                @if(\App\Models\User::count() > 0)
                    <div class="space-y-4">
                        @foreach(\App\Models\User::latest()->take(5)->get() as $user)
                            <div class="border-l-4 border-green-500 bg-gray-50 rounded-r-lg p-4 hover:bg-gray-100 transition-all duration-300">
                                <div class="flex justify-between items-start">
                                    <div class="flex items-start flex-1">
                                        <div class="bg-gradient-to-br from-green-400 to-green-600 text-white w-12 h-12 rounded-full flex items-center justify-center font-bold text-lg mr-3">
                                            {{ strtoupper(substr($user->full_name, 0, 1)) }}
                                        </div>
                                        <div class="flex-1">
                                            <p class="font-semibold text-gray-800">{{ $user->full_name }}</p>
                                            <p class="text-sm text-gray-500 mt-1">
                                                <i class="fas fa-envelope text-xs mr-1"></i>
                                                {{ $user->email }}
                                            </p>
                                            <p class="text-xs text-gray-400 mt-1">
                                                <i class="fas fa-clock text-xs mr-1"></i>
                                                Bergabung {{ $user->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full ml-4 {{ $user->current_task_status === 'working' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-200 text-gray-700' }}">
                                        <i class="fas fa-circle text-xs mr-1 {{ $user->current_task_status === 'working' ? 'text-yellow-500' : 'text-gray-500' }}"></i>
                                        {{ $user->current_task_status === 'working' ? 'Aktif' : 'Idle' }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-users text-gray-300 text-5xl mb-3"></i>
                        <p class="text-gray-500">Belum ada pengguna</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Completed Projects History -->
    @if(isset($recentCompletedProjects) && $recentCompletedProjects->count() > 0)
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-purple-500 to-indigo-600 p-6">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-history mr-3"></i>
                        Riwayat Proyek Selesai
                    </h2>
                    <span class="bg-white bg-opacity-20 text-white px-4 py-2 rounded-lg text-sm font-medium">
                        {{ $recentCompletedProjects->count() }} Terbaru
                    </span>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($recentCompletedProjects as $project)
                        <div class="border-l-4 border-green-500 bg-gradient-to-r from-green-50 to-white rounded-r-lg p-4 hover:shadow-md transition-all duration-300">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <h3 class="text-lg font-bold text-gray-800">{{ $project->project_name }}</h3>
                                        <span class="px-2.5 py-1 text-xs bg-green-100 text-green-700 rounded-full font-bold">
                                            <i class="fas fa-check-circle mr-1"></i>SELESAI
                                        </span>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                                        <p class="text-gray-600">
                                            <i class="fas fa-user text-xs mr-1 text-blue-500"></i>
                                            <span class="font-medium">Dibuat:</span> {{ $project->creator->full_name }}
                                        </p>
                                        <p class="text-gray-600">
                                            <i class="fas fa-user-check text-xs mr-1 text-green-500"></i>
                                            <span class="font-medium">Diselesaikan:</span> {{ $project->completedBy ? $project->completedBy->full_name : '-' }}
                                        </p>
                                        <p class="text-gray-600">
                                            <i class="fas fa-calendar-plus text-xs mr-1 text-indigo-500"></i>
                                            <span class="font-medium">Mulai:</span> {{ $project->created_at->format('d M Y') }}
                                        </p>
                                        <p class="text-gray-600">
                                            <i class="fas fa-calendar-check text-xs mr-1 text-green-500"></i>
                                            <span class="font-medium">Selesai:</span> {{ $project->completed_at ? $project->completed_at->format('d M Y') : '-' }}
                                        </p>
                                    </div>
                                    <div class="mt-2 flex items-center gap-2">
                                        <span class="text-xs text-gray-500">
                                            <i class="fas fa-users mr-1"></i>
                                            {{ $project->members->count() }} anggota tim
                                        </span>
                                        <span class="text-xs text-gray-400">•</span>
                                        <span class="text-xs text-gray-500">
                                            <i class="fas fa-clock mr-1"></i>
                                            Diselesaikan {{ $project->completed_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                                <a href="{{ route('admin.projects.show', $project) }}" class="ml-4 bg-green-100 hover:bg-green-200 text-green-700 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 flex items-center gap-2">
                                    <i class="fas fa-eye"></i>
                                    <span class="hidden sm:inline">Lihat</span>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
@endsection