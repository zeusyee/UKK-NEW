@extends('layouts.member')

@section('title', 'Member Dashboard')

@section('content')
    <div class="bg-white rounded-lg shadow-md p-4 sm:p-6">
        <h2 class="text-xl sm:text-2xl font-semibold mb-4 sm:mb-6">My Projects</h2>

        @if($activeProjects->isEmpty())
            <div class="text-center py-8 sm:py-12 bg-gray-50 rounded-lg">
                <i class="fas fa-folder-open text-4xl sm:text-6xl text-gray-300 mb-3 sm:mb-4"></i>
                <p class="text-gray-500 text-base sm:text-lg font-medium px-4">You are not a member of any projects yet.</p>
                <p class="text-gray-400 text-xs sm:text-sm mt-2 px-4">Wait for your admin or leader to add you to a project.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                @foreach($activeProjects as $project)
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

    <!-- Recent Completed Projects History -->
    @if(isset($completedProjects) && $completedProjects->count() > 0)
        <div class="mt-6 bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-purple-500 to-indigo-600 p-4 sm:p-6">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
                    <h2 class="text-lg sm:text-xl font-bold text-white flex items-center">
                        <i class="fas fa-history mr-3"></i>
                        Riwayat Proyek yang Telah Dikerjakan
                    </h2>
                    <span class="bg-white bg-opacity-20 text-white px-4 py-2 rounded-lg text-sm font-medium self-start">
                        {{ $completedProjects->count() }} Proyek Selesai
                    </span>
                </div>
            </div>
            <div class="p-4 sm:p-6">
                <div class="space-y-4">
                    @foreach($completedProjects as $project)
                        <div class="border-l-4 border-green-500 bg-gradient-to-r from-green-50 to-white rounded-r-lg p-4 sm:p-5 hover:shadow-md transition-all duration-300">
                            <div class="flex flex-col gap-4">
                                <div class="flex-1">
                                    <div class="flex items-start sm:items-center gap-2 mb-2 flex-wrap">
                                        <h3 class="text-base sm:text-lg font-bold text-gray-800">{{ $project->project_name }}</h3>
                                        <span class="px-2.5 py-1 text-xs bg-green-100 text-green-700 rounded-full font-bold whitespace-nowrap">
                                            <i class="fas fa-check-circle mr-1"></i>SELESAI
                                        </span>
                                        @php
                                            $myRole = $project->members->where('user_id', Auth::id())->first();
                                        @endphp
                                        @if($myRole)
                                            <span class="px-2.5 py-1 text-xs bg-blue-100 text-blue-700 rounded-full font-semibold whitespace-nowrap">
                                                <i class="fas fa-user mr-1"></i>{{ ucfirst($myRole->role) }}
                                            </span>
                                        @endif
                                    </div>
                                    
                                    @if($project->description)
                                        <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $project->description }}</p>
                                    @endif
                                    
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs sm:text-sm">
                                        <p class="text-gray-600">
                                            <i class="fas fa-user text-xs mr-1 text-blue-500"></i>
                                            <span class="font-medium">Dibuat oleh:</span> {{ $project->creator->full_name }}
                                        </p>
                                        <p class="text-gray-600">
                                            <i class="fas fa-user-check text-xs mr-1 text-green-500"></i>
                                            <span class="font-medium">Diselesaikan oleh:</span> {{ $project->completedBy ? $project->completedBy->full_name : '-' }}
                                        </p>
                                        <p class="text-gray-600">
                                            <i class="fas fa-calendar-plus text-xs mr-1 text-indigo-500"></i>
                                            <span class="font-medium">Mulai:</span> {{ $project->created_at->format('d M Y') }}
                                        </p>
                                        <p class="text-gray-600">
                                            <i class="fas fa-calendar-check text-xs mr-1 text-green-500"></i>
                                            <span class="font-medium">Selesai:</span> {{ $project->completed_at ? $project->completed_at->format('d M Y') : '-' }}
                                        </p>
                                    </div>
                                    
                                    <div class="mt-3 flex items-center gap-2 sm:gap-3 flex-wrap text-xs">
                                        <span class="text-gray-500">
                                            <i class="fas fa-users mr-1"></i>
                                            {{ $project->members->count() }} anggota tim
                                        </span>
                                        @if($project->completed_at)
                                            <span class="text-gray-400 hidden sm:inline">•</span>
                                            <span class="text-gray-500">
                                                <i class="fas fa-clock mr-1"></i>
                                                {{ $project->completed_at->diffForHumans() }}
                                            </span>
                                        @endif
                                        @if($project->created_at && $project->completed_at)
                                            @php
                                                $duration = $project->created_at->diffInDays($project->completed_at);
                                            @endphp
                                            <span class="text-gray-400 hidden sm:inline">•</span>
                                            <span class="text-gray-500">
                                                <i class="fas fa-hourglass-half mr-1"></i>
                                                Durasi: {{ $duration }} hari
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="flex items-center justify-end">
                                    <span class="bg-green-100 text-green-700 px-3 sm:px-4 py-2 rounded-lg text-xs sm:text-sm font-medium flex items-center gap-2">
                                        <i class="fas fa-trophy"></i>
                                        <span class="hidden sm:inline">Completed</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                @if($completedProjects->count() >= 5)
                    <div class="mt-4 text-center">
                        <p class="text-xs sm:text-sm text-gray-500">
                            <i class="fas fa-info-circle mr-1"></i>
                            Menampilkan 5 proyek terbaru yang telah diselesaikan
                        </p>
                    </div>
                @endif
            </div>
        </div>
    @endif
@endsection