@extends('layouts.member')

@section('title', 'Task Details')

@section('content')
    <div class="mb-6">
        <a href="{{ route('member.my-tasks') }}" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left mr-2"></i>Back to My Tasks
        </a>
    </div>

    <!-- Task Header -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex justify-between items-start mb-4">
            <div class="flex-1">
                <div class="flex items-center space-x-3 mb-2">
                    <h2 class="text-2xl font-bold text-gray-800">{{ $card->card_title }}</h2>
                    <span class="px-3 py-1 text-sm rounded-full {{ $card->priority === 'high' ? 'bg-red-100 text-red-800' : ($card->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                        {{ ucfirst($card->priority) }} Priority
                    </span>
                    <span class="px-3 py-1 text-sm rounded-full {{ $card->status === 'done' ? 'bg-green-100 text-green-800' : ($card->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : ($card->status === 'review' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800')) }}">
                        {{ ucfirst(str_replace('_', ' ', $card->status)) }}
                    </span>
                </div>
                <p class="text-gray-600">
                    Project: <span class="font-semibold">{{ $project->project_name }}</span> / 
                    Board: <span class="font-semibold">{{ $board->board_name }}</span>
                </p>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex space-x-2">
                @if($assignment->assignment_status === 'not_assigned' && $card->status === 'todo')
                    <form action="{{ route('member.task.start', ['project' => $project, 'board' => $board, 'card' => $card]) }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                            <i class="fas fa-play mr-1"></i>Start Task
                        </button>
                    </form>
                @endif

                @if($assignment->assignment_status === 'in_progress' && $card->status === 'in_progress')
                    <button onclick="document.getElementById('submit-modal').classList.remove('hidden')" 
                            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                        <i class="fas fa-check mr-1"></i>Submit for Review
                    </button>
                @endif
            </div>
        </div>

        @if($card->description)
            <div class="mt-4 p-4 bg-gray-50 rounded">
                <h3 class="font-semibold text-gray-700 mb-2">Description</h3>
                <p class="text-gray-600">{{ $card->description }}</p>
            </div>
        @endif

        <!-- Task Meta Information -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
            <div class="bg-gray-50 p-3 rounded">
                <p class="text-xs text-gray-500 mb-1">Created By</p>
                <p class="font-semibold text-gray-800">{{ $card->creator->full_name }}</p>
            </div>
            <div class="bg-gray-50 p-3 rounded">
                <p class="text-xs text-gray-500 mb-1">Due Date</p>
                <p class="font-semibold text-gray-800">
                    @if($card->due_date)
                        {{ $card->due_date->format('M d, Y') }}
                    @else
                        <span class="text-gray-400">Not set</span>
                    @endif
                </p>
            </div>
            <div class="bg-gray-50 p-3 rounded">
                <p class="text-xs text-gray-500 mb-1">Estimated Hours</p>
                <p class="font-semibold text-gray-800">{{ $card->estimated_hours ?? '-' }}</p>
            </div>
            <div class="bg-gray-50 p-3 rounded">
                <p class="text-xs text-gray-500 mb-1">Actual Hours</p>
                <p class="font-semibold text-gray-800">{{ $card->actual_hours ?? '-' }}</p>
            </div>
        </div>

        <!-- Assignment Status -->
        <div class="mt-6 p-4 bg-blue-50 rounded">
            <h3 class="font-semibold text-gray-700 mb-2">Your Assignment Status</h3>
            <div class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Status:</span>
                    <span class="font-semibold">{{ ucfirst(str_replace('_', ' ', $assignment->assignment_status)) }}</span>
                </div>
                @if($assignment->started_at)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Started At:</span>
                        <span class="font-semibold">{{ $assignment->started_at->format('M d, Y H:i') }}</span>
                    </div>
                @endif
                @if($assignment->completed_at)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Submitted At:</span>
                        <span class="font-semibold">{{ $assignment->completed_at->format('M d, Y H:i') }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Review Notes -->
        @if($card->review_notes && $card->reviewed_at)
            <div class="mt-6 p-4 {{ $card->status === 'done' ? 'bg-green-50' : 'bg-red-50' }} rounded">
                <h3 class="font-semibold text-gray-700 mb-2">
                    @if($card->status === 'done')
                        <i class="fas fa-check-circle text-green-600"></i> Task Approved
                    @else
                        <i class="fas fa-exclamation-circle text-red-600"></i> Revision Required
                    @endif
                </h3>
                <p class="text-sm text-gray-600 mb-2">{{ $card->review_notes }}</p>
                <div class="flex justify-between text-xs text-gray-500">
                    <span>Reviewed by: {{ $card->reviewer->full_name }}</span>
                    <span>{{ $card->reviewed_at->format('M d, Y H:i') }}</span>
                </div>
            </div>
        @endif
    </div>

    <!-- Subtasks Section -->
    @if($card->subtasks->count() > 0)
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">
                Subtasks 
                <span class="text-sm font-normal text-gray-500">
                    ({{ $card->subtasks->where('status', 'done')->count() }}/{{ $card->subtasks->count() }} completed)
                </span>
            </h3>

            <div class="space-y-3">
                @foreach($card->subtasks->sortBy('position') as $subtask)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $subtask->status === 'done' ? 'bg-green-100 text-green-800' : ($subtask->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                        {{ ucfirst(str_replace('_', ' ', $subtask->status)) }}
                                    </span>
                                    <h4 class="font-semibold text-gray-800">{{ $subtask->subtask_title }}</h4>
                                </div>
                                @if($subtask->description)
                                    <p class="text-sm text-gray-600 mb-2">{{ $subtask->description }}</p>
                                @endif
                                <div class="flex space-x-4 text-xs text-gray-500">
                                    @if($subtask->estimated_hours)
                                        <span><i class="fas fa-clock mr-1"></i>Est: {{ $subtask->estimated_hours }}h</span>
                                    @endif
                                    @if($subtask->actual_hours)
                                        <span><i class="fas fa-check-circle mr-1"></i>Actual: {{ $subtask->actual_hours }}h</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Submit Task Modal -->
    <div id="submit-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Submit Task for Review</h3>
                
                <form action="{{ route('member.task.submit', ['project' => $project, 'board' => $board, 'card' => $card]) }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Actual Hours Spent</label>
                        <input type="number" 
                               name="actual_hours" 
                               step="0.5" 
                               min="0"
                               class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="e.g., 5.5">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Completion Notes (Optional)</label>
                        <textarea name="completion_notes" 
                                  rows="4"
                                  class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Add any notes about the completed work..."></textarea>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" 
                                onclick="document.getElementById('submit-modal').classList.add('hidden')"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                            <i class="fas fa-paper-plane mr-2"></i>Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection