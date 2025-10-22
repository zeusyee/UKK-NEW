@extends('layouts.member')

@section('title', 'Project Details')

@section('content')
    <div class="mb-6">
        <a href="{{ route('member.dashboard') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-block">
            <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold">{{ $project->project_name }}</h2>
            <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-800">
                Active
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Project Stats -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-lg font-semibold mb-3">Project Progress</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tasks Completed:</span>
                        <span class="font-medium">{{ $projectStats['completed_tasks'] }}/{{ $projectStats['total_tasks'] }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $projectStats['progress_percentage'] }}%"></div>
                    </div>
                    <div class="text-right text-sm text-gray-600">
                        {{ $projectStats['progress_percentage'] }}% Complete
                    </div>
                </div>
            </div>

            <!-- Project Creator -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-lg font-semibold mb-3">Project Creator</h3>
                <div class="flex items-center">
                    <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-semibold text-lg">
                        {{ strtoupper(substr($project->creator->full_name, 0, 2)) }}
                    </div>
                    <div class="ml-3">
                        <p class="font-medium">{{ $project->creator->full_name }}</p>
                        <p class="text-sm text-gray-600">{{ $project->creator->email }}</p>
                    </div>
                </div>
            </div>

            <!-- Project Timeline -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-lg font-semibold mb-3">Timeline</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Created:</span>
                        <span>{{ $project->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Deadline:</span>
                        <span>
                            @if($project->deadline)
                                {{ $project->deadline->format('M d, Y') }}
                            @else
                                No deadline
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Project Description -->
        @if($project->description)
        <div class="mb-8">
            <h3 class="text-lg font-semibold mb-3">Description</h3>
            <p class="text-gray-600">{{ $project->description }}</p>
        </div>
        @endif

        <!-- Team Members -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold mb-4">Team Members</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($project->members as $member)
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-semibold text-lg">
                                {{ strtoupper(substr($member->user->full_name, 0, 2)) }}
                            </div>
                            <div class="ml-3">
                                <p class="font-medium">{{ $member->user->full_name }}</p>
                                <p class="text-sm text-gray-600">{{ ucfirst($member->role) }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Project Boards -->
        <div>
            <h3 class="text-lg font-semibold mb-4">Boards & Tasks</h3>
            @if($project->boards->isEmpty())
                <p class="text-gray-500">No boards have been created for this project yet.</p>
            @else
                <div class="space-y-6">
                    @foreach($project->boards as $board)
                        <div class="border rounded-lg p-4">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="text-lg font-semibold">{{ $board->board_name }}</h4>
                                <span class="text-sm text-gray-600">{{ $board->cards->count() }} tasks</span>
                            </div>
                            
                            @if($board->description)
                                <p class="text-sm text-gray-600 mb-4">{{ $board->description }}</p>
                            @endif

                            @if($board->cards->isEmpty())
                                <p class="text-gray-500 text-sm">No tasks in this board.</p>
                            @else
                                <div class="overflow-x-auto">
                                    <table class="min-w-full bg-white border rounded">
                                        <thead>
                                            <tr class="bg-gray-50">
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Task</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Priority</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Due Date</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            @foreach($board->cards as $card)
                                                <tr>
                                                    <td class="px-4 py-3">
                                                        <div class="text-sm text-gray-900 font-medium">{{ $card->card_title }}</div>
                                                        @if($card->description)
                                                            <div class="text-xs text-gray-500">{{ Str::limit($card->description, 50) }}</div>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                            {{ $card->status === 'done' ? 'bg-green-100 text-green-800' : 
                                                               ($card->status === 'in_progress' ? 'bg-yellow-100 text-yellow-800' : 
                                                               ($card->status === 'review' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')) }}">
                                                            {{ ucfirst(str_replace('_', ' ', $card->status)) }}
                                                        </span>
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                            {{ $card->priority === 'high' ? 'bg-red-100 text-red-800' : 
                                                               ($card->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                                            {{ ucfirst($card->priority) }}
                                                        </span>
                                                    </td>
                                                    <td class="px-4 py-3 text-sm text-gray-500">
                                                        @if($card->due_date)
                                                            {{ $card->due_date->format('M d, Y') }}
                                                        @else
                                                            <span class="text-gray-400">No due date</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection