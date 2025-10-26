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
                <div class="flex items-center space-x-3 mb-3 flex-wrap gap-2">
                    <h2 class="text-2xl font-bold text-gray-800">{{ $card->card_title }}</h2>
                    <span class="px-3 py-1.5 text-sm font-semibold rounded-lg {{ $card->priority === 'high' ? 'bg-red-100 text-red-800' : ($card->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                        <i class="fas fa-flag mr-1"></i>{{ ucfirst($card->priority) }} Priority
                    </span>
                    <span class="px-3 py-1.5 text-sm font-semibold rounded-lg {{ $card->status === 'done' ? 'bg-green-100 text-green-800' : ($card->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                        @if($card->status === 'done')
                            <i class="fas fa-check-circle mr-1"></i>Done
                        @elseif($card->status === 'in_progress')
                            <i class="fas fa-spinner mr-1"></i>In Progress
                        @else
                            <i class="fas fa-circle mr-1"></i>To Do
                        @endif
                    </span>
                    
                    @php
                        // Check due date status
                        $dueStatus = '';
                        $dueBadgeClass = '';
                        if ($card->due_date) {
                            $daysUntil = now()->diffInDays($card->due_date, false);
                            if ($daysUntil < 0) {
                                $dueStatus = abs($daysUntil) . ' days Overdue';
                                $dueBadgeClass = 'bg-red-100 text-red-800';
                            } elseif ($daysUntil <= 3) {
                                $dueStatus = 'Due in ' . $daysUntil . ' days';
                                $dueBadgeClass = 'bg-orange-100 text-orange-800';
                            } elseif ($daysUntil <= 7) {
                                $dueStatus = 'Due in ' . $daysUntil . ' days';
                                $dueBadgeClass = 'bg-yellow-100 text-yellow-800';
                            }
                        }
                    @endphp
                    
                    @if($dueStatus)
                        <span class="px-3 py-1.5 text-sm font-semibold rounded-lg {{ $dueBadgeClass }}">
                            <i class="fas fa-clock mr-1"></i>{{ $dueStatus }}
                        </span>
                    @endif
                </div>
                <p class="text-gray-600 mb-2"><i class="fas fa-columns mr-2"></i>Board: <span class="font-semibold">{{ $board->board_name }}</span></p>
                
                <!-- Quick Stats -->
                @php
                    $subtasksCount = $card->getSubtasksCountByStatus();
                    $progressPercentage = $card->getProgressPercentage();
                @endphp
                @if($card->subtasks->count() > 0)
                    <div class="flex items-center gap-4 mt-3">
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-600">Progress:</span>
                            <span class="text-sm font-bold {{ $progressPercentage == 100 ? 'text-green-600' : 'text-blue-600' }}">{{ $progressPercentage }}%</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-600">Subtasks:</span>
                            <span class="text-sm font-semibold text-gray-800">{{ $subtasksCount['done'] }}/{{ $subtasksCount['total'] }} completed</span>
                        </div>
                        @if($subtasksCount['review'] > 0)
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800 animate-pulse">
                                    <i class="fas fa-eye mr-1"></i>{{ $subtasksCount['review'] }} need review
                                </span>
                            </div>
                        @endif
                    </div>
                @endif
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

        <!-- Assigned Members (via Subtasks) -->
        <div class="mt-6">
            <h3 class="font-semibold text-gray-700 mb-3">Members Working on This Card</h3>
            @php
                $assignedUsers = $card->subtasks->pluck('assignedUser')->filter()->unique('user_id');
            @endphp
            @if($assignedUsers->count() > 0)
                @foreach($assignedUsers as $user)
                    <div class="flex items-center bg-blue-50 px-4 py-3 rounded-lg mb-2">
                        <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold mr-3">
                            {{ strtoupper(substr($user->full_name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $user->full_name }}</p>
                            <p class="text-xs text-gray-600">{{ $card->subtasks->where('assigned_user_id', $user->user_id)->count() }} subtask(s) assigned</p>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center py-6 bg-gray-50 rounded-lg">
                    <i class="fas fa-user-slash text-3xl text-gray-300 mb-2"></i>
                    <p class="text-gray-500">No member assigned to this card yet.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Subtasks Section -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="mb-4">
            <div class="flex justify-between items-center mb-2">
                <h3 class="text-xl font-bold text-gray-800">
                    Subtasks 
                    <span class="text-sm font-normal text-gray-500">
                        ({{ $card->subtasks->where('status', 'done')->count() }}/{{ $card->subtasks->count() }} completed)
                    </span>
                </h3>
                @if($card->assignedUser)
                    <div class="text-sm text-gray-600">
                        <i class="fas fa-info-circle mr-1"></i>{{ $card->assignedUser->full_name }} can create subtasks
                    </div>
                @endif
            </div>
            
            <!-- Progress Bar -->
            @if($card->subtasks->count() > 0)
                @php
                    $progressPercentage = $card->getProgressPercentage();
                    $subtasksCount = $card->getSubtasksCountByStatus();
                @endphp
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-semibold text-gray-700">Overall Progress</span>
                        <span class="text-2xl font-bold {{ $progressPercentage == 100 ? 'text-green-600' : 'text-blue-600' }}">{{ $progressPercentage }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-4 mb-3">
                        <div class="h-4 rounded-full {{ $progressPercentage == 100 ? 'bg-green-500' : ($progressPercentage >= 50 ? 'bg-blue-500' : 'bg-yellow-500') }}" 
                             style="width: {{ $progressPercentage }}%"></div>
                    </div>
                    <div class="grid grid-cols-4 gap-2 text-xs text-center">
                        <div class="bg-white rounded p-2">
                            <div class="text-gray-500">Todo</div>
                            <div class="font-bold text-gray-700">{{ $subtasksCount['todo'] }}</div>
                        </div>
                        <div class="bg-white rounded p-2">
                            <div class="text-blue-500">In Progress</div>
                            <div class="font-bold text-blue-700">{{ $subtasksCount['in_progress'] }}</div>
                        </div>
                        <div class="bg-white rounded p-2">
                            <div class="text-purple-500">Review</div>
                            <div class="font-bold text-purple-700">{{ $subtasksCount['review'] }}</div>
                        </div>
                        <div class="bg-white rounded p-2">
                            <div class="text-green-500">Done</div>
                            <div class="font-bold text-green-700">{{ $subtasksCount['done'] }}</div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Subtasks List -->
        @if($card->subtasks->isEmpty())
            <div class="text-center py-12">
                <i class="fas fa-list-check text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg font-medium">No subtasks yet.</p>
                @if($card->assignedUser)
                    <p class="text-gray-400 text-sm mt-2">{{ $card->assignedUser->full_name }} can create subtasks from their task detail page.</p>
                @else
                    <p class="text-gray-400 text-sm mt-2">Assign a member to this card first, then they can create subtasks.</p>
                @endif
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
                                    <span><i class="fas fa-user mr-1"></i>Created by: {{ $subtask->creator->full_name }}</span>
                                    @if($subtask->estimated_hours)
                                        <span><i class="fas fa-clock mr-1"></i>Est: {{ $subtask->estimated_hours }}h</span>
                                    @endif
                                    @if($subtask->actual_hours)
                                        <span><i class="fas fa-check-circle mr-1"></i>Actual: {{ $subtask->actual_hours }}h</span>
                                    @endif
                                </div>

                                <!-- Timer Display for In-Progress Subtasks -->
                                @if($subtask->status === 'in_progress' && $subtask->started_at)
                                    <div class="mt-2 px-3 py-2 rounded-lg border-2 inline-block {{ $subtask->paused_at ? 'bg-gray-100 border-gray-400' : 'bg-blue-100 border-blue-500' }}">
                                        <div class="text-xs font-semibold {{ $subtask->paused_at ? 'text-gray-600' : 'text-blue-700' }}">
                                            {{ $subtask->paused_at ? '⏸️ PAUSED' : '⏱️ TIME REMAINING' }}
                                        </div>
                                        <div class="text-lg font-bold {{ $subtask->paused_at ? 'text-gray-600' : 'text-blue-600' }}" id="timer-{{ $subtask->subtask_id }}">
                                            @if($subtask->estimated_hours)
                                                {{ gmdate('H:i:s', $subtask->estimated_hours * 3600) }}
                                            @else
                                                --:--:--
                                            @endif
                                        </div>
                                        @if($subtask->estimated_hours)
                                            <div class="text-xs {{ $subtask->paused_at ? 'text-gray-500' : 'text-blue-600' }}">
                                                Estimated: {{ $subtask->estimated_hours }}h
                                            </div>
                                        @else
                                            <div class="text-xs text-red-500">No estimate set!</div>
                                        @endif
                                    </div>
                                @endif

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

    <script>
        // Countdown timer functionality for in-progress subtasks (Leader View)
        function updateTimer(subtaskId, startedAt, estimatedHours = null, totalPausedSeconds = 0, isPaused = false) {
            const timerElement = document.getElementById('timer-' + subtaskId);
            
            if (!timerElement) return;
            if (isPaused) return; // Don't update if paused

            function updateTime() {
                const startTime = new Date(startedAt);
                const now = new Date();
                const elapsedSeconds = Math.floor((now - startTime) / 1000) - totalPausedSeconds;

                if (!estimatedHours) {
                    // Count up if no estimate
                    const hours = Math.floor(elapsedSeconds / 3600);
                    const minutes = Math.floor((elapsedSeconds % 3600) / 60);
                    const seconds = elapsedSeconds % 60;

                    const timeString = 
                        String(hours).padStart(2, '0') + ':' + 
                        String(minutes).padStart(2, '0') + ':' + 
                        String(seconds).padStart(2, '0');

                    timerElement.textContent = timeString;
                } else {
                    // Countdown from estimate
                    const totalEstimatedSeconds = estimatedHours * 3600;
                    const remainingSeconds = totalEstimatedSeconds - elapsedSeconds;

                    if (remainingSeconds <= 0) {
                        // Time's up! Show overtime in red
                        const overtimeSeconds = Math.abs(remainingSeconds);
                        const hours = Math.floor(overtimeSeconds / 3600);
                        const minutes = Math.floor((overtimeSeconds % 3600) / 60);
                        const seconds = overtimeSeconds % 60;

                        const timeString = 
                            '-' + String(hours).padStart(2, '0') + ':' + 
                            String(minutes).padStart(2, '0') + ':' + 
                            String(seconds).padStart(2, '0');

                        timerElement.textContent = timeString;
                        timerElement.classList.add('text-red-600');
                        timerElement.classList.remove('text-blue-600');
                    } else {
                        // Still have time
                        const hours = Math.floor(remainingSeconds / 3600);
                        const minutes = Math.floor((remainingSeconds % 3600) / 60);
                        const seconds = remainingSeconds % 60;

                        const timeString = 
                            String(hours).padStart(2, '0') + ':' + 
                            String(minutes).padStart(2, '0') + ':' + 
                            String(seconds).padStart(2, '0');

                        timerElement.textContent = timeString;
                    }
                }
            }

            updateTime(); // Initial update
            setInterval(updateTime, 1000); // Update every second
        }

        // Initialize timers for all in-progress subtasks
        document.addEventListener('DOMContentLoaded', function() {
            @foreach($card->subtasks as $subtask)
                @if($subtask->status === 'in_progress' && $subtask->started_at)
                    updateTimer(
                        {{ $subtask->subtask_id }}, 
                        '{{ $subtask->started_at->toIso8601String() }}',
                        {{ $subtask->estimated_hours ?? 'null' }},
                        {{ $subtask->total_paused_seconds }},
                        {{ $subtask->paused_at ? 'true' : 'false' }}
                    );
                @endif
            @endforeach
        });
    </script>
@endsection