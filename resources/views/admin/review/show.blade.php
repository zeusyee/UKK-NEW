@extends('layouts.admin')

@section('title', 'Review Task')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.review.index') }}" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left mr-2"></i>Back to Review List
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
                    <span class="px-3 py-1 text-sm rounded-full bg-purple-100 text-purple-800">
                        In Review
                    </span>
                </div>
                <p class="text-gray-600">
                    Project: <span class="font-semibold">{{ $card->board->project->project_name }}</span> / 
                    Board: <span class="font-semibold">{{ $card->board->board_name }}</span>
                </p>
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
                        @if($card->due_date->isPast())
                            <span class="text-red-600 text-xs">(Overdue)</span>
                        @endif
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

        <!-- Assigned Members -->
        @if($card->assignments->count() > 0)
            <div class="mt-6">
                <h3 class="font-semibold text-gray-700 mb-3">Assigned To</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($card->assignments as $assignment)
                        <div class="flex items-center bg-blue-50 px-3 py-2 rounded-full">
                            <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold mr-2">
                                {{ strtoupper(substr($assignment->user->full_name, 0, 1)) }}
                            </div>
                            <div>
                                <span class="text-sm font-medium">{{ $assignment->user->full_name }}</span>
                                @if($assignment->completed_at)
                                    <p class="text-xs text-gray-500">Submitted: {{ $assignment->completed_at->format('M d, H:i') }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Completion Notes -->
        @if($card->review_notes)
            <div class="mt-6 p-4 bg-blue-50 rounded">
                <h3 class="font-semibold text-gray-700 mb-2">Completion Notes from Team</h3>
                <p class="text-gray-600">{{ $card->review_notes }}</p>
            </div>
        @endif
    </div>

    <!-- Subtasks Section -->
    @if($card->subtasks->count() > 0)
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
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

    <!-- Review Actions -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Review Actions</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Approve -->
            <div class="border-2 border-green-200 rounded-lg p-6">
                <h4 class="text-lg font-semibold text-green-700 mb-3">
                    <i class="fas fa-check-circle mr-2"></i>Approve Task
                </h4>
                <p class="text-gray-600 text-sm mb-4">Mark this task as completed and approved.</p>
                
                <form action="{{ route('admin.review.approve', $card) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Admin Notes (Optional)</label>
                        <textarea name="admin_notes" 
                                  rows="3"
                                  class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                                  placeholder="Add any feedback or notes..."></textarea>
                    </div>
                    <button type="submit" 
                            class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-3 rounded"
                            onclick="return confirm('Are you sure you want to approve this task?')">
                        <i class="fas fa-check mr-2"></i>Approve Task
                    </button>
                </form>
            </div>

            <!-- Reject -->
            <div class="border-2 border-red-200 rounded-lg p-6">
                <h4 class="text-lg font-semibold text-red-700 mb-3">
                    <i class="fas fa-times-circle mr-2"></i>Request Revision
                </h4>
                <p class="text-gray-600 text-sm mb-4">Send this task back for revision.</p>
                
                <form action="{{ route('admin.review.reject', $card) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Revision *</label>
                        <textarea name="rejection_reason" 
                                  rows="3"
                                  required
                                  class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500"
                                  placeholder="Explain what needs to be revised..."></textarea>
                    </div>
                    <button type="submit" 
                            class="w-full bg-red-500 hover:bg-red-600 text-white font-semibold py-3 rounded"
                            onclick="return confirm('Are you sure you want to request revision for this task?')">
                        <i class="fas fa-undo mr-2"></i>Request Revision
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection