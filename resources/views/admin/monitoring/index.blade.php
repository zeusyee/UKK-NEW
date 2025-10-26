@extends('layouts.admin')

@section('title', 'Project Monitoring')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
        <!-- Total Projects -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Projects</p>
                    <p class="text-2xl font-semibold text-gray-800">{{ $projectStats['total'] }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-project-diagram text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Active Projects -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Active</p>
                    <p class="text-2xl font-semibold text-blue-600">{{ $projectStats['active'] }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-play-circle text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Completed Projects -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Completed</p>
                    <p class="text-2xl font-semibold text-green-600">{{ $projectStats['completed'] }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Deadline Approaching -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Deadline Soon</p>
                    <p class="text-2xl font-semibold text-orange-600">{{ $projectStats['deadline_approaching'] }}</p>
                </div>
                <div class="p-3 bg-orange-100 rounded-full">
                    <i class="fas fa-clock text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Overdue Projects -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Overdue</p>
                    <p class="text-2xl font-semibold text-red-600">{{ $projectStats['overdue'] }}</p>
                </div>
                <div class="p-3 bg-red-100 rounded-full">
                    <i class="fas fa-exclamation-circle text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Member Status Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Member Status</h3>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-yellow-50 rounded-lg p-4">
                    <p class="text-sm font-medium text-gray-600">Working</p>
                    <p class="text-2xl font-semibold text-yellow-600">{{ $workingUsers }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm font-medium text-gray-600">Idle</p>
                    <p class="text-2xl font-semibold text-gray-600">{{ $idleUsers }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-chart-line mr-2 text-green-500"></i>Project Progress Overview
            </h3>
            <div class="space-y-4 max-h-96 overflow-y-auto">
                @foreach($projectProgress as $projectData)
                    <div class="pb-3 border-b border-gray-100 last:border-0">
                        <div class="flex justify-between items-center text-sm mb-2">
                            <div class="flex items-center space-x-2">
                                <span class="text-gray-700 font-medium">{{ $projectData['project_name'] }}</span>
                                @if($projectData['status'] === 'completed')
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle"></i> Completed
                                    </span>
                                @endif
                            </div>
                            <span class="text-lg font-bold {{ $projectData['progress'] >= 80 ? 'text-green-600' : ($projectData['progress'] >= 50 ? 'text-blue-600' : ($projectData['progress'] >= 25 ? 'text-yellow-600' : 'text-red-600')) }}">
                                {{ $projectData['progress'] }}%
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3 mb-2">
                            <div class="{{ $projectData['progress'] >= 80 ? 'bg-green-500' : ($projectData['progress'] >= 50 ? 'bg-blue-500' : ($projectData['progress'] >= 25 ? 'bg-yellow-500' : 'bg-red-500')) }} h-3 rounded-full transition-all duration-300" 
                                 style="width: {{ $projectData['progress'] }}%"></div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500">
                            <span>
                                <i class="fas fa-tasks mr-1"></i>
                                Cards: {{ $projectData['completed_cards'] }}/{{ $projectData['total_cards'] }}
                            </span>
                            <span>
                                <i class="fas fa-list-check mr-1"></i>
                                Subtasks: {{ $projectData['completed_subtasks'] }}/{{ $projectData['total_subtasks'] }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Project List -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-list-alt mr-2 text-blue-500"></i>Detailed Project Overview
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Members</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deadline</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($projects as $project)
                        @php
                            $progressData = $projectProgress->firstWhere('project_name', $project->project_name);
                            $progress = $progressData['progress'] ?? 0;
                            $progressColor = $progress >= 80 ? 'bg-green-500' : ($progress >= 50 ? 'bg-blue-500' : ($progress >= 25 ? 'bg-yellow-500' : 'bg-red-500'));
                            $textColor = $progress >= 80 ? 'text-green-600' : ($progress >= 50 ? 'text-blue-600' : ($progress >= 25 ? 'text-yellow-600' : 'text-red-600'));
                        @endphp
                        <tr class="{{ $project->status === 'completed' ? 'bg-green-50' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $project->project_name }}</div>
                                        <div class="text-sm text-gray-500">Created by {{ $project->creator->full_name }}</div>
                                    </div>
                                    @if($project->status === 'completed')
                                        <i class="fas fa-check-circle text-green-500" title="Completed"></i>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="w-32">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-xs font-semibold {{ $textColor }}">{{ $progress }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="{{ $progressColor }} h-2 rounded-full transition-all" style="width: {{ $progress }}%"></div>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ $progressData['completed_cards'] ?? 0 }}/{{ $progressData['total_cards'] ?? 0 }} cards
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex -space-x-2">
                                    @foreach($project->members->take(3) as $member)
                                        <div class="h-8 w-8 rounded-full bg-gray-200 border-2 border-white flex items-center justify-center text-xs font-medium">
                                            {{ strtoupper(substr($member->user->full_name, 0, 2)) }}
                                        </div>
                                    @endforeach
                                    @if($project->members->count() > 3)
                                        <div class="h-8 w-8 rounded-full bg-gray-300 border-2 border-white flex items-center justify-center text-xs font-medium">
                                            +{{ $project->members->count() - 3 }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($project->deadline)
                                    @php
                                        $daysUntil = (int) floor(now()->diffInDays($project->deadline, false));
                                    @endphp
                                    <div class="text-sm {{ $daysUntil < 0 ? 'text-red-600' : ($daysUntil <= 7 ? 'text-orange-600' : 'text-gray-900') }}">
                                        {{ date('M d, Y', strtotime($project->deadline)) }}
                                    </div>
                                    <div class="mt-1">
                                        @if($daysUntil < 0)
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                <i class="fas fa-exclamation-circle mr-1"></i>{{ abs($daysUntil) }} hari terlambat
                                            </span>
                                        @elseif($daysUntil == 0)
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">
                                                <i class="fas fa-clock mr-1"></i>Hari ini!
                                            </span>
                                        @elseif($daysUntil <= 7)
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-hourglass-half mr-1"></i>{{ $daysUntil }} hari tersisa
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i>{{ $daysUntil }} hari tersisa
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-sm text-gray-500">No deadline</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $status = 'Active';
                                    $statusColor = 'green';
                                    if ($project->deadline) {
                                        if (now()->isAfter($project->deadline)) {
                                            $status = 'Overdue';
                                            $statusColor = 'red';
                                        } elseif (now()->diffInDays($project->deadline) <= 7) {
                                            $status = 'Due Soon';
                                            $statusColor = 'orange';
                                        }
                                    }
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800">
                                    {{ $status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.monitoring.project-details', $project->project_id) }}" class="text-blue-600 hover:text-blue-900">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection