@extends('layouts.admin')

@section('title', 'Projects')

@section('content')

        <div class="px-4 py-6 sm:px-0">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Projects</h2>
                <a href="{{ route('admin.projects.create') }}" 
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Create New Project
                </a>
            </div>

            @php
                $readyToCompleteProjects = $projects->filter(function($project) {
                    $project->load('boards.cards');
                    return $project->isReadyToComplete();
                });
            @endphp

            @if($readyToCompleteProjects->count() > 0)
                <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-lg shadow-md">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-lg font-semibold text-green-800 mb-2">
                                <i class="fas fa-trophy mr-2"></i>Projects Ready to Complete!
                            </h3>
                            <p class="text-sm text-green-700 mb-3">
                                The following {{ $readyToCompleteProjects->count() }} project(s) have all cards completed and are ready to be marked as finished:
                            </p>
                            <div class="space-y-2">
                                @foreach($readyToCompleteProjects as $project)
                                    <div class="bg-white p-3 rounded-lg border border-green-200 flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <i class="fas fa-project-diagram text-green-600"></i>
                                            <div>
                                                <p class="font-semibold text-gray-800">{{ $project->project_name }}</p>
                                                <p class="text-xs text-gray-500">All tasks completed</p>
                                            </div>
                                        </div>
                                        <form action="{{ route('admin.projects.complete', $project) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    onclick="return confirm('Mark this project as completed? All team members will be set to idle status.')"
                                                    class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg transition-colors shadow-sm">
                                                <i class="fas fa-check-double mr-2"></i>Complete Project
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Project Name
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Created By
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Deadline
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($projects as $project)
                        <tr class="{{ $project->status === 'completed' ? 'bg-gray-50' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $project->project_name }}
                                    @if($project->status === 'completed')
                                        <i class="fas fa-check-circle text-green-500 ml-2" title="Completed"></i>
                                    @endif
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ Str::limit($project->description, 50) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($project->status === 'completed')
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i> Completed
                                    </span>
                                    @if($project->completed_at)
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ $project->completed_at->format('M d, Y') }}
                                        </div>
                                    @endif
                                @else
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        <i class="fas fa-play-circle mr-1"></i> Active
                                    </span>
                                    @php
                                        $project->load('boards.cards');
                                        if ($project->isReadyToComplete()) {
                                            echo '<div class="text-xs text-green-600 mt-1 font-semibold"><i class="fas fa-trophy mr-1"></i>Ready to complete!</div>';
                                        }
                                    @endphp
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $project->creator->full_name }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                @if($project->deadline)
                                    <div>{{ date('M d, Y', strtotime($project->deadline)) }}</div>
                                    @php
                                        $daysUntil = (int) floor(now()->diffInDays($project->deadline, false));
                                    @endphp
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
                                    <span class="text-gray-400">No deadline</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.projects.show', $project) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                    <i class="fas fa-eye mr-1"></i>View
                                </a>
                                
                                @if($project->status === 'active')
                                    <a href="{{ route('admin.projects.edit', $project) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                        <i class="fas fa-edit mr-1"></i>Edit
                                    </a>
                                    <form action="{{ route('admin.projects.destroy', $project) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" 
                                                onclick="return confirm('Are you sure you want to delete this project?')">
                                            <i class="fas fa-trash mr-1"></i>Delete
                                        </button>
                                    </form>
                                @else
                                    <span class="text-gray-400 mr-3" title="Cannot edit completed project">
                                        <i class="fas fa-lock mr-1"></i>Locked
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection