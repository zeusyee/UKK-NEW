@extends('layouts.member')

@section('title', 'Member Dashboard')

@section('content')
    <div class="bg-white rounded-lg shadow-md p-4 sm:p-6">
        <h2 class="text-xl sm:text-2xl font-semibold mb-4 sm:mb-6">My Projects</h2>

        @if($assignedProjects->isEmpty())
            <div class="text-center py-8 sm:py-12 bg-gray-50 rounded-lg">
                <i class="fas fa-folder-open text-4xl sm:text-6xl text-gray-300 mb-3 sm:mb-4"></i>
                <p class="text-gray-500 text-base sm:text-lg font-medium px-4">You are not a member of any projects yet.</p>
                <p class="text-gray-400 text-xs sm:text-sm mt-2 px-4">Wait for your admin or leader to add you to a project.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                @foreach($assignedProjects as $project)
                    <div class="bg-white border-2 border-blue-200 rounded-lg shadow-sm hover:shadow-lg transition-all duration-300">
                        <div class="p-4 sm:p-5">
                            <h3 class="text-lg sm:text-xl font-semibold mb-2 line-clamp-2">{{ $project->project_name }}</h3>
                            <p class="text-gray-600 text-sm mb-3 sm:mb-4 line-clamp-2">{{ Str::limit($project->description, 80) }}</p>
                            
                            <div class="space-y-2 mb-4">
                                <!-- Progress Bar -->
                                @php
                                    $projectCards = $project->boards->flatMap->cards->where('assigned_user_id', Auth::id());
                                    $totalSubtasks = $projectCards->sum(fn($card) => $card->subtasks->count());
                                    $completedSubtasks = $projectCards->sum(fn($card) => $card->subtasks->where('status', 'done')->count());
                                    $progressPercentage = $totalSubtasks > 0 ? round(($completedSubtasks / $totalSubtasks) * 100, 1) : 0;
                                @endphp
                                <div class="mb-3">
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="text-xs font-medium text-gray-700">
                                            <i class="fas fa-tasks mr-1 text-blue-500"></i>Task Progress
                                        </span>
                                        <span class="text-xs font-bold text-blue-600">{{ $progressPercentage }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-2.5 rounded-full transition-all duration-500" 
                                             style="width: {{ $progressPercentage }}%"></div>
                                    </div>
                                    <div class="flex justify-between mt-1">
                                        <span class="text-xs text-gray-500">{{ $completedSubtasks }}/{{ $totalSubtasks }} subtasks</span>
                                    </div>
                                </div>

                                <!-- Deadline -->
                                <div class="flex flex-col sm:flex-row sm:items-center gap-1">
                                    <span class="text-xs sm:text-sm text-gray-600 font-medium">
                                        <i class="fas fa-calendar-alt mr-1 text-blue-500"></i>Deadline:
                                    </span>
                                    <span class="text-xs sm:text-sm text-gray-700">
                                        {{ $project->deadline ? $project->deadline->format('M d, Y') : 'No deadline set' }}
                                    </span>
                                </div>
                                
                                @if($project->deadline)
                                    @php
                                        $daysUntil = (int) floor(now()->diffInDays($project->deadline, false));
                                    @endphp
                                    <div class="flex items-start">
                                        @if($daysUntil < 0)
                                            <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                <i class="fas fa-exclamation-circle mr-1"></i>{{ abs($daysUntil) }} hari terlambat
                                            </span>
                                        @elseif($daysUntil == 0)
                                            <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">
                                                <i class="fas fa-clock mr-1"></i>Deadline hari ini!
                                            </span>
                                        @elseif($daysUntil <= 7)
                                            <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-hourglass-half mr-1"></i>{{ $daysUntil }} hari tersisa
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i>{{ $daysUntil }} hari tersisa
                                            </span>
                                        @endif
                                    </div>
                                @endif
                                
                                <!-- Creator -->
                                <div class="flex items-center text-xs sm:text-sm text-gray-600">
                                    <i class="fas fa-user mr-1 text-blue-500"></i>
                                    <span class="truncate">{{ optional($project->creator)->full_name }}</span>
                                </div>
                            </div>

                            <div class="mt-3 sm:mt-4 pt-3 sm:pt-4 border-t border-gray-200">
                                <a href="{{ route('member.project.details', $project) }}" 
                                   class="block w-full text-center bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors text-sm font-medium">
                                    <i class="fas fa-eye mr-1"></i>View Details
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection