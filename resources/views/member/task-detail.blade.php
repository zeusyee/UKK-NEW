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
                    <span class="px-3 py-1 text-sm rounded-full {{ $card->status === 'done' ? 'bg-green-100 text-green-800' : ($card->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                        {{ ucfirst(str_replace('_', ' ', $card->status)) }}
                    </span>
                </div>
                <p class="text-gray-600">
                    Project: <span class="font-semibold">{{ $project->project_name }}</span> / 
                    Board: <span class="font-semibold">{{ $board->board_name }}</span>
                </p>
            </div>
            
            <!-- Action Button -->
            @if($assignment->assignment_status === 'not_assigned' && $card->status === 'todo')
                <form action="{{ route('member.task.start', ['project' => $project, 'board' => $board, 'card' => $card]) }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                        <i class="fas fa-play mr-1"></i>Start Task
                    </button>
                </form>
            @endif
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
            </div>
        </div>
    </div>

    <!-- Subtasks Section -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-gray-800">
                My Subtasks 
                <span class="text-sm font-normal text-gray-500">
                    ({{ $card->subtasks->where('created_by', Auth::id())->where('status', 'done')->count() }}/{{ $card->subtasks->where('created_by', Auth::id())->count() }} completed)
                </span>
            </h3>
            @if($assignment->assignment_status !== 'not_assigned')
                <button onclick="toggleSubtaskForm()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                    <i class="fas fa-plus mr-2"></i>Add Subtask
                </button>
            @endif
        </div>

        <!-- Add Subtask Form (Hidden by default) -->
        <div id="subtask-form" class="hidden mb-6 p-4 bg-gray-50 rounded-lg">
            <form action="{{ route('member.subtask.store', ['project' => $project, 'board' => $board, 'card' => $card]) }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Subtask Title *</label>
                        <input type="text" name="subtask_title" required 
                               class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea name="description" rows="3" 
                                  class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Estimated Hours</label>
                        <input type="number" name="estimated_hours" step="0.5" min="0" 
                               class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div class="flex justify-end space-x-2 mt-4">
                    <button type="button" onclick="toggleSubtaskForm()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">
                        Cancel
                    </button>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                        <i class="fas fa-save mr-2"></i>Save Subtask
                    </button>
                </div>
            </form>
        </div>

        <!-- Subtasks List -->
        @php
            $mySubtasks = $card->subtasks->where('created_by', Auth::id());
        @endphp

        @if($mySubtasks->isEmpty())
            <div class="text-center py-12">
                <i class="fas fa-list-check text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500">No subtasks yet. Add your first subtask above.</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($mySubtasks->sortBy('position') as $subtask)
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div id="subtask-view-{{ $subtask->subtask_id }}" class="subtask-view">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3 mb-2">
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            {{ $subtask->status === 'done' ? 'bg-green-100 text-green-800' : 
                                               ($subtask->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 
                                               ($subtask->status === 'review' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800')) }}">
                                            {{ $subtask->status === 'review' ? 'In Review' : ucfirst(str_replace('_', ' ', $subtask->status)) }}
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

                                    <!-- Review Notes -->
                                    @if($subtask->review_notes && $subtask->reviewed_at)
                                        <div class="mt-3 p-3 {{ $subtask->status === 'done' ? 'bg-green-50' : 'bg-red-50' }} rounded">
                                            <p class="text-xs font-semibold {{ $subtask->status === 'done' ? 'text-green-700' : 'text-red-700' }} mb-1">
                                                @if($subtask->status === 'done')
                                                    <i class="fas fa-check-circle"></i> Approved
                                                @else
                                                    <i class="fas fa-exclamation-circle"></i> Revision Required
                                                @endif
                                            </p>
                                            <p class="text-xs text-gray-600">{{ $subtask->review_notes }}</p>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex space-x-2">
                                    @if($subtask->status === 'todo')
                                        <form action="{{ route('member.subtask.start', ['project' => $project, 'board' => $board, 'card' => $card, 'subtask' => $subtask]) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-800" title="Start">
                                                <i class="fas fa-play"></i>
                                            </button>
                                        </form>
                                    @endif

                                    @if($subtask->status === 'in_progress')
                                        <button onclick="openSubmitModal({{ $subtask->subtask_id }})" class="text-blue-600 hover:text-blue-800" title="Submit for Review">
                                            <i class="fas fa-paper-plane"></i>
                                        </button>
                                    @endif

                                    @if($subtask->status === 'todo' || $subtask->status === 'in_progress')
                                        <button onclick="toggleSubtaskEdit({{ $subtask->subtask_id }})" 
                                                class="text-blue-600 hover:text-blue-800" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('member.subtask.destroy', ['project' => $project, 'board' => $board, 'card' => $card, 'subtask' => $subtask]) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('Delete this subtask?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Edit Subtask Form (Hidden) -->
                        @if($subtask->status === 'todo' || $subtask->status === 'in_progress')
                            <div id="subtask-edit-{{ $subtask->subtask_id }}" class="subtask-edit hidden mt-4">
                                <form action="{{ route('member.subtask.update', ['project' => $project, 'board' => $board, 'card' => $card, 'subtask' => $subtask]) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="grid grid-cols-1 gap-3">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                                            <input type="text" name="subtask_title" value="{{ $subtask->subtask_title }}" required 
                                                   class="w-full border border-gray-300 rounded px-3 py-2">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                            <textarea name="description" rows="2" 
                                                      class="w-full border border-gray-300 rounded px-3 py-2">{{ $subtask->description }}</textarea>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Estimated Hours</label>
                                            <input type="number" name="estimated_hours" value="{{ $subtask->estimated_hours }}" step="0.5" min="0" 
                                                   class="w-full border border-gray-300 rounded px-3 py-2">
                                        </div>
                                    </div>
                                    <div class="flex justify-end space-x-2 mt-3">
                                        <button type="button" onclick="toggleSubtaskEdit({{ $subtask->subtask_id }})" 
                                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-3 py-1 rounded text-sm">
                                            Cancel
                                        </button>
                                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm">
                                            Save
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @endif

                        <!-- Submit Modal for this subtask -->
                        <div id="submit-modal-{{ $subtask->subtask_id }}" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                                <div class="mt-3">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Submit Subtask for Review</h3>
                                    
                                    <form action="{{ route('member.subtask.submit', ['project' => $project, 'board' => $board, 'card' => $card, 'subtask' => $subtask]) }}" method="POST">
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
                                                    onclick="closeSubmitModal({{ $subtask->subtask_id }})"
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
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <script>
        function toggleSubtaskForm() {
            const form = document.getElementById('subtask-form');
            form.classList.toggle('hidden');
        }

        function toggleSubtaskEdit(subtaskId) {
            const viewDiv = document.getElementById('subtask-view-' + subtaskId);
            const editDiv = document.getElementById('subtask-edit-' + subtaskId);
            viewDiv.classList.toggle('hidden');
            editDiv.classList.toggle('hidden');
        }

        function openSubmitModal(subtaskId) {
            document.getElementById('submit-modal-' + subtaskId).classList.remove('hidden');
        }

        function closeSubmitModal(subtaskId) {
            document.getElementById('submit-modal-' + subtaskId).classList.add('hidden');
        }
    </script>
@endsection