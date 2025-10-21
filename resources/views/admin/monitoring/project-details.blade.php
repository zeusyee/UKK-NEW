@extends('layouts.admin')

@section('title', 'Project Details - ' . $project->project_name)

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.monitoring.index') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-block">
            <i class="fas fa-arrow-left mr-2"></i>Back to Monitoring Dashboard
        </a>
    </div>

    <!-- Project Overview -->
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="p-6">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">{{ $project->project_name }}</h2>
                    <p class="text-gray-600 mt-1">Created by {{ $project->creator->full_name }}</p>
                </div>
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
                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800">
                    {{ $status }}
                </span>
            </div>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm font-medium text-gray-600">Created</p>
                    <p class="text-lg font-semibold text-gray-800">{{ $project->created_at->format('M d, Y') }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm font-medium text-gray-600">Deadline</p>
                    @if($project->deadline)
                        <p class="text-lg font-semibold {{ now()->isAfter($project->deadline) ? 'text-red-600' : 'text-gray-800' }}">
                            {{ date('M d, Y', strtotime($project->deadline)) }}
                        </p>
                        @php
                            $daysUntil = now()->diffInDays($project->deadline, false);
                        @endphp
                        @if($daysUntil < 0)
                            <p class="text-sm text-red-600">{{ abs($daysUntil) }} days overdue</p>
                        @else
                            <p class="text-sm text-gray-600">{{ $daysUntil }} days remaining</p>
                        @endif
                    @else
                        <p class="text-lg font-semibold text-gray-800">No deadline</p>
                    @endif
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm font-medium text-gray-600">Total Members</p>
                    <p class="text-lg font-semibold text-gray-800">{{ $project->members->count() }}</p>
                </div>
            </div>

            @if($project->description)
                <div class="mt-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Description</h3>
                    <p class="text-gray-600">{{ $project->description }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Team Members -->
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Team Members</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($project->members as $member)
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-semibold text-lg">
                                {{ strtoupper(substr($member->user->full_name, 0, 2)) }}
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">{{ $member->user->full_name }}</p>
                                <p class="text-sm text-gray-500">{{ ucfirst($member->role) }}</p>
                            </div>
                        </div>
                        <div class="mt-2">
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $member->user->current_task_status === 'working' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($member->user->current_task_status) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection