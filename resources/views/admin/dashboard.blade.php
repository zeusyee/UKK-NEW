```html
@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <!-- Welcome Section -->
    <div class="mb-8">
        <h1 class="text-2xl font-semibold text-gray-900">Dashboard</h1>
        <p class="text-gray-600 mt-1">Selamat datang di panel administrator</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Total Users -->
        <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-sm transition-all duration-200 hover:-translate-y-0.5">
            <div class="flex items-center">
                <div class="p-2 rounded-lg bg-blue-50 text-blue-600 mr-3">
                    <i class="fas fa-users text-sm"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Pengguna</p>
                    <p class="text-xl font-semibold text-gray-900">{{ \App\Models\User::count() }}</p>
                </div>
            </div>
        </div>

        <!-- Total Projects -->
        <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-sm transition-all duration-200 hover:-translate-y-0.5">
            <div class="flex items-center">
                <div class="p-2 rounded-lg bg-indigo-50 text-indigo-600 mr-3">
                    <i class="fas fa-project-diagram text-sm"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Proyek</p>
                    <p class="text-xl font-semibold text-gray-900">{{ \App\Models\Project::count() }}</p>
                </div>
            </div>
        </div>

        <!-- Working Users -->
        <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-sm transition-all duration-200 hover:-translate-y-0.5">
            <div class="flex items-center">
                <div class="p-2 rounded-lg bg-green-50 text-green-600 mr-3">
                    <i class="fas fa-user-clock text-sm"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Aktif</p>
                    <p class="text-xl font-semibold text-gray-900">{{ \App\Models\User::where('current_task_status', 'working')->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Idle Users -->
        <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-sm transition-all duration-200 hover:-translate-y-0.5">
            <div class="flex items-center">
                <div class="p-2 rounded-lg bg-gray-100 text-gray-600 mr-3">
                    <i class="fas fa-user-slash text-sm"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Tidak Aktif</p>
                    <p class="text-xl font-semibold text-gray-900">{{ \App\Models\User::where('current_task_status', 'idle')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Time Logs Monitoring Section -->
    @php
        $recentTimeLogs = \App\Models\TimeLog::with(['user', 'card.board.project', 'subtask'])
            ->orderBy('start_time', 'desc')
            ->take(10)
            ->get();
        
        $totalTimeLogsToday = \App\Models\TimeLog::whereDate('start_time', today())
            ->get()
            ->sum(function($log) {
                return $log->end_time 
                    ? $log->end_time->diffInMinutes($log->start_time) 
                    : 0;
            });
        
        $activeTimeLogs = \App\Models\TimeLog::whereNull('end_time')->count();
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <!-- Time Logs Today -->
        <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-sm transition-all duration-200 hover:-translate-y-0.5">
            <div class="flex items-center">
                <div class="p-2 rounded-lg bg-cyan-50 text-cyan-600 mr-3">
                    <i class="fas fa-calendar-check text-sm"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Waktu Kerja Hari Ini</p>
                    <p class="text-xl font-semibold text-gray-900">{{ number_format($totalTimeLogsToday / 60, 1) }}h</p>
                </div>
            </div>
        </div>

        <!-- Active Time Logs -->
        <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-sm transition-all duration-200 hover:-translate-y-0.5">
            <div class="flex items-center">
                <div class="p-2 rounded-lg bg-lime-50 text-lime-600 mr-3">
                    <i class="fas fa-stopwatch text-sm"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Sedang Berjalan</p>
                    <p class="text-xl font-semibold text-gray-900">{{ $activeTimeLogs }}</p>
                </div>
            </div>
        </div>

        <!-- Time Logs Analytics Link -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-200 p-4 hover:shadow-sm transition-all duration-200 hover:-translate-y-0.5">
            <a href="{{ route('admin.time-logs.index') }}" class="flex items-center justify-between h-full">
                <div>
                    <p class="text-xs text-blue-700 mb-1">Monitoring</p>
                    <p class="text-sm font-medium text-blue-900">Lihat Detail</p>
                </div>
                <i class="fas fa-arrow-right text-lg text-blue-300"></i>
            </a>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Recent Projects -->
        <div class="bg-white rounded-lg border border-gray-200 hover:shadow-sm transition-all duration-200">
            <div class="px-4 py-3 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-sm font-medium text-gray-900 flex items-center">
                        <i class="fas fa-folder-open text-gray-500 mr-2 text-sm"></i>
                        Proyek Terbaru
                    </h2>
                    <a href="{{ route('admin.projects.index') }}" class="text-xs text-blue-600 hover:text-blue-800">
                        Lihat Semua
                    </a>
                </div>
            </div>
            <div class="p-4">
                @if(\App\Models\Project::count() > 0)
                    <div class="space-y-3">
                        @foreach(\App\Models\Project::with(['creator', 'members'])->latest()->take(5)->get() as $project)
                            <a href="{{ route('admin.projects.show', $project) }}" class="block p-3 rounded border border-gray-100 hover:bg-blue-50 hover:border-blue-200 transition-all duration-200 hover:-translate-y-0.5">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-sm font-medium text-gray-900 hover:text-blue-600">
                                                {{ $project->project_name }}
                                            </span>
                                            @if($project->status === 'completed')
                                                <span class="px-2 py-0.5 text-xs bg-green-100 text-green-700 rounded font-medium">
                                                    Selesai
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 text-xs bg-blue-100 text-blue-700 rounded font-medium">
                                                    Aktif
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-gray-500">
                                            Oleh {{ $project->creator->full_name }}
                                        </p>
                                        <div class="flex items-center mt-1 text-xs text-gray-400">
                                            <span class="flex items-center mr-2">
                                                <i class="fas fa-clock text-xs mr-1"></i>
                                                {{ $project->created_at->diffForHumans() }}
                                            </span>
                                            <span class="flex items-center">
                                                <i class="fas fa-users text-xs mr-1"></i>
                                                {{ $project->members->count() }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6">
                        <i class="fas fa-folder-open text-gray-300 text-2xl mb-2"></i>
                        <p class="text-sm text-gray-500">Belum ada proyek</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Users -->
        <div class="bg-white rounded-lg border border-gray-200 hover:shadow-sm transition-all duration-200">
            <div class="px-4 py-3 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-sm font-medium text-gray-900 flex items-center">
                        <i class="fas fa-user-plus text-gray-500 mr-2 text-sm"></i>
                        Pengguna Terbaru
                    </h2>
                    <a href="{{ route('admin.users.index') }}" class="text-xs text-blue-600 hover:text-blue-800">
                        Lihat Semua
                    </a>
                </div>
            </div>
            <div class="p-4">
                @if(\App\Models\User::count() > 0)
                    <div class="space-y-3">
                        @foreach(\App\Models\User::latest()->take(5)->get() as $user)
                            <div class="p-3 rounded border border-gray-100 hover:bg-gray-50 transition-all duration-200 hover:-translate-y-0.5">
                                <div class="flex justify-between items-start">
                                    <div class="flex items-start flex-1">
                                        <div class="bg-gray-200 text-gray-700 w-8 h-8 rounded-full flex items-center justify-center text-xs font-medium mr-3">
                                            {{ strtoupper(substr($user->full_name, 0, 1)) }}
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900">{{ $user->full_name }}</p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ $user->email }}
                                            </p>
                                            <div class="flex items-center mt-1 text-xs text-gray-400">
                                                <span class="flex items-center">
                                                    <i class="fas fa-clock text-xs mr-1"></i>
                                                    {{ $user->created_at->diffForHumans() }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="px-2 py-0.5 text-xs font-medium rounded-full ml-2 {{ $user->current_task_status === 'working' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $user->current_task_status === 'working' ? 'Aktif' : 'Idle' }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6">
                        <i class="fas fa-users text-gray-300 text-2xl mb-2"></i>
                        <p class="text-sm text-gray-500">Belum ada pengguna</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Time Logs & Completed Projects Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Recent Time Logs -->
        <div class="bg-white rounded-lg border border-gray-200 hover:shadow-sm transition-all duration-200">
            <div class="px-4 py-3 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-sm font-medium text-gray-900 flex items-center">
                        <i class="fas fa-history text-gray-500 mr-2 text-sm"></i>
                        Time Logs Terbaru
                    </h2>
                    <a href="{{ route('admin.time-logs.index') }}" class="text-xs text-blue-600 hover:text-blue-800">
                        Lihat Semua
                    </a>
                </div>
            </div>
            <div class="p-4">
                @if($recentTimeLogs->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentTimeLogs->take(5) as $log)
                            <div class="p-3 rounded border border-gray-100 hover:bg-gray-50 transition-all duration-200 hover:-translate-y-0.5">
                                <div class="flex justify-between items-start mb-1">
                                    <div class="flex-1">
                                        <div class="text-sm text-gray-900">
                                            <i class="fas fa-user text-gray-400 mr-1 text-xs"></i>
                                            {{ $log->user->full_name }}
                                        </div>
                                        @if($log->card)
                                            <p class="text-xs text-gray-600 mt-1 ml-4">
                                                {{ $log->card->card_title }}
                                            </p>
                                        @endif
                                        @if($log->subtask)
                                            <p class="text-xs text-gray-500 mt-1 ml-4">
                                                {{ $log->subtask->subtask_title }}
                                            </p>
                                        @endif
                                    </div>
                                    <span class="px-2 py-0.5 text-xs font-medium rounded-full {{ $log->end_time ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                        {{ $log->end_time ? number_format($log->end_time->diffInMinutes($log->start_time) / 60, 1) . 'h' : 'Ongoing' }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between text-xs text-gray-400 ml-4">
                                    <span>{{ $log->start_time->format('d M Y H:i') }}</span>
                                    @if($log->card && $log->card->board)
                                        <span class="text-blue-600 font-medium">{{ $log->card->board->project->project_name }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6">
                        <i class="fas fa-inbox text-gray-300 text-2xl mb-2"></i>
                        <p class="text-sm text-gray-500">Belum ada time logs</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Completed Projects -->
        <div class="bg-white rounded-lg border border-gray-200 hover:shadow-sm transition-all duration-200">
            <div class="px-4 py-3 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-sm font-medium text-gray-900 flex items-center">
                        <i class="fas fa-check-circle text-gray-500 mr-2 text-sm"></i>
                        Proyek Selesai
                    </h2>
                    <span class="text-xs text-gray-500">
                        {{ isset($recentCompletedProjects) ? $recentCompletedProjects->count() : 0 }} Terbaru
                    </span>
                </div>
            </div>
            <div class="p-4">
                @if(isset($recentCompletedProjects) && $recentCompletedProjects->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentCompletedProjects as $project)
                            <a href="{{ route('admin.projects.show', $project) }}" class="block p-3 rounded border border-gray-100 hover:bg-blue-50 hover:border-blue-200 transition-all duration-200 hover:-translate-y-0.5">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-sm font-medium text-gray-900 hover:text-blue-600">
                                                {{ $project->project_name }}
                                            </span>
                                            <span class="px-2 py-0.5 text-xs bg-green-100 text-green-700 rounded font-medium">
                                                SELESAI
                                            </span>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-1 text-xs text-gray-600">
                                            <p>
                                                <span class="font-medium">Dibuat:</span> {{ $project->creator->full_name }}
                                            </p>
                                            <p>
                                                <span class="font-medium">Selesai:</span> {{ $project->completed_at ? $project->completed_at->format('d M Y') : '-' }}
                                            </p>
                                        </div>
                                        <div class="mt-1 flex items-center gap-2 text-xs text-gray-500">
                                            <span>
                                                {{ $project->members->count() }} anggota
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6">
                        <i class="fas fa-check-circle text-gray-300 text-2xl mb-2"></i>
                        <p class="text-sm text-gray-500">Belum ada proyek selesai</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection