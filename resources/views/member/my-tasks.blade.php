@extends('layouts.member')

@section('title', 'My Tasks')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">My Assigned Cards</h2>
        <p class="text-gray-600 mt-2">Manage and track your assigned cards</p>
    </div>

    <!-- Task Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Cards</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $myCards->count() }}</p>
                </div>
                <i class="fas fa-tasks text-3xl text-gray-300"></i>
            </div>
        </div>

        <div class="bg-yellow-50 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">To Do</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $myCards->where('status', 'todo')->count() }}</p>
                </div>
                <i class="fas fa-circle text-3xl text-yellow-300"></i>
            </div>
        </div>

        <div class="bg-blue-50 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">In Progress</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $myCards->where('status', 'in_progress')->count() }}</p>
                </div>
                <i class="fas fa-spinner text-3xl text-blue-300"></i>
            </div>
        </div>

        <div class="bg-green-50 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Completed</p>
                    <p class="text-2xl font-bold text-green-600">{{ $myCards->where('status', 'done')->count() }}</p>
                </div>
                <i class="fas fa-check-circle text-3xl text-green-300"></i>
            </div>
        </div>
    </div>

    <!-- Cards List -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">All My Cards</h3>
        </div>
        
        @if($myCards->isEmpty())
            <div class="p-12 text-center">
                <i class="fas fa-clipboard-list text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg">No cards assigned to you yet.</p>
                <p class="text-gray-400 text-sm mt-2">Your leader will assign cards to you soon.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Card Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project / Board</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Active Timer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($myCards as $card)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $card->card_title }}</div>
                                    @if($card->description)
                                        <div class="text-xs text-gray-500">{{ Str::limit($card->description, 50) }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $card->board->project->project_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $card->board->board_name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                        {{ $card->status === 'done' ? 'bg-green-100 text-green-800' : 
                                           ($card->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                        {{ ucfirst(str_replace('_', ' ', $card->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $progressPercentage = $card->getProgressPercentage();
                                        $subtasksCount = $card->getSubtasksCountByStatus();
                                    @endphp
                                    <div class="min-w-[120px]">
                                        <div class="flex justify-between items-center mb-1">
                                            <span class="text-xs text-gray-600">{{ $subtasksCount['done'] }}/{{ $subtasksCount['total'] }}</span>
                                            <span class="text-xs font-bold text-blue-600">{{ $progressPercentage }}%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="h-2 rounded-full transition-all duration-300 {{ $progressPercentage == 100 ? 'bg-green-500' : 'bg-blue-500' }}" 
                                                 style="width: {{ $progressPercentage }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @php
                                        $activeSubtask = $card->subtasks->where('status', 'in_progress')->first();
                                    @endphp
                                    @if($activeSubtask && $activeSubtask->started_at)
                                        <div class="px-2 py-1 rounded {{ $activeSubtask->paused_at ? 'bg-gray-100' : 'bg-blue-100' }}">
                                            <div class="text-xs {{ $activeSubtask->paused_at ? 'text-gray-600' : 'text-blue-600' }} font-semibold">
                                                {{ $activeSubtask->paused_at ? '⏸️' : '⏱️' }}
                                            </div>
                                            <div class="text-sm font-bold {{ $activeSubtask->paused_at ? 'text-gray-600' : 'text-blue-600' }}" id="timer-card-{{ $card->card_id }}">
                                                @if($activeSubtask->estimated_hours)
                                                    {{ gmdate('H:i:s', $activeSubtask->estimated_hours * 3600) }}
                                                @else
                                                    --:--:--
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $card->priority === 'high' ? 'bg-red-100 text-red-800' : ($card->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                        {{ ucfirst($card->priority) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('member.task.show', ['project' => $card->board->project->project_id, 'board' => $card->board->board_id, 'card' => $card->card_id]) }}" 
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

        <script>
            // Countdown timer functionality for active subtasks
            function updateCardTimer(cardId, subtaskId, startedAt, estimatedHours = null, totalPausedSeconds = 0, isPaused = false) {
                const timerElement = document.getElementById('timer-card-' + cardId);
                
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
                            // Time's up!
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

            // Initialize timers for all cards with active subtasks
            document.addEventListener('DOMContentLoaded', function() {
                @foreach($myCards as $card)
                    @php
                        $activeSubtask = $card->subtasks->where('status', 'in_progress')->first();
                    @endphp
                    @if($activeSubtask && $activeSubtask->started_at)
                        updateCardTimer(
                            {{ $card->card_id }}, 
                            {{ $activeSubtask->subtask_id }}, 
                            '{{ $activeSubtask->started_at->toIso8601String() }}',
                            {{ $activeSubtask->estimated_hours ?? 'null' }},
                            {{ $activeSubtask->total_paused_seconds }},
                            {{ $activeSubtask->paused_at ? 'true' : 'false' }}
                        );
                    @endif
                @endforeach
            });
        </script>
    @endsection
