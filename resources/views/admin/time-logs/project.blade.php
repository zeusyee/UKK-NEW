@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="mb-4">
            <a href="{{ route('admin.time-logs.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
                ← Back to Time Logs
            </a>
        </div>
        
        <h1 class="text-4xl font-bold text-gray-800 mb-2">Time Logs for Project</h1>
        
        <div class="bg-gray-50 rounded-lg p-4 mt-4">
            <p class="text-sm text-gray-600"><strong>Project:</strong> {{ $project->project_name }}</p>
            <p class="text-sm text-gray-600 mt-2"><strong>Status:</strong> <span class="capitalize">{{ $project->status }}</span></p>
        </div>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Total Time</p>
                    <p class="text-3xl font-bold text-gray-800">
                        @php
                            $totalMins = $stats['total_duration'];
                            if ($totalMins < 60) {
                                echo $totalMins . 'm';
                            } else {
                                $hours = intval($totalMins / 60);
                                $mins = $totalMins % 60;
                                echo $hours . 'h ' . $mins . 'm';
                            }
                        @endphp
                    </p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Sessions</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $stats['total_sessions'] }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Team Members</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $stats['team_members'] }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-2a6 6 0 0112 0v2z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Time Logs Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Date & Time</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">User</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Card</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Subtask</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Duration</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($timeLogs as $log)
                    <tr class="border-b hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm">
                            <div class="font-semibold text-gray-800">{{ $log->start_time->format('M d, Y') }}</div>
                            <div class="text-gray-600 text-xs">{{ $log->start_time->format('H:i:s') }} - {{ $log->end_time ? $log->end_time->format('H:i:s') : 'Ongoing' }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @if($log->user)
                                <a href="{{ route('admin.time-logs.user', $log->user) }}" class="text-blue-600 hover:underline">
                                    {{ $log->user->name }}
                                </a>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-800">
                            {{ $log->card ? Str::limit($log->card->card_title, 25) : '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-800">
                            {{ $log->subtask ? Str::limit($log->subtask->subtask_title, 25) : '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full font-semibold text-xs">
                                @if($log->end_time)
                                    {{ $log->end_time->diffInMinutes($log->start_time) }}m
                                @else
                                    Ongoing
                                @endif
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @if($log->end_time)
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-semibold">Completed</span>
                            @else
                                <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs font-semibold">Ongoing</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            <div class="text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-lg font-semibold">No time logs found</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $timeLogs->links() }}
    </div>

    <!-- Back Button -->
    <div class="mt-8">
        <a href="{{ route('admin.time-logs.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold">
            ← Back to Time Logs
        </a>
    </div>
</div>
@endsection
