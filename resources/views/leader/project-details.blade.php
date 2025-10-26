@extends('layouts.leader')

@section('title', 'Project Details')

@section('content')
    <div class="mb-6">
        <a href="{{ route('leader.dashboard') }}" class="text-green-600 hover:text-green-800 mb-4 inline-block">
            <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
        </a>
    </div>

    <!-- Project Header -->
    <div class="bg-white rounded-lg shadow-md p-4 sm:p-6 mb-4 sm:mb-6">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3 mb-4">
            <div class="flex-1">
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-2">{{ $project->project_name }}</h2>
                <p class="text-gray-600 text-sm sm:text-base">{{ $project->description }}</p>
                
                <!-- Deadline Info -->
                @if($project->deadline)
                    @php
                        $daysUntil = (int) floor(now()->diffInDays($project->deadline, false));
                    @endphp
                    <div class="mt-3 sm:mt-4">
                        <div class="flex items-center text-xs sm:text-sm text-gray-600 mb-2">
                            <i class="fas fa-calendar-alt mr-2 text-green-500"></i>
                            <span class="font-medium">Deadline: {{ $project->deadline->format('F j, Y') }}</span>
                        </div>
                        <div class="mt-2">
                            @if($daysUntil < 0)
                                <div class="inline-flex items-center px-3 py-2 text-xs sm:text-sm font-semibold rounded-lg bg-red-100 text-red-800">
                                    <i class="fas fa-exclamation-circle mr-2"></i>
                                    <span class="text-xl sm:text-2xl font-bold mr-2">{{ abs($daysUntil) }}</span>
                                    <span>hari terlambat</span>
                                </div>
                            @elseif($daysUntil == 0)
                                <div class="inline-flex items-center px-3 py-2 text-xs sm:text-sm font-semibold rounded-lg bg-orange-100 text-orange-800">
                                    <i class="fas fa-clock mr-2"></i>
                                    <span class="font-bold">Deadline hari ini!</span>
                                </div>
                            @elseif($daysUntil <= 7)
                                <div class="inline-flex items-center px-3 py-2 text-xs sm:text-sm font-semibold rounded-lg bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-hourglass-half mr-2"></i>
                                    <span class="text-xl sm:text-2xl font-bold mr-2">{{ $daysUntil }}</span>
                                    <span>hari tersisa</span>
                                </div>
                            @else
                                <div class="inline-flex items-center px-3 py-2 text-xs sm:text-sm font-semibold rounded-lg bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    <span class="text-xl sm:text-2xl font-bold mr-2">{{ $daysUntil }}</span>
                                    <span>hari tersisa</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs sm:text-sm font-semibold whitespace-nowrap self-start">
                <i class="fas fa-crown mr-1"></i>{{ ucfirst($member->role) }}
            </span>
        </div>

        <!-- Statistics -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 mt-4 sm:mt-6">
            <div class="bg-purple-50 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Total Cards</p>
                        <p class="text-2xl font-bold text-purple-600">{{ $projectStats['total_cards'] }}</p>
                        <p class="text-xs text-gray-500">{{ $projectStats['completed_cards'] }} completed</p>
                    </div>
                    <i class="fas fa-tasks text-3xl text-purple-300"></i>
                </div>
            </div>

            <div class="bg-green-50 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Subtasks Progress</p>
                        <p class="text-2xl font-bold text-green-600">{{ $projectStats['completed_subtasks'] }}/{{ $projectStats['total_subtasks'] }}</p>
                        <p class="text-xs text-gray-500">{{ $projectStats['subtask_progress'] }}%</p>
                    </div>
                    <i class="fas fa-list-check text-3xl text-green-300"></i>
                </div>
            </div>

            <div class="bg-orange-50 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Team Members</p>
                        <p class="text-2xl font-bold text-orange-600">{{ $projectStats['active_members'] }}</p>
                    </div>
                    <i class="fas fa-users text-3xl text-orange-300"></i>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-6">
            @php
                $defaultBoard = $project->boards->first();
            @endphp
            @if($defaultBoard)
                <a href="{{ route('leader.card.create', ['project' => $project, 'board' => $defaultBoard]) }}" 
                   class="inline-flex items-center bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-6 rounded-lg shadow-lg transition-colors">
                    <i class="fas fa-plus mr-2"></i>Create New Card
                </a>
            @endif
        </div>
    </div>

    <!-- Cards Section -->
    @php
        $defaultBoard = $project->boards->first();
    @endphp
    
    @if(!$defaultBoard || $defaultBoard->cards->isEmpty())
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <i class="fas fa-clipboard-list text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg font-semibold">No cards created yet.</p>
            <p class="text-gray-400 text-sm mt-2">Create your first card to start organizing tasks.</p>
            @if($defaultBoard)
                <a href="{{ route('leader.card.create', ['project' => $project, 'board' => $defaultBoard]) }}" 
                   class="inline-flex items-center bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg mt-4">
                    <i class="fas fa-plus mr-2"></i>Create First Card
                </a>
            @endif
        </div>
    @else
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <!-- Cards Header -->
            <div class="bg-gradient-to-r from-green-500 to-green-600 p-4">
                <div class="flex justify-between items-center">
                    <div class="text-white">
                        <h3 class="text-xl font-bold"><i class="fas fa-tasks mr-2"></i>Project Cards</h3>
                        <p class="text-green-100 text-sm mt-1">{{ $defaultBoard->cards->count() }} cards in this project</p>
                    </div>
                </div>
            </div>

            <!-- Cards Grid -->
            <div class="p-6">
                @if($defaultBoard->cards->isEmpty())
                    <p class="text-gray-400 text-center py-8">No cards in this project yet.</p>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($defaultBoard->cards as $card)
                            @php
                                $subtasksCount = $card->getSubtasksCountByStatus();
                                $progressPercentage = $card->getProgressPercentage();
                                
                                // Determine card border color based on status
                                $borderColorClass = 'border-gray-200';
                                if ($card->status === 'done') {
                                    $borderColorClass = 'border-green-300';
                                } elseif ($card->status === 'in_progress') {
                                    $borderColorClass = 'border-blue-300';
                                }
                                
                                // Check due date status
                                $dueStatus = '';
                                $dueBadgeClass = '';
                                if ($card->due_date) {
                                    $daysUntil = now()->diffInDays($card->due_date, false);
                                    if ($daysUntil < 0) {
                                        $dueStatus = 'Overdue';
                                        $dueBadgeClass = 'bg-red-100 text-red-800';
                                    } elseif ($daysUntil <= 3) {
                                        $dueStatus = 'Due Soon';
                                        $dueBadgeClass = 'bg-orange-100 text-orange-800';
                                    }
                                }
                            @endphp
                            <div class="border-2 {{ $borderColorClass }} rounded-lg p-4 hover:shadow-lg transition-shadow cursor-pointer bg-white"
                                 onclick="window.location='{{ route('leader.card.show', ['project' => $project, 'board' => $defaultBoard, 'card' => $card]) }}'">
                                <!-- Card Header -->
                                <div class="flex justify-between items-start mb-3">
                                    <h4 class="font-semibold text-gray-800 flex-1">{{ $card->card_title }}</h4>
                                    <span class="ml-2 px-2 py-1 text-xs rounded-full {{ $card->priority === 'high' ? 'bg-red-100 text-red-800' : ($card->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                        <i class="fas fa-flag mr-1"></i>{{ ucfirst($card->priority) }}
                                    </span>
                                </div>

                                <!-- Card Description -->
                                @if($card->description)
                                    <p class="text-sm text-gray-600 mb-3">{{ Str::limit($card->description, 80) }}</p>
                                @endif

                                <!-- Card Status Badge -->
                                <div class="mb-3 flex items-center gap-2 flex-wrap">
                                    <span class="px-2.5 py-1.5 text-xs font-semibold rounded-lg {{ $card->status === 'done' ? 'bg-green-100 text-green-800' : ($card->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                        @if($card->status === 'done')
                                            <i class="fas fa-check-circle mr-1"></i>Done
                                        @elseif($card->status === 'in_progress')
                                            <i class="fas fa-spinner mr-1"></i>In Progress
                                        @else
                                            <i class="fas fa-circle mr-1"></i>To Do
                                        @endif
                                    </span>
                                    
                                    @if($dueStatus)
                                        <span class="px-2 py-1 text-xs font-semibold rounded-lg {{ $dueBadgeClass }}">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>{{ $dueStatus }}
                                        </span>
                                    @endif
                                </div>
                                
                                <!-- Subtasks Status Breakdown -->
                                @if($card->subtasks->count() > 0)
                                    <div class="mb-3 bg-gray-50 rounded-lg p-3">
                                        <div class="flex justify-between items-center mb-2">
                                            <span class="text-xs font-semibold text-gray-600">Subtasks Status</span>
                                            <span class="text-xs font-bold {{ $progressPercentage == 100 ? 'text-green-600' : 'text-blue-600' }}">{{ $progressPercentage }}%</span>
                                        </div>
                                        <div class="grid grid-cols-4 gap-1 mb-2">
                                            <div class="text-center">
                                                <div class="text-xs font-bold text-gray-700">{{ $subtasksCount['todo'] }}</div>
                                                <div class="text-xs text-gray-500">Todo</div>
                                            </div>
                                            <div class="text-center">
                                                <div class="text-xs font-bold text-blue-700">{{ $subtasksCount['in_progress'] }}</div>
                                                <div class="text-xs text-gray-500">Progress</div>
                                            </div>
                                            <div class="text-center">
                                                <div class="text-xs font-bold text-purple-700">{{ $subtasksCount['review'] }}</div>
                                                <div class="text-xs text-gray-500">Review</div>
                                            </div>
                                            <div class="text-center">
                                                <div class="text-xs font-bold text-green-700">{{ $subtasksCount['done'] }}</div>
                                                <div class="text-xs text-gray-500">Done</div>
                                            </div>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="h-2 rounded-full {{ $progressPercentage == 100 ? 'bg-green-500' : ($progressPercentage >= 50 ? 'bg-blue-500' : 'bg-yellow-500') }}" 
                                                 style="width: {{ $progressPercentage }}%"></div>
                                        </div>
                                    </div>
                                @else
                                    <div class="mb-3 bg-gray-50 rounded-lg p-2 text-center">
                                        <span class="text-xs text-gray-500"><i class="fas fa-info-circle mr-1"></i>No subtasks yet</span>
                                    </div>
                                @endif

                                <!-- Card Footer -->
                                <div class="flex justify-between items-center text-xs text-gray-500 pt-3 border-t border-gray-200">
                                    <div>
                                        @if($card->due_date)
                                            <i class="fas fa-calendar-alt mr-1"></i>
                                            {{ $card->due_date->format('M d') }}
                                        @endif
                                    </div>
                                    <div class="flex items-center">
                                        @if($card->assignedUser)
                                            <div class="flex items-center space-x-1">
                                                <div class="h-6 w-6 rounded-full bg-blue-500 border-2 border-white flex items-center justify-center text-white text-xs font-semibold"
                                                     title="{{ $card->assignedUser->full_name }}">
                                                    {{ strtoupper(substr($card->assignedUser->full_name, 0, 1)) }}
                                                </div>
                                                <span class="text-xs text-gray-600 ml-1">{{ Str::limit($card->assignedUser->full_name, 15) }}</span>
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400"><i class="fas fa-user-slash mr-1"></i>Not assigned</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endif
@endsection