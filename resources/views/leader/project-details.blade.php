@extends('layouts.leader')

@section('title', 'Project Details')

@section('content')
    <div class="mb-6">
        <a href="{{ route('leader.dashboard') }}" class="text-green-600 hover:text-green-800 mb-4 inline-block">
            <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
        </a>
    </div>

    <!-- Project Header -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h2 class="text-3xl font-bold text-gray-800">{{ $project->project_name }}</h2>
                <p class="text-gray-600 mt-2">{{ $project->description }}</p>
            </div>
            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                <i class="fas fa-crown mr-1"></i>{{ ucfirst($member->role) }}
            </span>
        </div>

        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
            <div class="bg-blue-50 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Total Boards</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $projectStats['total_boards'] }}</p>
                    </div>
                    <i class="fas fa-columns text-3xl text-blue-300"></i>
                </div>
            </div>

            <div class="bg-purple-50 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Cards Progress</p>
                        <p class="text-2xl font-bold text-purple-600">{{ $projectStats['completed_cards'] }}/{{ $projectStats['total_cards'] }}</p>
                        <p class="text-xs text-gray-500">{{ $projectStats['card_progress'] }}%</p>
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
    </div>

    <!-- Add Board Button -->
    <div class="mb-6">
        <a href="{{ route('leader.board.create', $project) }}" 
           class="inline-flex items-center bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded">
            <i class="fas fa-plus mr-2"></i>Create New Board
        </a>
    </div>

    <!-- Boards and Cards -->
    @if($project->boards->isEmpty())
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <i class="fas fa-folder-open text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg">No boards created yet.</p>
            <p class="text-gray-400 text-sm mt-2">Create your first board to start organizing tasks.</p>
        </div>
    @else
        <div class="space-y-6">
            @foreach($project->boards as $board)
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <!-- Board Header -->
                    <div class="bg-gradient-to-r from-green-500 to-green-600 p-4">
                        <div class="flex justify-between items-center">
                            <div class="text-white">
                                <h3 class="text-xl font-bold">{{ $board->board_name }}</h3>
                                @if($board->description)
                                    <p class="text-green-100 text-sm mt-1">{{ $board->description }}</p>
                                @endif
                            </div>
                            <div class="flex space-x-2">
                                <a href="{{ route('leader.card.create', ['project' => $project, 'board' => $board]) }}" 
                                   class="bg-white text-green-600 px-3 py-1 rounded hover:bg-green-50 text-sm font-semibold">
                                    <i class="fas fa-plus mr-1"></i>Add Card
                                </a>
                                <a href="{{ route('leader.board.edit', ['project' => $project, 'board' => $board]) }}" 
                                   class="bg-green-700 text-white px-3 py-1 rounded hover:bg-green-800 text-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('leader.board.destroy', ['project' => $project, 'board' => $board]) }}" 
                                      method="POST" class="inline"
                                      onsubmit="return confirm('Are you sure you want to delete this board and all its cards?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Cards -->
                    <div class="p-4">
                        @if($board->cards->isEmpty())
                            <p class="text-gray-400 text-center py-8">No cards in this board yet.</p>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($board->cards as $card)
                                    <div class="border-2 border-gray-200 rounded-lg p-4 hover:shadow-lg transition-shadow cursor-pointer"
                                         onclick="window.location='{{ route('leader.card.show', ['project' => $project, 'board' => $board, 'card' => $card]) }}'">
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
                                            <span class="px-2 py-1 text-xs rounded-full {{ $card->status === 'done' ? 'bg-green-100 text-green-800' : ($card->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : ($card->status === 'review' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800')) }}">
                                                {{ ucfirst(str_replace('_', ' ', $card->status)) }}
                                            </span>
                                        </div>

                                        <!-- Subtasks Progress -->
                                        @if($card->subtasks->count() > 0)
                                            <div class="mb-3">
                                                <div class="flex justify-between text-xs text-gray-600 mb-1">
                                                    <span>Subtasks</span>
                                                    <span>{{ $card->subtasks->where('status', 'done')->count() }}/{{ $card->subtasks->count() }}</span>
                                                </div>
                                                <div class="w-full bg-gray-200 rounded-full h-2">
                                                    <div class="bg-green-500 h-2 rounded-full" style="width: {{ $card->subtasks->count() > 0 ? round(($card->subtasks->where('status', 'done')->count() / $card->subtasks->count()) * 100) : 0 }}%"></div>
                                                </div>
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
                                            <div class="flex -space-x-2">
                                                @foreach($card->assignments->take(3) as $assignment)
                                                    <div class="h-6 w-6 rounded-full bg-blue-500 border-2 border-white flex items-center justify-center text-white text-xs font-semibold"
                                                         title="{{ $assignment->user->full_name }}">
                                                        {{ strtoupper(substr($assignment->user->full_name, 0, 1)) }}
                                                    </div>
                                                @endforeach
                                                @if($card->assignments->count() > 3)
                                                    <div class="h-6 w-6 rounded-full bg-gray-400 border-2 border-white flex items-center justify-center text-white text-xs">
                                                        +{{ $card->assignments->count() - 3 }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection