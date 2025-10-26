@extends('layouts.admin')

@section('title', 'Project Details - ' . $project->project_name)

@section('content')
        @if(session('success'))
        <div class="alert-notification bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 flex items-center justify-between" role="alert">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900">
                <i class="fas fa-times"></i>
            </button>
        </div>
        @endif

        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:px-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            {{ $project->project_name }}
                        </h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">
                            Created by {{ $project->creator->full_name }}
                        </p>
                    </div>
                    <div>
                        @if($project->status === 'completed')
                            <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-lg bg-green-100 text-green-800 border-2 border-green-200">
                                <i class="fas fa-check-circle mr-2"></i> Completed
                            </span>
                        @else
                            <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-lg bg-blue-100 text-blue-800 border-2 border-blue-200">
                                <i class="fas fa-play-circle mr-2"></i> Active
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-200">
                <dl>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $project->description ?? 'No description provided' }}
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            @if($project->status === 'completed')
                                <div class="space-y-1">
                                    <div class="flex items-center">
                                        <i class="fas fa-check-double text-green-500 mr-2"></i>
                                        <span class="font-semibold text-green-700">Project Completed</span>
                                    </div>
                                    @if($project->completed_at)
                                        <div class="text-sm text-gray-600">
                                            <i class="fas fa-calendar-check mr-2"></i>
                                            Completed on {{ $project->completed_at->format('F j, Y, g:i a') }}
                                        </div>
                                    @endif
                                    @if($project->completed_by && $project->completedBy)
                                        <div class="text-sm text-gray-600">
                                            <i class="fas fa-user-check mr-2"></i>
                                            Completed by {{ $project->completedBy->full_name }}
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="flex items-center">
                                    <i class="fas fa-play-circle text-blue-500 mr-2"></i>
                                    <span class="font-semibold text-blue-700">Active Project</span>
                                </div>
                            @endif
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Deadline</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            @if($project->deadline)
                                <div class="flex items-center">
                                    <span>{{ date('F j, Y', strtotime($project->deadline)) }}</span>
                                    @php
                                        $daysUntil = (int) floor(now()->diffInDays($project->deadline, false));
                                    @endphp
                                    @if($daysUntil < 0)
                                        <span class="ml-2 px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                            <i class="fas fa-exclamation-circle mr-1"></i>{{ abs($daysUntil) }} hari terlambat
                                        </span>
                                    @elseif($daysUntil == 0)
                                        <span class="ml-2 px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">
                                            <i class="fas fa-clock mr-1"></i>Hari ini!
                                        </span>
                                    @elseif($daysUntil <= 7)
                                        <span class="ml-2 px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-hourglass-half mr-1"></i>{{ $daysUntil }} hari tersisa
                                        </span>
                                    @else
                                        <span class="ml-2 px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>{{ $daysUntil }} hari tersisa
                                        </span>
                                    @endif
                                </div>
                            @else
                                <span class="text-gray-500">No deadline set</span>
                            @endif
                        </dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Created At</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ date('F j, Y, g:i a', strtotime($project->created_at)) }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Project Members
                    </h3>
                    @if($project->status === 'completed')
                        <p class="mt-1 text-sm text-gray-500">
                            <i class="fas fa-lock mr-1"></i>Member management is locked for completed projects
                        </p>
                    @endif
                </div>
                @if($project->status === 'active')
                    <a href="{{ route('admin.projects.members', $project) }}" 
                       class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-colors">
                        <i class="fas fa-users-cog mr-2"></i>Manage Members
                    </a>
                @else
                    <button disabled
                            class="bg-gray-300 text-gray-500 font-bold py-2 px-4 rounded cursor-not-allowed" 
                            title="Cannot manage members of completed project">
                        <i class="fas fa-lock mr-2"></i>Locked
                    </button>
                @endif
            </div>
            <div class="border-t border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Member Name
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Role
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Joined At
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($project->members as $member)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $member->user->full_name }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $member->user->email }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ ucfirst($member->role) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ date('M d, Y', strtotime($member->joined_at)) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection