@extends('layouts.member')

@section('title', 'Task Details')

@section('content')
    <div class="mb-6">
        <a href="{{ route('member.my-tasks') }}" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left mr-2"></i>Back to My Tasks
        </a>
    </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="alert-notification bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert-notification bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <span>{{ session('error') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-red-700 hover:text-red-900">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert-notification bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <div class="flex items-start justify-between">
                    <div>
                        <strong><i class="fas fa-exclamation-triangle mr-2"></i>Whoops! There were some problems:</strong>
                        <ul class="list-disc list-inside mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="text-red-700 hover:text-red-900">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

    <!-- Task Header -->
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
                <p class="text-gray-600">
                    Project: <span class="font-semibold">{{ $project->project_name }}</span> / 
                    Board: <span class="font-semibold">{{ $board->board_name }}</span>
                </p>
            </div>
        </div>

        @if($card->description)
            <div class="mt-4 p-4 bg-gray-50 rounded">
                <h3 class="font-semibold text-gray-700 mb-2">Description</h3>
                <p class="text-gray-600">{{ $card->description }}</p>
            </div>
        @endif

        <!-- Task Meta Information -->
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

        <!-- Card Status Info & Progress Monitoring -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Overall Progress -->
            <div class="p-4 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg">
                <h3 class="font-semibold text-gray-700 mb-3 flex items-center">
                    <i class="fas fa-chart-line mr-2 text-blue-600"></i>
                    Overall Progress
                </h3>
                @php
                    $progressPercentage = $card->getProgressPercentage();
                    $subtasksCount = $card->getSubtasksCountByStatus();
                @endphp
                <div class="mb-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm text-gray-700 font-medium">Completion Rate</span>
                        <span class="text-2xl font-bold text-blue-600">{{ $progressPercentage }}%</span>
                    </div>
                    <div class="w-full bg-white rounded-full h-4 shadow-inner">
                        <div class="h-4 rounded-full transition-all duration-500 flex items-center justify-end pr-2 {{ $progressPercentage == 100 ? 'bg-gradient-to-r from-green-400 to-green-600' : 'bg-gradient-to-r from-blue-400 to-blue-600' }}" 
                             style="width: {{ $progressPercentage }}%">
                            @if($progressPercentage > 10)
                                <span class="text-xs text-white font-semibold">{{ $progressPercentage }}%</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="text-sm text-gray-700">
                    <div class="flex justify-between py-1">
                        <span>Total Subtasks:</span>
                        <span class="font-semibold">{{ $subtasksCount['total'] }}</span>
                    </div>
                    <div class="flex justify-between py-1">
                        <span>Completed:</span>
                        <span class="font-semibold text-green-600">{{ $subtasksCount['done'] }}</span>
                    </div>
                    <div class="flex justify-between py-1">
                        <span>Remaining:</span>
                        <span class="font-semibold text-orange-600">{{ $subtasksCount['total'] - $subtasksCount['done'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Subtasks Breakdown -->
            <div class="p-4 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg">
                <h3 class="font-semibold text-gray-700 mb-3 flex items-center">
                    <i class="fas fa-list-check mr-2 text-purple-600"></i>
                    Subtasks Breakdown
                </h3>
                <div class="space-y-3">
                    <!-- To Do -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-gray-400 rounded-full mr-2"></div>
                            <span class="text-sm text-gray-700">To Do</span>
                        </div>
                        <span class="px-3 py-1 bg-white rounded-full text-sm font-semibold text-gray-700">
                            {{ $subtasksCount['todo'] }}
                        </span>
                    </div>
                    <!-- In Progress -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-blue-500 rounded-full mr-2 animate-pulse"></div>
                            <span class="text-sm text-gray-700">In Progress</span>
                        </div>
                        <span class="px-3 py-1 bg-white rounded-full text-sm font-semibold text-blue-600">
                            {{ $subtasksCount['in_progress'] }}
                        </span>
                    </div>
                    <!-- In Review -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></div>
                            <span class="text-sm text-gray-700">In Review</span>
                        </div>
                        <span class="px-3 py-1 bg-white rounded-full text-sm font-semibold text-yellow-600">
                            {{ $subtasksCount['review'] }}
                        </span>
                    </div>
                    <!-- Done -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                            <span class="text-sm text-gray-700">Done</span>
                        </div>
                        <span class="px-3 py-1 bg-white rounded-full text-sm font-semibold text-green-600">
                            {{ $subtasksCount['done'] }}
                        </span>
                    </div>
                </div>
                
                @if($progressPercentage == 100)
                    <div class="mt-4 p-3 bg-green-100 border border-green-300 rounded-lg">
                        <p class="text-sm text-green-800 font-semibold flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            All subtasks completed! 🎉
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Subtasks Section -->
    <div class="bg-white rounded-lg shadow-md p-6">
        @php
            $hasInProgressSubtask = $card->subtasks->where('status', 'in_progress')->isNotEmpty();
            $inProgressSubtask = $card->subtasks->where('status', 'in_progress')->first();
        @endphp
        
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-gray-800">
                My Subtasks 
                <span class="text-sm font-normal text-gray-500">
                    ({{ $card->subtasks->where('status', 'done')->count() }}/{{ $card->subtasks->count() }} completed)
                </span>
            </h3>
            <button onclick="toggleSubtaskForm()" 
                    {{ $hasInProgressSubtask ? 'disabled' : '' }}
                    class="px-4 py-2 rounded-lg transition-colors {{ $hasInProgressSubtask ? 'bg-gray-300 text-gray-500 cursor-not-allowed' : 'bg-green-500 hover:bg-green-600 text-white' }}">
                <i class="fas fa-plus mr-2"></i>Add Subtask
            </button>
        </div>

        @if($hasInProgressSubtask)
            <div class="mb-4 p-4 bg-yellow-50 border-l-4 border-yellow-500 rounded">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-triangle text-yellow-500 mt-1 mr-3"></i>
                    <div>
                        <p class="font-semibold text-gray-800">Subtask In Progress</p>
                        <p class="text-sm text-gray-700">Complete or submit "<strong>{{ $inProgressSubtask->subtask_title }}</strong>" before creating or starting another subtask.</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Add Subtask Form (Hidden by default) -->
        <div id="subtask-form" class="hidden mb-6 p-6 bg-gray-50 rounded-lg border border-gray-200">
            <h4 class="font-semibold text-gray-800 mb-4">Create New Subtask</h4>
            <form action="{{ route('member.subtask.store', ['project' => $project, 'board' => $board, 'card' => $card]) }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Subtask Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="subtask_title" required 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                               placeholder="Enter subtask title">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea name="description" rows="3" 
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                                  placeholder="Describe the subtask"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Estimated Hours</label>
                        <input type="number" name="estimated_hours" step="0.5" min="0" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                               placeholder="0.0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Position</label>
                        <input type="number" name="position" min="0" value="0"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                </div>
                <div class="flex justify-end space-x-2 mt-6">
                    <button type="button" onclick="toggleSubtaskForm()" 
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-save mr-2"></i>Create Subtask
                    </button>
                </div>
            </form>
        </div>

        <!-- Subtasks List -->
        @if($card->subtasks->isEmpty())
            <div class="text-center py-12">
                <i class="fas fa-list-check text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg font-medium">No subtasks yet.</p>
                <p class="text-gray-400 text-sm mt-2">Click "Add Subtask" above to break down this card into smaller tasks.</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($card->subtasks->sortBy('position') as $subtask)
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow bg-white">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <span class="px-2 py-1 text-xs rounded-full font-semibold
                                        {{ $subtask->status === 'done' ? 'bg-green-100 text-green-800' : 
                                           ($subtask->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 
                                           ($subtask->status === 'review' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800')) }}">
                                        {{ $subtask->status === 'review' ? 'In Review' : ucfirst(str_replace('_', ' ', $subtask->status)) }}
                                    </span>
                                    <h4 class="font-semibold text-gray-800">{{ $subtask->subtask_title }}</h4>
                                    
                                    @if($subtask->status === 'in_progress' && $subtask->paused_at)
                                        <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full font-semibold">
                                            <i class="fas fa-pause mr-1"></i>PAUSED
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="flex space-x-4 text-xs text-gray-500 mb-2">
                                    @if($subtask->estimated_hours)
                                        <span><i class="fas fa-clock mr-1"></i>Est: {{ $subtask->estimated_hours }}h</span>
                                    @endif
                                    @if($subtask->actual_hours)
                                        <span><i class="fas fa-check-circle mr-1"></i>Actual: {{ $subtask->actual_hours }}h</span>
                                    @endif
                                    @if($subtask->status === 'in_progress' && $subtask->started_at)
                                        <span class="text-blue-600 font-semibold">
                                            <i class="fas fa-hourglass-half mr-1"></i>
                                            <span id="timer-mini-{{ $subtask->subtask_id }}">00:00:00</span>
                                        </span>
                                    @endif
                                </div>

                                @if($subtask->description)
                                    <p class="text-sm text-gray-600 line-clamp-2">{{ $subtask->description }}</p>
                                @endif
                            </div>
                            
                            <div class="flex items-center space-x-2 ml-4">
                                <button onclick="openDetailModal{{ $subtask->subtask_id }}()" 
                                        class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition-colors flex items-center">
                                    <i class="fas fa-eye mr-2"></i>View Details
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Detail Modal for this subtask -->
                    <div id="detail-modal-{{ $subtask->subtask_id }}" class="hidden fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full z-50">
                        <div class="relative top-10 mx-auto p-0 border max-w-3xl shadow-2xl rounded-lg bg-white mb-10">
                            <!-- Modal Header -->
                            <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white px-6 py-4 rounded-t-lg">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-xl font-bold flex items-center">
                                        <i class="fas fa-tasks mr-3"></i>
                                        Subtask Details
                                    </h3>
                                    <button onclick="closeDetailModal{{ $subtask->subtask_id }}()" 
                                            class="text-white hover:text-gray-200 transition-colors">
                                        <i class="fas fa-times text-2xl"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Modal Body -->
                            <div class="p-6 max-h-[calc(100vh-200px)] overflow-y-auto">
                                <!-- Subtask Title & Status -->
                                <div class="mb-6">
                                    <div class="flex items-center space-x-3 mb-3">
                                        <span class="px-3 py-1 text-sm rounded-full font-semibold
                                            {{ $subtask->status === 'done' ? 'bg-green-100 text-green-800' : 
                                               ($subtask->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 
                                               ($subtask->status === 'review' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800')) }}">
                                            {{ $subtask->status === 'review' ? 'In Review' : ucfirst(str_replace('_', ' ', $subtask->status)) }}
                                        </span>
                                        @if($subtask->status === 'in_progress' && $subtask->paused_at)
                                            <span class="px-3 py-1 text-sm bg-yellow-100 text-yellow-800 rounded-full font-semibold">
                                                <i class="fas fa-pause mr-1"></i>PAUSED
                                            </span>
                                        @endif
                                    </div>
                                    <h4 class="text-2xl font-bold text-gray-800">{{ $subtask->subtask_title }}</h4>
                                </div>

                                <!-- Description -->
                                @if($subtask->description)
                                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                                        <h5 class="font-semibold text-gray-700 mb-2 flex items-center">
                                            <i class="fas fa-align-left mr-2 text-blue-600"></i>
                                            Description
                                        </h5>
                                        <p class="text-gray-700">{{ $subtask->description }}</p>
                                    </div>
                                @endif

                                <!-- Timer Display (for in_progress) -->
                                @if($subtask->status === 'in_progress' && $subtask->started_at)
                                    <div class="mb-6 p-6 rounded-lg border-2 {{ $subtask->paused_at ? 'bg-gray-50 border-gray-400' : 'bg-blue-50 border-blue-500' }}">
                                        <div class="text-center">
                                            <div class="text-sm font-semibold {{ $subtask->paused_at ? 'text-gray-600' : 'text-blue-700' }} mb-2">
                                                {{ $subtask->paused_at ? '⏸️ TIMER PAUSED' : '⏱️ TIME REMAINING' }}
                                            </div>
                                            <div class="text-5xl font-bold {{ $subtask->paused_at ? 'text-gray-700' : 'text-blue-600' }} mb-2" id="timer-modal-{{ $subtask->subtask_id }}">
                                                @if($subtask->estimated_hours)
                                                    {{ gmdate('H:i:s', $subtask->estimated_hours * 3600) }}
                                                @else
                                                    --:--:--
                                                @endif
                                            </div>
                                            @if($subtask->estimated_hours)
                                                <div class="text-sm {{ $subtask->paused_at ? 'text-gray-600' : 'text-blue-600' }}">
                                                    Estimated: {{ $subtask->estimated_hours }} hours
                                                </div>
                                            @else
                                                <div class="text-sm text-red-600 font-semibold">⚠️ No time estimate set!</div>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                <!-- Subtask Information Grid -->
                                <div class="grid grid-cols-2 gap-4 mb-6">
                                    <div class="bg-blue-50 p-4 rounded-lg">
                                        <p class="text-xs text-gray-600 mb-1"><i class="fas fa-clock mr-1"></i>Estimated Hours</p>
                                        <p class="text-xl font-bold text-gray-800">{{ $subtask->estimated_hours ?? '-' }}</p>
                                    </div>
                                    <div class="bg-green-50 p-4 rounded-lg">
                                        <p class="text-xs text-gray-600 mb-1"><i class="fas fa-check-circle mr-1"></i>Actual Hours</p>
                                        <p class="text-xl font-bold text-gray-800">{{ $subtask->actual_hours ?? '-' }}</p>
                                    </div>
                                    @if($subtask->started_at)
                                        <div class="bg-purple-50 p-4 rounded-lg">
                                            <p class="text-xs text-gray-600 mb-1"><i class="fas fa-play-circle mr-1"></i>Started At</p>
                                            <p class="text-sm font-semibold text-gray-800">{{ $subtask->started_at->format('M d, Y H:i') }}</p>
                                        </div>
                                    @endif
                                    @if($subtask->completed_at)
                                        <div class="bg-green-50 p-4 rounded-lg">
                                            <p class="text-xs text-gray-600 mb-1"><i class="fas fa-check-double mr-1"></i>Completed At</p>
                                            <p class="text-sm font-semibold text-gray-800">{{ $subtask->completed_at->format('M d, Y H:i') }}</p>
                                        </div>
                                    @endif
                                </div>

                                <!-- Completion Notes -->
                                @if($subtask->completion_notes)
                                    <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                                        <h5 class="font-semibold text-gray-700 mb-2 flex items-center">
                                            <i class="fas fa-sticky-note mr-2 text-blue-600"></i>
                                            Completion Notes
                                        </h5>
                                        <p class="text-gray-700">{{ $subtask->completion_notes }}</p>
                                    </div>
                                @endif

                                <!-- Review Notes -->
                                @if($subtask->review_notes && $subtask->reviewed_at)
                                    <div class="mb-6 p-4 {{ $subtask->status === 'done' ? 'bg-green-50 border-l-4 border-green-500' : 'bg-red-50 border-l-4 border-red-500' }} rounded">
                                        <p class="text-sm font-bold {{ $subtask->status === 'done' ? 'text-green-700' : 'text-red-700' }} mb-2">
                                            @if($subtask->status === 'done')
                                                <i class="fas fa-check-circle mr-1"></i> ✅ Approved by Leader
                                            @else
                                                <i class="fas fa-exclamation-triangle mr-1"></i> ⚠️ Revision Required
                                            @endif
                                        </p>
                                        <p class="text-gray-700 mb-2">{{ $subtask->review_notes }}</p>
                                        <p class="text-xs text-gray-600">
                                            <i class="fas fa-user mr-1"></i>
                                            Reviewed by {{ $subtask->reviewer->full_name ?? 'Leader' }} 
                                            on {{ $subtask->reviewed_at->format('M d, Y H:i') }}
                                        </p>
                                    </div>
                                @endif

                                <!-- Action Buttons -->
                                <div class="border-t pt-6">
                                    <h5 class="font-semibold text-gray-700 mb-4 flex items-center">
                                        <i class="fas fa-bolt mr-2 text-yellow-500"></i>
                                        Actions
                                    </h5>
                                    
                                    <div class="space-y-3">
                                        @if($subtask->status === 'todo')
                                            <form action="{{ route('member.subtask.start', ['project' => $project, 'board' => $board, 'card' => $card, 'subtask' => $subtask]) }}" method="POST">
                                                @csrf
                                                <button type="submit" 
                                                        {{ $hasInProgressSubtask ? 'disabled' : '' }}
                                                        class="w-full px-4 py-3 rounded-lg font-semibold transition-colors {{ $hasInProgressSubtask ? 'bg-gray-300 text-gray-500 cursor-not-allowed' : 'bg-green-500 hover:bg-green-600 text-white' }}">
                                                    <i class="fas fa-play mr-2"></i>Start Working on This Subtask
                                                </button>
                                            </form>
                                            @if($hasInProgressSubtask)
                                                <p class="text-xs text-center text-red-600">
                                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                                    Complete "{{ $inProgressSubtask->subtask_title }}" before starting this one
                                                </p>
                                            @endif
                                        @endif

                                        @if($subtask->status === 'in_progress')
                                            <div class="grid grid-cols-2 gap-3">
                                                @if($subtask->paused_at)
                                                    <form action="{{ route('member.subtask.resume', ['project' => $project, 'board' => $board, 'card' => $card, 'subtask' => $subtask]) }}" method="POST" class="col-span-2">
                                                        @csrf
                                                        <button type="submit" class="w-full px-4 py-3 bg-green-500 hover:bg-green-600 text-white rounded-lg font-semibold transition-colors">
                                                            <i class="fas fa-play mr-2"></i>Resume Timer
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('member.subtask.pause', ['project' => $project, 'board' => $board, 'card' => $card, 'subtask' => $subtask]) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="w-full px-4 py-3 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg font-semibold transition-colors">
                                                            <i class="fas fa-pause mr-2"></i>Pause Timer
                                                        </button>
                                                    </form>
                                                @endif
                                                
                                                <button type="button" onclick="showSubmitForm{{ $subtask->subtask_id }}()" 
                                                        class="px-4 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-semibold transition-colors {{ $subtask->paused_at ? 'col-span-2' : '' }}">
                                                    <i class="fas fa-paper-plane mr-2"></i>Submit for Review
                                                </button>
                                            </div>

                                            <!-- Submit Form (Hidden initially) -->
                                            <div id="submit-form-{{ $subtask->subtask_id }}" class="hidden p-4 bg-gray-50 rounded-lg border border-gray-300">
                                                <h6 class="font-semibold text-gray-800 mb-3">Submit for Review</h6>
                                                <form action="{{ route('member.subtask.submit', ['project' => $project, 'board' => $board, 'card' => $card, 'subtask' => $subtask]) }}" method="POST">
                                                    @csrf
                                                    <div class="mb-3">
                                                        <label class="block text-sm font-medium text-gray-700 mb-2">Actual Hours Spent</label>
                                                        <input type="number" 
                                                               name="actual_hours" 
                                                               step="0.5" 
                                                               min="0"
                                                               value="{{ $subtask->actual_hours }}"
                                                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                               placeholder="e.g., 5.5">
                                        </div>
                                                        
                                                        <div class="mb-4">
                                                            <label class="block text-sm font-medium text-gray-700 mb-2">Completion Notes</label>
                                                            <textarea name="completion_notes" 
                                                                      rows="3"
                                                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                                      placeholder="Add notes about your work...">{{ $subtask->completion_notes }}</textarea>
                                                        </div>
                                                        
                                                        <div class="flex space-x-2">
                                                            <button type="button" onclick="hideSubmitForm{{ $subtask->subtask_id }}()" 
                                                                    class="flex-1 px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg font-medium transition-colors">
                                                                Cancel
                                                            </button>
                                                            <button type="submit" class="flex-1 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-semibold transition-colors">
                                                                <i class="fas fa-paper-plane mr-2"></i>Submit
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            @endif

                                            @if($subtask->status === 'review')
                                                <div class="text-center p-4 bg-purple-50 rounded-lg">
                                                    <i class="fas fa-hourglass-half text-4xl text-purple-500 mb-2"></i>
                                                    <p class="font-semibold text-gray-800">Waiting for Leader Review</p>
                                                    <p class="text-sm text-gray-600 mt-1">Your subtask is being reviewed by the project leader</p>
                                                </div>
                                            @endif

                                            @if($subtask->status === 'done')
                                                <div class="text-center p-4 bg-green-50 rounded-lg">
                                                    <i class="fas fa-check-circle text-4xl text-green-500 mb-2"></i>
                                                    <p class="font-semibold text-gray-800">Subtask Completed! 🎉</p>
                                                    <p class="text-sm text-gray-600 mt-1">This subtask has been approved and completed</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- JavaScript Functions for this subtask modal -->
                    <script>
                        function openDetailModal{{ $subtask->subtask_id }}() {
                            document.getElementById('detail-modal-{{ $subtask->subtask_id }}').classList.remove('hidden');
                            document.body.style.overflow = 'hidden';
                        }

                        function closeDetailModal{{ $subtask->subtask_id }}() {
                            document.getElementById('detail-modal-{{ $subtask->subtask_id }}').classList.add('hidden');
                            document.body.style.overflow = 'auto';
                            // Hide submit form if open
                            const submitForm = document.getElementById('submit-form-{{ $subtask->subtask_id }}');
                            if (submitForm) {
                                submitForm.classList.add('hidden');
                            }
                        }

                        function showSubmitForm{{ $subtask->subtask_id }}() {
                            document.getElementById('submit-form-{{ $subtask->subtask_id }}').classList.remove('hidden');
                        }

                        function hideSubmitForm{{ $subtask->subtask_id }}() {
                            document.getElementById('submit-form-{{ $subtask->subtask_id }}').classList.add('hidden');
                        }

                        // Close modal when clicking outside
                        document.getElementById('detail-modal-{{ $subtask->subtask_id }}').addEventListener('click', function(e) {
                            if (e.target === this) {
                                closeDetailModal{{ $subtask->subtask_id }}();
                            }
                        });

                        @if($subtask->status === 'in_progress' && $subtask->started_at && !$subtask->paused_at)
                            // Update timer in modal
                            function updateModalTimer{{ $subtask->subtask_id }}() {
                                const timerElement = document.getElementById('timer-modal-{{ $subtask->subtask_id }}');
                                const timerMiniElement = document.getElementById('timer-mini-{{ $subtask->subtask_id }}');
                                
                                if (!timerElement) return;

                                const startTime = new Date('{{ $subtask->started_at->toIso8601String() }}');
                                const now = new Date();
                                const totalPausedSeconds = {{ $subtask->total_paused_seconds }};
                                const elapsedSeconds = Math.floor((now - startTime) / 1000) - totalPausedSeconds;

                                @if($subtask->estimated_hours)
                                    // Countdown mode
                                    const totalEstimatedSeconds = {{ $subtask->estimated_hours }} * 3600;
                                    const remainingSeconds = totalEstimatedSeconds - elapsedSeconds;

                                    if (remainingSeconds <= 0) {
                                        // Time's up - show overtime
                                        const overtimeSeconds = Math.abs(remainingSeconds);
                                        const hours = Math.floor(overtimeSeconds / 3600);
                                        const minutes = Math.floor((overtimeSeconds % 3600) / 60);
                                        const seconds = overtimeSeconds % 60;

                                        const timeString = 
                                            '-' + String(hours).padStart(2, '0') + ':' + 
                                            String(minutes).padStart(2, '0') + ':' + 
                                            String(seconds).padStart(2, '0');

                                        timerElement.textContent = timeString;
                                        if (timerMiniElement) timerMiniElement.textContent = timeString;
                                        
                                        timerElement.classList.remove('text-blue-600');
                                        timerElement.classList.add('text-red-600');
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
                                        if (timerMiniElement) timerMiniElement.textContent = timeString;
                                    }
                                @else
                                    // Count up mode
                                    const hours = Math.floor(elapsedSeconds / 3600);
                                    const minutes = Math.floor((elapsedSeconds % 3600) / 60);
                                    const seconds = elapsedSeconds % 60;

                                    const timeString = 
                                        String(hours).padStart(2, '0') + ':' + 
                                        String(minutes).padStart(2, '0') + ':' + 
                                        String(seconds).padStart(2, '0');

                                    timerElement.textContent = timeString;
                                    if (timerMiniElement) timerMiniElement.textContent = timeString;
                                @endif
                            }

                            // Update timer every second
                            updateModalTimer{{ $subtask->subtask_id }}();
                            setInterval(updateModalTimer{{ $subtask->subtask_id }}, 1000);
                        @endif
                    </script>
                @endforeach
            </div>
        @endif
    </div>

    <script>
        function toggleSubtaskForm() {
            const form = document.getElementById('subtask-form');
            form.classList.toggle('hidden');
        }

        function toggleSubtaskEdit(subtaskId) {
            const editForm = document.getElementById('subtask-edit-' + subtaskId);
            editForm.classList.toggle('hidden');
        }

        function openSubmitModal(subtaskId) {
            document.getElementById('submit-modal-' + subtaskId).classList.remove('hidden');
        }

        function closeSubmitModal(subtaskId) {
            document.getElementById('submit-modal-' + subtaskId).classList.add('hidden');
        }

        // Update all countdown timers
        function updateCountdownTimer(subtaskId, startedAt, estimatedHours, totalPausedSeconds = 0, isPaused = false) {
            if (isPaused) return;
            
            const timerElement = document.getElementById('timer-' + subtaskId);
            const timerMainElement = document.getElementById('timer-main-' + subtaskId);
            
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

                    if (timerElement) timerElement.textContent = timeString;
                    if (timerMainElement) timerMainElement.textContent = timeString;
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

                        if (timerElement) timerElement.textContent = timeString;
                        if (timerMainElement) {
                            timerMainElement.textContent = timeString;
                            timerMainElement.classList.add('text-red-600');
                            timerMainElement.classList.remove('text-blue-600');
                        }
                    } else {
                        // Still have time
                        const hours = Math.floor(remainingSeconds / 3600);
                        const minutes = Math.floor((remainingSeconds % 3600) / 60);
                        const seconds = remainingSeconds % 60;

                        const timeString = 
                            String(hours).padStart(2, '0') + ':' + 
                            String(minutes).padStart(2, '0') + ':' + 
                            String(seconds).padStart(2, '0');

                        if (timerElement) timerElement.textContent = timeString;
                        if (timerMainElement) timerMainElement.textContent = timeString;
                    }
                }
            }

            updateTime();
            setInterval(updateTime, 1000);
        }

        // Initialize timers on page load
        document.addEventListener('DOMContentLoaded', function() {
            @foreach($card->subtasks as $subtask)
                @if($subtask->status === 'in_progress' && $subtask->started_at)
                    updateCountdownTimer(
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