@extends('layouts.leader')

@section('title', 'Review Subtask')

@section('content')
    <div class="mb-6">
        <a href="{{ route('leader.review.index') }}" class="text-green-600 hover:text-green-800">
            <i class="fas fa-arrow-left mr-2"></i>Back to Review List
        </a>
    </div>

    <!-- Subtask Header -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex justify-between items-start mb-4">
            <div class="flex-1">
                <div class="flex items-center space-x-3 mb-2">
                    <h2 class="text-2xl font-bold text-gray-800">{{ $subtask->subtask_title }}</h2>
                    <span class="px-3 py-1 text-sm rounded-full bg-purple-100 text-purple-800">
                        In Review
                    </span>
                </div>
                <p class="text-gray-600">
                    Card: <span class="font-semibold">{{ $card->card_title }}</span> / 
                    Project: <span class="font-semibold">{{ $project->project_name }}</span>
                </p>
            </div>
        </div>

        @if($subtask->description)
            <div class="mt-4 p-4 bg-gray-50 rounded">
                <h3 class="font-semibold text-gray-700 mb-2">Description</h3>
                <p class="text-gray-600">{{ $subtask->description }}</p>
            </div>
        @endif

        <!-- Subtask Meta Information -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
            <div class="bg-gray-50 p-3 rounded">
                <p class="text-xs text-gray-500 mb-1">Created By</p>
                <div class="flex items-center">
                    <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center text-white text-xs font-semibold mr-2">
                        {{ strtoupper(substr($subtask->creator->full_name, 0, 1)) }}
                    </div>
                    <p class="font-semibold text-gray-800">{{ $subtask->creator->full_name }}</p>
                </div>
            </div>
            <div class="bg-gray-50 p-3 rounded">
                <p class="text-xs text-gray-500 mb-1">Created At</p>
                <p class="font-semibold text-gray-800">{{ $subtask->created_at->format('M d, Y H:i') }}</p>
            </div>
            <div class="bg-gray-50 p-3 rounded">
                <p class="text-xs text-gray-500 mb-1">Estimated Hours</p>
                <p class="font-semibold text-gray-800">{{ $subtask->estimated_hours ?? '-' }}</p>
            </div>
            <div class="bg-gray-50 p-3 rounded">
                <p class="text-xs text-gray-500 mb-1">Actual Hours</p>
                <p class="font-semibold text-gray-800">{{ $subtask->actual_hours ?? '-' }}</p>
            </div>
        </div>

        <!-- Completion Notes -->
        @if($subtask->completion_notes)
            <div class="mt-6 p-4 bg-blue-50 rounded">
                <h3 class="font-semibold text-gray-700 mb-2">Completion Notes from Member</h3>
                <p class="text-gray-600">{{ $subtask->completion_notes }}</p>
            </div>
        @endif

        <!-- Card Context -->
        <div class="mt-6 p-4 bg-gray-50 rounded">
            <h3 class="font-semibold text-gray-700 mb-2">Parent Card Information</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-gray-500">Card Title</p>
                    <p class="text-sm font-medium">{{ $card->card_title }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Card Priority</p>
                    <span class="px-2 py-1 text-xs rounded-full {{ $card->priority === 'high' ? 'bg-red-100 text-red-800' : ($card->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                        {{ ucfirst($card->priority) }}
                    </span>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Card Status</p>
                    <span class="px-2 py-1 text-xs rounded-full {{ $card->status === 'done' ? 'bg-green-100 text-green-800' : ($card->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                        {{ ucfirst(str_replace('_', ' ', $card->status)) }}
                    </span>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Due Date</p>
                    <p class="text-sm">{{ $card->due_date ? $card->due_date->format('M d, Y') : 'Not set' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Review Actions -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Review Actions</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Approve -->
            <div class="border-2 border-green-200 rounded-lg p-6">
                <h4 class="text-lg font-semibold text-green-700 mb-3">
                    <i class="fas fa-check-circle mr-2"></i>Approve Subtask
                </h4>
                <p class="text-gray-600 text-sm mb-4">Mark this subtask as completed and approved.</p>
                
                <form action="{{ route('leader.review.approve', ['project' => $project, 'board' => $board, 'card' => $card, 'subtask' => $subtask]) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Leader Notes (Optional)</label>
                        <textarea name="leader_notes" 
                                  rows="3"
                                  class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                                  placeholder="Add any feedback or notes..."></textarea>
                    </div>
                    <button type="submit" 
                            class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-3 rounded"
                            onclick="return confirm('Are you sure you want to approve this subtask?')">
                        <i class="fas fa-check mr-2"></i>Approve Subtask
                    </button>
                </form>
            </div>

            <!-- Reject -->
            <div class="border-2 border-red-200 rounded-lg p-6">
                <h4 class="text-lg font-semibold text-red-700 mb-3">
                    <i class="fas fa-times-circle mr-2"></i>Request Revision
                </h4>
                <p class="text-gray-600 text-sm mb-4">Send this subtask back for revision.</p>
                
                <form action="{{ route('leader.review.reject', ['project' => $project, 'board' => $board, 'card' => $card, 'subtask' => $subtask]) }}" method="POST">
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
                            onclick="return confirm('Are you sure you want to request revision for this subtask?')">
                        <i class="fas fa-undo mr-2"></i>Request Revision
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection