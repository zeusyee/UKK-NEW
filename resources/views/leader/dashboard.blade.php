@extends('layouts.leader')

@section('title', 'Leader Dashboard')

@section('content')
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">
            <i class="fas fa-crown text-green-500 mr-2"></i>Leader Dashboard
        </h1>
        <p class="text-gray-600">Manage and monitor your projects</p>
    </div>

    @if($projectsWithStats->isEmpty())
        <div class="bg-white rounded-lg shadow-md p-12">
            <div class="text-center">
                <i class="fas fa-folder-open text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">No Projects Yet</h3>
                <p class="text-gray-500">You are not assigned as a leader to any projects yet.</p>
            </div>
        </div>
    @else
        <!-- Projects List -->
        <div class="space-y-6">
            @foreach($projectsWithStats as $projectData)
                @php
                    $project = $projectData['project'];
                    $stats = $projectData['stats'];
                    
                    // Determine health color
                    $healthColors = [
                        'critical' => ['bg' => 'bg-red-100', 'border' => 'border-red-300', 'text' => 'text-red-800', 'icon' => 'fa-exclamation-triangle', 'badge' => 'bg-red-500'],
                        'warning' => ['bg' => 'bg-yellow-100', 'border' => 'border-yellow-300', 'text' => 'text-yellow-800', 'icon' => 'fa-exclamation-circle', 'badge' => 'bg-yellow-500'],
                        'good' => ['bg' => 'bg-green-100', 'border' => 'border-green-300', 'text' => 'text-green-800', 'icon' => 'fa-check-circle', 'badge' => 'bg-green-500']
                    ];
                    $healthColor = $healthColors[$stats['health']];
                    
                    // Progress bar color
                    $progressColor = $stats['overall_progress'] >= 80 ? 'bg-green-500' : 
                                    ($stats['overall_progress'] >= 50 ? 'bg-blue-500' : 
                                    ($stats['overall_progress'] >= 25 ? 'bg-yellow-500' : 'bg-red-500'));
                @endphp
                
                <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-all duration-300 border-l-4 {{ $healthColor['badge'] }}">
                    <div class="flex flex-col lg:flex-row">
                        <!-- Left Side: Project Header -->
                        <div class="flex-1 p-6 border-b lg:border-b-0 lg:border-r border-gray-200">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <h3 class="text-xl font-bold text-gray-800 mb-1">{{ $project->project_name }}</h3>
                                    <p class="text-sm text-gray-600 line-clamp-2">{{ $project->description }}</p>
                                </div>
                                <div class="ml-3 flex gap-2">
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 whitespace-nowrap">
                                        <i class="fas fa-crown mr-1"></i>Leader
                                    </span>
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $healthColor['bg'] }} {{ $healthColor['text'] }} whitespace-nowrap">
                                        <i class="fas {{ $healthColor['icon'] }} mr-1"></i>
                                        {{ ucfirst($stats['health']) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Overall Progress -->
                            <div class="mt-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-semibold text-gray-700">Overall Progress</span>
                                    <span class="text-lg font-bold {{ $progressColor }} bg-clip-text text-transparent">{{ $stats['overall_progress'] }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                                    <div class="{{ $progressColor }} h-3 rounded-full transition-all duration-500 ease-out flex items-center justify-end pr-1" 
                                         style="width: {{ $stats['overall_progress'] }}%">
                                        @if($stats['overall_progress'] > 5)
                                            <div class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Deadline Info -->
                            <div class="mt-4">
                                @if($project->deadline)
                                    @php
                                        $daysUntil = (int) floor(now()->diffInDays($project->deadline, false));
                                    @endphp
                                    <div class="bg-gray-50 rounded-lg p-3 border-l-4 {{ $daysUntil < 0 ? 'border-red-500' : ($daysUntil <= 7 ? 'border-yellow-500' : 'border-green-500') }}">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-2">
                                                <i class="fas fa-calendar-alt {{ $daysUntil < 0 ? 'text-red-500' : ($daysUntil <= 7 ? 'text-yellow-500' : 'text-green-500') }}"></i>
                                                <span class="text-sm font-medium text-gray-700">Deadline:</span>
                                                <span class="text-sm text-gray-800">{{ $project->deadline->format('d M Y') }}</span>
                                            </div>
                                            @if($daysUntil < 0)
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                    <i class="fas fa-exclamation-triangle mr-1"></i>{{ abs($daysUntil) }} days overdue
                                                </span>
                                            @elseif($daysUntil == 0)
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">
                                                    <i class="fas fa-clock mr-1"></i>Due today!
                                                </span>
                                            @elseif($daysUntil <= 7)
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    <i class="fas fa-hourglass-half mr-1"></i>{{ $daysUntil }} days left
                                                </span>
                                            @else
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                    <i class="fas fa-check-circle mr-1"></i>{{ $daysUntil }} days left
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                                        <div class="flex items-center text-sm text-gray-600">
                                            <i class="fas fa-calendar-times text-gray-400 mr-2"></i>
                                            <span>No deadline set</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Right Side: Project Statistics -->
                        <div class="w-full lg:w-2/5 p-6 bg-gray-50">
                            <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                <i class="fas fa-chart-bar mr-2 text-green-500"></i>Project Statistics
                            </h4>
                            
                            <div class="grid grid-cols-2 gap-3 mb-4">
                                <!-- Cards Stats -->
                                <div class="bg-white rounded-lg p-3 border border-gray-200">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs text-gray-600 font-medium">Tasks</span>
                                        <i class="fas fa-tasks text-blue-500"></i>
                                    </div>
                                    <div class="text-2xl font-bold text-gray-800">{{ $stats['total_cards'] }}</div>
                                    <div class="mt-2 flex items-center gap-2 text-xs flex-wrap">
                                        <span class="text-green-600">
                                            <i class="fas fa-check-circle"></i> {{ $stats['completed_cards'] }}
                                        </span>
                                        <span class="text-blue-600">
                                            <i class="fas fa-spinner"></i> {{ $stats['in_progress_cards'] }}
                                        </span>
                                        <span class="text-gray-600">
                                            <i class="fas fa-circle"></i> {{ $stats['todo_cards'] }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Subtasks Stats -->
                                <div class="bg-white rounded-lg p-3 border border-gray-200">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs text-gray-600 font-medium">Subtasks</span>
                                        <i class="fas fa-list-check text-purple-500"></i>
                                    </div>
                                    <div class="text-2xl font-bold text-gray-800">{{ $stats['total_subtasks'] }}</div>
                                    <div class="mt-2 flex items-center gap-2 text-xs flex-wrap">
                                        <span class="text-green-600">
                                            <i class="fas fa-check"></i> {{ $stats['completed_subtasks'] }}
                                        </span>
                                        @if($stats['review_subtasks'] > 0)
                                            <span class="text-purple-600 animate-pulse">
                                                <i class="fas fa-eye"></i> {{ $stats['review_subtasks'] }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Team Stats -->
                                <div class="bg-white rounded-lg p-3 border border-gray-200">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs text-gray-600 font-medium">Team</span>
                                        <i class="fas fa-users text-teal-500"></i>
                                    </div>
                                    <div class="text-2xl font-bold text-gray-800">{{ $stats['total_members'] }}</div>
                                    <div class="mt-2 text-xs text-gray-600">
                                        <i class="fas fa-user-check"></i> {{ $stats['assigned_members'] }} assigned
                                    </div>
                                </div>

                                <!-- Boards Stats -->
                                <div class="bg-white rounded-lg p-3 border border-gray-200">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs text-gray-600 font-medium">Boards</span>
                                        <i class="fas fa-columns text-indigo-500"></i>
                                    </div>
                                    <div class="text-2xl font-bold text-gray-800">{{ $stats['total_boards'] }}</div>
                                    <div class="mt-2 text-xs text-gray-600">
                                        <i class="fas fa-layer-group"></i> Boards
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="w-full">
                                <a href="{{ route('leader.project.details', $project) }}" 
                                   class="flex items-center justify-center w-full bg-green-500 hover:bg-green-600 text-white px-4 py-2.5 rounded-lg transition-colors text-sm font-semibold shadow-sm">
                                    <i class="fas fa-tasks mr-2"></i>Manage Project
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Summary Stats -->
        @php
            $totalProjects = $projectsWithStats->count();
            $totalCards = $projectsWithStats->sum(fn($p) => $p['stats']['total_cards']);
            $totalMembers = $projectsWithStats->sum(fn($p) => $p['stats']['total_members']);
            $avgProgress = $projectsWithStats->avg(fn($p) => $p['stats']['overall_progress']);
        @endphp
        
        <div class="mt-8 bg-gradient-to-r from-green-500 to-blue-500 rounded-lg shadow-lg p-6 text-white">
            <h3 class="text-lg font-semibold mb-4 flex items-center">
                <i class="fas fa-chart-pie mr-2"></i>Your Leadership Summary
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white bg-opacity-20 rounded-lg p-4 backdrop-blur-sm">
                    <div class="text-3xl font-bold">{{ $totalProjects }}</div>
                    <div class="text-sm opacity-90 mt-1">Projects Leading</div>
                </div>
                <div class="bg-white bg-opacity-20 rounded-lg p-4 backdrop-blur-sm">
                    <div class="text-3xl font-bold">{{ $totalCards }}</div>
                    <div class="text-sm opacity-90 mt-1">Total Tasks</div>
                </div>
                <div class="bg-white bg-opacity-20 rounded-lg p-4 backdrop-blur-sm">
                    <div class="text-3xl font-bold">{{ $totalMembers }}</div>
                    <div class="text-sm opacity-90 mt-1">Team Members</div>
                </div>
                <div class="bg-white bg-opacity-20 rounded-lg p-4 backdrop-blur-sm">
                    <div class="text-3xl font-bold">{{ round($avgProgress) }}%</div>
                    <div class="text-sm opacity-90 mt-1">Avg Progress</div>
                </div>
            </div>
        </div>
    @endif
@endsection