@extends('layouts.member')

@section('title', 'My Tasks')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">My Assigned Tasks</h2>
        <p class="text-gray-600 mt-2">Manage and track your assigned tasks</p>
    </div>

    <!-- Task Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Tasks</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $assignments->count() }}</p>
                </div>
                <i class="fas fa-tasks text-3xl text-gray-300"></i>
            </div>
        </div>

        <div class="bg-yellow-50 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Not Started</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $assignments->where('assignment_status', 'not_assigned')->count() }}</p>
                </div>
                <i class="fas fa-circle text-3xl text-yellow-300"></i>
            </div>
        </div>

        <div class="bg-blue-50 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">In Progress</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $assignments->where('assignment_status', 'in_progress')->count() }}</p>
                </div>
                <i class="fas fa-spinner text-3xl text-blue-300"></i>
            </div>
        </div>

        <div class="bg-green-50 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Completed</p>
                    <p class="text-2xl font-bold text-green-600">{{ $assignments->where('assignment_status', 'completed')->count() }}</p>
                </div>
                <i class="fas fa-check-circle text-3xl text-green-300"></i>
            </div>
        </div>
    </div>

    <!-- Tasks List -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">All Tasks</h3>
        </div>
        
        @if($assignments->isEmpty())
            <div class="p-12 text-center">
                <i class="fas fa-clipboard-list text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg">No tasks assigned yet.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project / Board</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($assignments as $assignment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $assignment->card->card_title }}</div>
                                    @if($assignment->card->description)
                                        <div class="text-xs text-gray-500">{{ Str::limit($assignment->card->description, 50) }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $assignment->card->board->project->project_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $assignment->card->board->board_name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                        {{ $assignment->card->priority === 'high' ? 'bg-red-100 text-red-800' : 
                                           ($assignment->card->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                        {{ ucfirst($assignment->card->priority) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                        {{ $assignment->assignment_status === 'completed' ? 'bg-green-100 text-green-800' : 
                                           ($assignment->assignment_status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                        {{ ucfirst(str_replace('_', ' ', $assignment->assignment_status)) }}
                                    </span>
                                    @if($assignment->card->status === 'review')
                                        <span class="ml-1 px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                            In Review
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($assignment->card->due_date)
                                        {{ $assignment->card->due_date->format('M d, Y') }}
                                    @else
                                        <span class="text-gray-400">No due date</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('member.task.show', ['project' => $assignment->card->board->project->project_id, 'board' => $assignment->card->board->board_id, 'card' => $assignment->card->card_id]) }}" 
                                       class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye mr-1"></i>View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection