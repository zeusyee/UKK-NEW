@extends('layouts.admin')

@section('title', 'Time Logs Report')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb Navigation -->
    <div class="mb-6 flex items-center gap-2 text-sm">
        <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:text-blue-800">Dashboard</a>
        <span class="text-gray-400">→</span>
        <a href="{{ route('admin.time-logs.index') }}" class="text-blue-600 hover:text-blue-800">Time Logs</a>
        <span class="text-gray-400">→</span>
        <span class="text-gray-600">Report</span>
    </div>

    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Time Logs Report</h1>
        <p class="text-gray-600 mt-2">Detailed report of all time logs dengan filtering options</p>
    </div>

    <!-- Filter Form -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <form method="GET" action="{{ route('admin.time-logs.report') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- User Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">User</label>
                    <select name="user_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Users</option>
                        @foreach(\App\Models\User::orderBy('full_name')->get() as $user)
                            <option value="{{ $user->user_id }}" {{ request('user_id') == $user->user_id ? 'selected' : '' }}>
                                {{ $user->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Project Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Project</label>
                    <select name="project_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Projects</option>
                        @foreach(\App\Models\Project::orderBy('project_name')->get() as $project)
                            <option value="{{ $project->project_id }}" {{ request('project_id') == $project->project_id ? 'selected' : '' }}>
                                {{ $project->project_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Start Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- End Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-search mr-2"></i>Apply Filters
                </button>
                <a href="{{ route('admin.time-logs.report') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                    <i class="fas fa-redo mr-2"></i>Reset
                </a>
                @if($timeLogs->count() > 0)
                <button type="button" onclick="window.print()" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-print mr-2"></i>Print
                </button>
                <a href="{{ route('admin.time-logs.report', array_merge(request()->all(), ['print' => '1'])) }}" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    <i class="fas fa-file-pdf mr-2"></i>Download PDF
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Summary Statistics -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">
            <i class="fas fa-chart-line mr-2 text-blue-600"></i>Summary Statistics
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="border-l-4 border-blue-500 pl-4">
                <p class="text-sm text-gray-600 mb-1">Total Hours</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($totalHours, 2) }}</p>
            </div>
            <div class="border-l-4 border-green-500 pl-4">
                <p class="text-sm text-gray-600 mb-1">Total Sessions</p>
                <p class="text-2xl font-bold text-gray-900">{{ $totalSessions }}</p>
            </div>
            <div class="border-l-4 border-orange-500 pl-4">
                <p class="text-sm text-gray-600 mb-1">Avg Hours/Session</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($avgHours, 2) }}</p>
            </div>
            <div class="border-l-4 border-purple-500 pl-4">
                <p class="text-sm text-gray-600 mb-1">Active Users</p>
                <p class="text-2xl font-bold text-gray-900">{{ $activeUsers }}</p>
            </div>
        </div>
    </div>

    <!-- Time Logs Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">User</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Project</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Card</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Start Time</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">End Time</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Duration</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($timeLogs as $log)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $log->user->full_name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                @if($log->card && $log->card->board)
                                    {{ $log->card->board->project->project_name ?? 'N/A' }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $log->card->card_title ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $log->start_time->format('Y-m-d H:i') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                @if($log->end_time)
                                    {{ $log->end_time->format('Y-m-d H:i') }}
                                @else
                                    <span class="text-yellow-600 font-medium">Ongoing</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                @if($log->end_time)
                                    @php
                                        $minutes = $log->end_time->diffInMinutes($log->start_time);
                                        $hours = floor($minutes / 60);
                                        $mins = $minutes % 60;
                                    @endphp
                                    {{ $hours }}h {{ $mins }}m
                                @else
                                    <span class="text-yellow-600">In Progress</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                No time logs found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="bg-white px-6 py-4 border-t border-gray-200">
            {{ $timeLogs->links() }}
        </div>
    </div>
</div>
@endsection
