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
                    <!-- Kanban Board Layout -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        @php
                            $statusConfig = [
                                'todo' => [
                                    'title' => 'To Do',
                                    'icon' => 'fa-circle',
                                    'bgColor' => 'bg-gray-100',
                                    'textColor' => 'text-gray-800',
                                    'borderColor' => 'border-gray-300',
                                    'badgeColor' => 'bg-gray-500'
                                ],
                                'in_progress' => [
                                    'title' => 'In Progress',
                                    'icon' => 'fa-spinner',
                                    'bgColor' => 'bg-blue-50',
                                    'textColor' => 'text-blue-800',
                                    'borderColor' => 'border-blue-300',
                                    'badgeColor' => 'bg-blue-500'
                                ],
                                'review' => [
                                    'title' => 'Review',
                                    'icon' => 'fa-eye',
                                    'bgColor' => 'bg-purple-50',
                                    'textColor' => 'text-purple-800',
                                    'borderColor' => 'border-purple-300',
                                    'badgeColor' => 'bg-purple-500'
                                ],
                                'done' => [
                                    'title' => 'Done',
                                    'icon' => 'fa-check-circle',
                                    'bgColor' => 'bg-green-50',
                                    'textColor' => 'text-green-800',
                                    'borderColor' => 'border-green-300',
                                    'badgeColor' => 'bg-green-500'
                                ]
                            ];
                            
                            // Group cards by status
                            $cardsByStatus = $defaultBoard->cards->groupBy('status');
                        @endphp

                        @foreach($statusConfig as $status => $config)
                            <!-- Column for {{ $config['title'] }} -->
                            <div class="flex flex-col">
                                <!-- Column Header -->
                                <div class="rounded-t-xl {{ $config['bgColor'] }} {{ $config['borderColor'] }} border-2 border-b-0 p-4 mb-0">
                                    <div class="flex items-center justify-between">
                                        <h3 class="font-bold {{ $config['textColor'] }} flex items-center">
                                            <i class="fas {{ $config['icon'] }} mr-2"></i>
                                            {{ $config['title'] }}
                                        </h3>
                                        <span class="{{ $config['badgeColor'] }} text-white rounded-full w-7 h-7 flex items-center justify-center text-sm font-bold">
                                            {{ $cardsByStatus->get($status, collect())->count() }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Cards Container -->
                                <div class="flex-1 rounded-b-xl {{ $config['borderColor'] }} border-2 border-t-0 p-4 bg-white min-h-[200px] space-y-3 overflow-y-auto max-h-[600px]">
                                    @forelse($cardsByStatus->get($status, collect()) as $card)
                                        @php
                                            $subtasksCount = $card->getSubtasksCountByStatus();
                                            $progressPercentage = $card->getProgressPercentage();
                                            
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
                                        
                                        <!-- Card -->
                                        <div class="bg-white border-2 {{ $config['borderColor'] }} rounded-xl p-4 hover:shadow-xl transition-all duration-300 cursor-pointer transform hover:-translate-y-1"
                                             onclick="window.location='{{ route('leader.card.show', ['project' => $project, 'board' => $defaultBoard, 'card' => $card]) }}'">
                                            <!-- Card Header -->
                                            <div class="mb-3">
                                                <div class="flex justify-between items-start mb-2">
                                                    <h4 class="font-bold text-gray-800 text-sm flex-1 line-clamp-2">{{ $card->card_title }}</h4>
                                                </div>
                                                <span class="inline-block px-2 py-1 text-xs rounded-full font-semibold {{ $card->priority === 'high' ? 'bg-red-100 text-red-800' : ($card->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                                                    <i class="fas fa-flag mr-1"></i>{{ ucfirst($card->priority) }}
                                                </span>
                                            </div>

                                            <!-- Card Description -->
                                            @if($card->description)
                                                <p class="text-xs text-gray-600 mb-3 line-clamp-2">{{ Str::limit($card->description, 60) }}</p>
                                            @endif

                                            <!-- Due Date Badge -->
                                            @if($dueStatus)
                                                <div class="mb-3">
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-lg {{ $dueBadgeClass }}">
                                                        <i class="fas fa-exclamation-triangle mr-1"></i>{{ $dueStatus }}
                                                    </span>
                                                </div>
                                            @endif
                                            
                                            <!-- Subtasks Progress -->
                                            @if($card->subtasks->count() > 0)
                                                <div class="mb-3 bg-gray-50 rounded-lg p-2.5">
                                                    <div class="flex justify-between items-center mb-2">
                                                        <span class="text-xs font-semibold text-gray-600">
                                                            <i class="fas fa-tasks mr-1"></i>Subtasks
                                                        </span>
                                                        <span class="text-xs font-bold {{ $progressPercentage == 100 ? 'text-green-600' : 'text-blue-600' }}">
                                                            {{ $subtasksCount['done'] }}/{{ $card->subtasks->count() }}
                                                        </span>
                                                    </div>
                                                    <div class="w-full bg-gray-200 rounded-full h-1.5 mb-2">
                                                        <div class="h-1.5 rounded-full {{ $progressPercentage == 100 ? 'bg-green-500' : ($progressPercentage >= 50 ? 'bg-blue-500' : 'bg-yellow-500') }}" 
                                                             style="width: {{ $progressPercentage }}%"></div>
                                                    </div>
                                                    <div class="grid grid-cols-4 gap-1 text-center">
                                                        <div>
                                                            <div class="text-xs font-bold text-gray-600">{{ $subtasksCount['todo'] }}</div>
                                                            <div class="text-xs text-gray-400">To Do</div>
                                                        </div>
                                                        <div>
                                                            <div class="text-xs font-bold text-blue-600">{{ $subtasksCount['in_progress'] }}</div>
                                                            <div class="text-xs text-gray-400">Progress</div>
                                                        </div>
                                                        <div>
                                                            <div class="text-xs font-bold text-purple-600">{{ $subtasksCount['review'] }}</div>
                                                            <div class="text-xs text-gray-400">Review</div>
                                                        </div>
                                                        <div>
                                                            <div class="text-xs font-bold text-green-600">{{ $subtasksCount['done'] }}</div>
                                                            <div class="text-xs text-gray-400">Done</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="mb-3 bg-gray-50 rounded-lg p-2 text-center">
                                                    <span class="text-xs text-gray-400">
                                                        <i class="fas fa-info-circle mr-1"></i>No subtasks
                                                    </span>
                                                </div>
                                            @endif

                                            <!-- Card Footer -->
                                            <div class="flex justify-between items-center text-xs text-gray-500 pt-3 border-t border-gray-100">
                                                <div>
                                                    @if($card->due_date)
                                                        <i class="fas fa-calendar-alt mr-1"></i>
                                                        {{ $card->due_date->format('M d') }}
                                                    @else
                                                        <span class="text-gray-400">No deadline</span>
                                                    @endif
                                                </div>
                                                <div>
                                                    @if($card->assignedUser)
                                                        <div class="flex items-center">
                                                            <div class="h-6 w-6 rounded-full {{ $config['badgeColor'] }} border-2 border-white flex items-center justify-center text-white text-xs font-bold shadow-sm"
                                                                 title="{{ $card->assignedUser->full_name }}">
                                                                {{ strtoupper(substr($card->assignedUser->full_name, 0, 1)) }}
                                                            </div>
                                                        </div>
                                                    @else
                                                        <span class="text-gray-400">
                                                            <i class="fas fa-user-slash"></i>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="flex flex-col items-center justify-center py-8 text-gray-400">
                                            <i class="fas fa-inbox text-3xl mb-2 opacity-50"></i>
                                            <p class="text-xs">No cards</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endif
@endsection