@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-800 mb-2">Time Logs Analytics</h1>
        <p class="text-gray-600">Comprehensive analytics for all time tracking data</p>
    </div>

    <!-- Period Selector -->
    <div class="mb-6 flex gap-2">
        <a href="{{ route('admin.time-logs.analytics', ['period' => 'week']) }}" 
           class="px-4 py-2 rounded-lg font-semibold transition {{ $period === 'week' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300' }}">
            This Week
        </a>
        <a href="{{ route('admin.time-logs.analytics', ['period' => 'month']) }}" 
           class="px-4 py-2 rounded-lg font-semibold transition {{ $period === 'month' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300' }}">
            This Month
        </a>
        <a href="{{ route('admin.time-logs.analytics', ['period' => 'year']) }}" 
           class="px-4 py-2 rounded-lg font-semibold transition {{ $period === 'year' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300' }}">
            This Year
        </a>
    </div>

    <!-- Analytics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm mb-2">Total Duration</p>
            <p class="text-3xl font-bold text-gray-800">
                @php
                    $totalMins = $analytics['total_duration'];
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

        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm mb-2">Total Sessions</p>
            <p class="text-3xl font-bold text-gray-800">{{ $analytics['total_sessions'] }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm mb-2">Avg. Session Duration</p>
            <p class="text-3xl font-bold text-gray-800">
                @php
                    $avgMins = $analytics['avg_session_duration'];
                    if ($avgMins < 60) {
                        echo $avgMins . 'm';
                    } else {
                        $hours = intval($avgMins / 60);
                        $mins = $avgMins % 60;
                        echo $hours . 'h ' . $mins . 'm';
                    }
                @endphp
            </p>
        </div>
    </div>

    <!-- Time by User -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Top Users by Time Spent</h2>
            <div class="space-y-3">
                @forelse($timeByUser as $userName => $minutes)
                    @php
                        $percentage = $analytics['total_duration'] > 0 ? round(($minutes / $analytics['total_duration']) * 100) : 0;
                    @endphp
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-semibold text-gray-700">{{ $userName }}</span>
                            <span class="text-sm text-gray-600">
                                @php
                                    if ($minutes < 60) {
                                        echo $minutes . 'm';
                                    } else {
                                        $h = intval($minutes / 60);
                                        $m = $minutes % 60;
                                        echo $h . 'h ' . $m . 'm';
                                    }
                                @endphp
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No user time data available</p>
                @endforelse
            </div>
        </div>

        <!-- Time by Project -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Top Projects by Time Spent</h2>
            <div class="space-y-3">
                @forelse($timeByProject as $projectName => $minutes)
                    @php
                        $percentage = $analytics['total_duration'] > 0 ? round(($minutes / $analytics['total_duration']) * 100) : 0;
                    @endphp
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-semibold text-gray-700">{{ $projectName }}</span>
                            <span class="text-sm text-gray-600">
                                @php
                                    if ($minutes < 60) {
                                        echo $minutes . 'm';
                                    } else {
                                        $h = intval($minutes / 60);
                                        $m = $minutes % 60;
                                        echo $h . 'h ' . $m . 'm';
                                    }
                                @endphp
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No project time data available</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Daily Breakdown -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Daily Breakdown</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Date</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Sessions</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Total Duration</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Avg Session</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($analytics['logs_by_date'] as $date => $logs)
                        @php
                            $totalDuration = $logs->sum(function($log) {
                                return $log->end_time 
                                    ? $log->end_time->diffInMinutes($log->start_time) 
                                    : 0;
                            });
                            $avgDuration = $logs->count() > 0 ? round($totalDuration / $logs->count()) : 0;
                        @endphp
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-gray-800 font-semibold">{{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $logs->count() }}</td>
                            <td class="px-4 py-3 text-gray-700">
                                @php
                                    if ($totalDuration < 60) {
                                        echo $totalDuration . 'm';
                                    } else {
                                        $h = intval($totalDuration / 60);
                                        $m = $totalDuration % 60;
                                        echo $h . 'h ' . $m . 'm';
                                    }
                                @endphp
                            </td>
                            <td class="px-4 py-3 text-gray-700">
                                @php
                                    if ($avgDuration < 60) {
                                        echo $avgDuration . 'm';
                                    } else {
                                        $h = intval($avgDuration / 60);
                                        $m = $avgDuration % 60;
                                        echo $h . 'h ' . $m . 'm';
                                    }
                                @endphp
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500">No data available for this period</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Back Button -->
    <div class="mt-8">
        <a href="{{ route('admin.time-logs.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold">
            ← Back to Time Logs
        </a>
    </div>
</div>
@endsection
