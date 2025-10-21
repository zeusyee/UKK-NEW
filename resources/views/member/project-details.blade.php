@extends('layouts.member')

@section('title', 'Project Details')

@section('content')
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold">{{ $project->name }}</h2>
            <span class="px-3 py-1 rounded-full {{ $project->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                {{ ucfirst($project->status) }}
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

            <!-- Project Leader -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-lg font-semibold mb-3">Project Leader</h3>
                <div class="flex items-center">
                    <div class="ml-3">
                        <p class="font-medium">{{ $project->leader->name }}</p>
                        <p class="text-sm text-gray-600">{{ $project->leader->email }}</p>
                    </div>
                </div>
            </div>

            <!-- Project Timeline -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-lg font-semibold mb-3">Timeline</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Start Date:</span>
                        <span>{{ $project->start_date->format('M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">End Date:</span>
                        <span>{{ $project->end_date->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Project Description -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold mb-3">Description</h3>
            <p class="text-gray-600">{{ $project->description }}</p>
        </div>

        <!-- Project Tasks -->
        <div>
            <h3 class="text-lg font-semibold mb-4">Tasks</h3>
            @if($project->tasks->isEmpty())
                <p class="text-gray-500">No tasks have been assigned to this project yet.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border rounded-lg">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($project->tasks as $task)
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">{{ $task->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $task->description }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $task->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ ucfirst($task->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ $task->due_date->format('M d, Y') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection