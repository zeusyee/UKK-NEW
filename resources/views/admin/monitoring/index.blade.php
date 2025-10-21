@extends('layouts.admin')

@section('title', 'Project Monitoring')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
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

        <!-- Projects with Deadline -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">With Deadline</p>
                    <p class="text-2xl font-semibold text-gray-800">{{ $projectStats['with_deadline'] }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-calendar-check text-green-600 text-xl"></i>
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
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Member Distribution</h3>
            <div class="space-y-4">
                @foreach($memberDistribution as $project => $count)
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">{{ $project }}</span>
                            <span class="text-gray-800 font-medium">{{ $count }} members</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($count / ($workingUsers + $idleUsers)) * 100 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Project List -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Project Status Overview</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Members</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deadline</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($projects as $project)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $project->project_name }}</div>
                                <div class="text-sm text-gray-500">Created by {{ $project->creator->full_name }}</div>
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
                                        $daysUntil = now()->diffInDays($project->deadline, false);
                                    @endphp
                                    <div class="text-sm {{ $daysUntil < 0 ? 'text-red-600' : ($daysUntil <= 7 ? 'text-orange-600' : 'text-gray-900') }}">
                                        {{ date('M d, Y', strtotime($project->deadline)) }}
                                        @if($daysUntil < 0)
                                            <span class="text-red-600">({{ abs($daysUntil) }} days overdue)</span>
                                        @elseif($daysUntil <= 7)
                                            <span class="text-orange-600">({{ $daysUntil }} days left)</span>
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