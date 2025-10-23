@extends('layouts.leader')

@section('title', 'Card Details')

@section('content')
    <div class="mb-6">
        <a href="{{ route('leader.project.details', $project) }}" class="text-green-600 hover:text-green-800">
            <i class="fas fa-arrow-left mr-2"></i>Back to Project
        </a>
    </div>

    <!-- Card Header -->
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
                <p class="text-gray-600">Board: <span class="font-semibold">{{ $board->board_name }}</span></p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('leader.card.edit', ['project' => $project, 'board' => $board, 'card' => $card]) }}" 
                   class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                    <i class="fas fa-edit mr-1"></i>Edit Card
                </a>
                <form action="{{ route('leader.card.destroy', ['project' => $project, 'board' => $board, 'card' => $card]) }}" 
                      method="POST" 
                      onsubmit="return confirm('Are you sure you want to delete this card?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                        <i class="fas fa-trash mr-1"></i>Delete
                    </button>
                </form>
            </div>
        </div>

        @if($card->description)
            <div class="mt-4 p-4 bg-gray-50 rounded">
                <h3 class="font-semibold text-gray-700 mb-2">Description</h3>
                <p class="text-gray-600">{{ $card->description }}</p>
            </div>
        @endif

        <!-- Card Meta Information -->
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
                            <span class="text-sm font-medium">{{ $assignment->user->full_name }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Subtasks Section -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-gray-800">
                Subtasks 
                <span class="text-sm font-normal text-gray-500">
                    ({{ $card->subtasks->where('status', 'done')->count() }}/{{ $card->subtasks->count() }} completed)
                </span>
            </h3>
            <div class="text-sm text-gray-600">
                <i class="fas fa-info-circle mr-1"></i>Members create and manage their own subtasks
            </div>
        </div>

        <!-- Subtasks List -->
        @if($card->subtasks->isEmpty())
            <div class="text-center py-12">
                <i class="fas fa-list-check text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500">No subtasks yet.</p>
                <p class="text-gray-400 text-sm mt-2">Assigned members will create subtasks for this card.</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($card->subtasks->sortBy('position') as $subtask)
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $subtask->status === 'done' ? 'bg-green-100 text-green-800' : ($subtask->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : ($subtask->status === 'review' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800')) }}">
                                        {{ $subtask->status === 'review' ? 'In Review' : ucfirst(str_replace('_', ' ', $subtask->status)) }}
                                    </span>
                                    <h4 class="font-semibold text-gray-800">{{ $subtask->subtask_title }}</h4>
                                </div>
                                @if($subtask->description)
                                    <p class="text-sm text-gray-600 mb-2">{{ $subtask->description }}</p>
                                @endif
                                <div class="flex space-x-4 text-xs text-gray-500 mb-2">
                                    <span><i class="fas fa-user mr-1"></i>{{ $subtask->creator->full_name }}</span>
                                    @if($subtask->estimated_hours)
                                        <span><i class="fas fa-clock mr-1"></i>Est: {{ $subtask->estimated_hours }}h</span>
                                    @endif
                                    @if($subtask->actual_hours)
                                        <span><i class="fas fa-check-circle mr-1"></i>Actual: {{ $subtask->actual_hours }}h</span>
                                    @endif
                                </div>

                                <!-- Review Info -->
                                @if($subtask->status === 'review')
                                    <div class="mt-2 p-2 bg-purple-50 rounded text-xs">
                                        <i class="fas fa-clock text-purple-600"></i>
                                        <span class="text-purple-800 font-medium">Pending your review</span>
                                        <a href="{{ route('leader.review.show', ['project' => $project, 'board' => $board, 'card' => $card, 'subtask' => $subtask]) }}" 
                                           class="ml-2 text-purple-600 hover:text-purple-800 underline">
                                            Review Now
                                        </a>
                                    </div>
                                @endif

                                @if($subtask->status === 'done' && $subtask->reviewed_at)
                                    <div class="mt-2 p-2 bg-green-50 rounded text-xs">
                                        <i class="fas fa-check-circle text-green-600"></i>
                                        <span class="text-green-800">Approved by {{ $subtask->reviewer->full_name }}</span>
                                        <span class="text-gray-500 ml-2">{{ $subtask->reviewed_at->diffForHumans() }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="flex space-x-2">
                                @if($subtask->status === 'todo' || $subtask->status === 'in_progress')
                                    <form action="{{ route('leader.subtask.destroy', ['project' => $project, 'board' => $board, 'card' => $card, 'subtask' => $subtask]) }}" 
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
                @endforeach
            </div>
        @endif
    </div>
@endsection