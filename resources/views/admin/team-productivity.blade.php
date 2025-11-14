@extends('layouts.admin')

@section('title', 'Team Productivity')

@section('content')
    <!-- Breadcrumb Navigation -->
    <div class="mb-6 flex items-center gap-2 text-sm">
        <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:text-blue-800">Dashboard</a>
        <span class="text-gray-400">→</span>
        <a href="{{ route('admin.time-logs.index') }}" class="text-blue-600 hover:text-blue-800">Time Logs</a>
        <span class="text-gray-400">→</span>
        <span class="text-gray-600">Team Productivity</span>
    </div>

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Team Productivity Dashboard</h1>
        <p class="text-gray-600 mt-2">Monitoring produktivitas tim berdasarkan time logs</p>
    </div>

    <!-- Period Selector -->
    <div class="mb-6 flex gap-2">
        <a href="{{ route('admin.team-productivity', ['period' => 'today']) }}" 
           class="px-4 py-2 rounded-lg font-medium {{ $period === 'today' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300' }}">
            <i class="fas fa-calendar-day mr-1"></i>Hari Ini
        </a>
        <a href="{{ route('admin.team-productivity', ['period' => 'week']) }}" 
           class="px-4 py-2 rounded-lg font-medium {{ $period === 'week' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300' }}">
            <i class="fas fa-calendar-week mr-1"></i>Minggu Ini
        </a>
        <a href="{{ route('admin.team-productivity', ['period' => 'month']) }}" 
           class="px-4 py-2 rounded-lg font-medium {{ $period === 'month' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300' }}">
            <i class="fas fa-calendar mr-1"></i>Bulan Ini
        </a>
    </div>

    <!-- Overall Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm mb-1">Total Jam Kerja</p>
                    <p class="text-3xl font-bold text-blue-600">{{ number_format($stats['total_hours'], 1) }}h</p>
                </div>
                <i class="fas fa-clock text-blue-200 text-4xl"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm mb-1">Total Sessions</p>
                    <p class="text-3xl font-bold text-green-600">{{ $stats['total_sessions'] }}</p>
                </div>
                <i class="fas fa-list text-green-200 text-4xl"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm mb-1">Active Users</p>
                    <p class="text-3xl font-bold text-purple-600">{{ $stats['active_users'] }}</p>
                </div>
                <i class="fas fa-users text-purple-200 text-4xl"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm mb-1">Avg Hours/User</p>
                    <p class="text-3xl font-bold text-orange-600">{{ number_format($stats['avg_hours_per_user'], 1) }}h</p>
                </div>
                <i class="fas fa-chart-bar text-orange-200 text-4xl"></i>
            </div>
        </div>
    </div>

    <!-- Team Member Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden mb-8">
        <div class="px-6 py-4 bg-gray-50 border-b">
            <h2 class="text-lg font-semibold text-gray-800">Productivity Per User</h2>
        </div>
        <table class="w-full">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">User</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Total Hours</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Sessions</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Avg/Session</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($userStats as $stat)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ $stat->user->name }}</td>
                        <td class="px-6 py-4 text-sm">
                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full font-medium">
                                {{ number_format($stat->total_hours, 1) }}h
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $stat->sessions }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ number_format($stat->avg_hours, 1) }}h</td>
                        <td class="px-6 py-4 text-sm">
                            @if($stat->total_hours >= $stats['avg_hours_per_user'])
                                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-medium">
                                    <i class="fas fa-check-circle mr-1"></i>Active
                                </span>
                            @else
                                <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-xs font-medium">
                                    <i class="fas fa-info-circle mr-1"></i>Low Activity
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            Tidak ada data
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Top Projects -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b">
            <h2 class="text-lg font-semibold text-gray-800">Top Projects by Time</h2>
        </div>
        <table class="w-full">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Project</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Total Hours</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Sessions</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Team Members</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($projectStats as $stat)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ $stat->project->name }}</td>
                        <td class="px-6 py-4 text-sm">
                            <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full font-medium">
                                {{ number_format($stat->total_hours, 1) }}h
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $stat->sessions }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $stat->members }}</td>
                        <td class="px-6 py-4 text-sm">
                            @php
                                $statusClass = match($stat->project->status) {
                                    'completed' => ['bg-green-100', 'text-green-800'],
                                    'active' => ['bg-blue-100', 'text-blue-800'],
                                    'paused' => ['bg-yellow-100', 'text-yellow-800'],
                                    default => ['bg-gray-100', 'text-gray-800']
                                };
                            @endphp
                            <span class="{{ $statusClass[0] }} {{ $statusClass[1] }} px-3 py-1 rounded-full text-xs font-medium">
                                {{ ucfirst($stat->project->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            Tidak ada data
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
