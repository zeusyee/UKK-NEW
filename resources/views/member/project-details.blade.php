@extends('layouts.member')

@section('title', 'Project Details')

@section('content')
    <div class="mb-6">
        <a href="{{ route('member.dashboard') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-block">
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
                            <i class="fas fa-calendar-alt mr-2 text-blue-500"></i>
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
            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs sm:text-sm font-semibold whitespace-nowrap self-start">
                <i class="fas fa-user mr-1"></i>Member
            </span>
        </div>

        <!-- Statistics -->
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mt-4 sm:mt-6">
            <div class="bg-purple-50 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">My Cards</p>
                        <p class="text-2xl font-bold text-purple-600">{{ $projectStats['total_cards'] }}</p>
                        <p class="text-xs text-gray-500">{{ $projectStats['completed_cards'] }} completed</p>
                    </div>
                    <i class="fas fa-tasks text-3xl text-purple-300"></i>
                </div>
            </div>

            <div class="bg-green-50 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">My Subtasks</p>
                        <p class="text-2xl font-bold text-green-600">{{ $projectStats['completed_subtasks'] }}/{{ $projectStats['total_subtasks'] }}</p>
                        <p class="text-xs text-gray-500">{{ $projectStats['subtask_progress'] }}%</p>
                    </div>
                    <i class="fas fa-list-check text-3xl text-green-300"></i>
                </div>
            </div>

            <div class="bg-blue-50 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Progress</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $projectStats['card_progress'] }}%</p>
                        <p class="text-xs text-gray-500">Card completion</p>
                    </div>
                    <i class="fas fa-chart-line text-3xl text-blue-300"></i>
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
    </div>

    <!-- My Assigned Cards -->
    @php
        $defaultBoard = $project->boards->first();
    @endphp
    
    @if(!$defaultBoard || $defaultBoard->cards->isEmpty())
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <i class="fas fa-clipboard-list text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg font-semibold">No cards assigned to you yet.</p>
            <p class="text-gray-400 text-sm mt-2">Wait for your leader to assign cards to you.</p>
        </div>
    @else
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <!-- Cards Header -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-4">
                <div class="flex justify-between items-center">
                    <div class="text-white">
                        <h3 class="text-xl font-bold"><i class="fas fa-tasks mr-2"></i>My Assigned Cards</h3>
                        <p class="text-blue-100 text-sm mt-1">{{ $defaultBoard->cards->count() }} cards assigned to you</p>
                    </div>
                </div>
            </div>

            <!-- Cards Grid -->
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($defaultBoard->cards as $card)
                        <div class="border-2 border-gray-200 rounded-lg p-4 hover:shadow-lg transition-shadow cursor-pointer"
                             onclick="window.location='{{ route('member.task.show', ['project' => $project, 'board' => $defaultBoard, 'card' => $card]) }}'">
                            <!-- Card Header -->
                            <div class="flex justify-between items-start mb-3">
                                <h4 class="font-semibold text-gray-800 flex-1">{{ $card->card_title }}</h4>
                                <span class="ml-2 px-2 py-1 text-xs rounded-full {{ $card->priority === 'high' ? 'bg-red-100 text-red-800' : ($card->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                    {{ ucfirst($card->priority) }}
                                </span>
                            </div>

                            <!-- Card Description -->
                            @if($card->description)
                                <p class="text-sm text-gray-600 mb-3">{{ Str::limit($card->description, 80) }}</p>
                            @endif

                            <!-- Card Status -->
                            <div class="mb-3">
                                <span class="px-2 py-1 text-xs rounded-full {{ $card->status === 'done' ? 'bg-green-100 text-green-800' : ($card->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ ucfirst(str_replace('_', ' ', $card->status)) }}
                                </span>
                            </div>

                            <!-- Subtasks Progress -->
                            @if($card->subtasks->count() > 0)
                                <div class="mb-3">
                                    <div class="flex justify-between text-xs text-gray-600 mb-1">
                                        <span>My Subtasks</span>
                                        <span>{{ $card->subtasks->where('status', 'done')->count() }}/{{ $card->subtasks->count() }}</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-500 h-2 rounded-full" style="width: {{ $card->subtasks->count() > 0 ? round(($card->subtasks->where('status', 'done')->count() / $card->subtasks->count()) * 100) : 0 }}%"></div>
                                    </div>
                                </div>
                            @else
                                <div class="mb-3 p-2 bg-yellow-50 rounded text-xs text-yellow-700">
                                    <i class="fas fa-info-circle mr-1"></i>No subtasks yet. Click to create!
                                </div>
                            @endif

                            <!-- Card Footer -->
                            <div class="flex justify-between items-center text-xs text-gray-500 pt-3 border-t border-gray-200">
                                <div>
                                    @if($card->due_date)
                                        <i class="fas fa-calendar-alt mr-1"></i>
                                        {{ $card->due_date->format('M d') }}
                                    @else
                                        <span class="text-gray-400">No due date</span>
                                    @endif
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-arrow-right text-blue-500"></i>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
@endsection
