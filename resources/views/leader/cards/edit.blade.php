@extends('layouts.leader')

@section('title', 'Edit Card')

@section('content')
    <div class="mb-6">
        <a href="{{ route('leader.card.show', ['project' => $project, 'board' => $board, 'card' => $card]) }}" class="text-green-600 hover:text-green-800">
            <i class="fas fa-arrow-left mr-2"></i>Back to Card
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Edit Card</h2>
        <p class="text-gray-600 mb-6">Board: <span class="font-semibold">{{ $board->board_name }}</span></p>

        <form action="{{ route('leader.card.update', ['project' => $project, 'board' => $board, 'card' => $card]) }}" method="POST">
            @csrf
            @method('PUT')

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <strong>Whoops! There were some problems with your input:</strong>
                    <ul class="list-disc list-inside mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Card Title -->
                <div class="md:col-span-2">
                    <label for="card_title" class="block text-sm font-medium text-gray-700 mb-2">
                        Card Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="card_title" 
                           name="card_title" 
                           value="{{ old('card_title', $card->card_title) }}" 
                           required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                           placeholder="Enter task title">
                    @error('card_title')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description
                    </label>
                    <textarea id="description" 
                              name="description" 
                              rows="4"
                              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                              placeholder="Describe the task in detail">{{ old('description', $card->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Priority -->
                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
                        Priority <span class="text-red-500">*</span>
                    </label>
                    <select id="priority" 
                            name="priority" 
                            required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="low" {{ old('priority', $card->priority) === 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ old('priority', $card->priority) === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ old('priority', $card->priority) === 'high' ? 'selected' : '' }}>High</option>
                    </select>
                    @error('priority')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Due Date -->
                <div>
                    <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Due Date
                    </label>
                    <input type="date" 
                           id="due_date" 
                           name="due_date" 
                           value="{{ old('due_date', $card->due_date ? $card->due_date->format('Y-m-d') : '') }}"
                           min="{{ date('Y-m-d') }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                    @error('due_date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Estimated Hours -->
                <div>
                    <label for="estimated_hours" class="block text-sm font-medium text-gray-700 mb-2">
                        Estimated Hours
                    </label>
                    <input type="number" 
                           id="estimated_hours" 
                           name="estimated_hours" 
                           value="{{ old('estimated_hours', $card->estimated_hours) }}" 
                           step="0.5" 
                           min="0"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                           placeholder="0.0">
                    @error('estimated_hours')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Actual Hours -->
                <div>
                    <label for="actual_hours" class="block text-sm font-medium text-gray-700 mb-2">
                        Actual Hours
                    </label>
                    <input type="number" 
                           id="actual_hours" 
                           name="actual_hours" 
                           value="{{ old('actual_hours', $card->actual_hours) }}" 
                           step="0.5" 
                           min="0"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                           placeholder="0.0">
                    @error('actual_hours')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Assign To Members -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        <i class="fas fa-users mr-2"></i>Assign To Team Members
                    </label>
                    @if($projectMembers->isEmpty())
                        <p class="text-gray-500 text-sm">No team members available to assign.</p>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 max-h-64 overflow-y-auto border border-gray-200 rounded-lg p-4 bg-gray-50">
                            @foreach($projectMembers as $member)
                                <label class="flex items-center space-x-3 cursor-pointer hover:bg-white p-3 rounded-lg transition-colors border border-transparent hover:border-green-200">
                                    <input type="checkbox" 
                                           name="assigned_users[]" 
                                           value="{{ $member->user_id }}"
                                           {{ in_array($member->user_id, old('assigned_users', $assignedUserIds)) ? 'checked' : '' }}
                                           class="rounded text-green-500 focus:ring-green-500">
                                    <div class="flex items-center space-x-2">
                                        <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold text-sm">
                                            {{ strtoupper(substr($member->user->full_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-800">{{ $member->user->full_name }}</p>
                                            <p class="text-xs text-gray-500">{{ ucfirst($member->role) }}</p>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @endif
                    @error('assigned_users')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('leader.card.show', ['project' => $project, 'board' => $board, 'card' => $card]) }}" 
                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-6 rounded-lg transition-colors">
                    <i class="fas fa-times mr-2"></i>Cancel
                </a>
                <button type="submit" 
                        class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-6 rounded-lg transition-colors">
                    <i class="fas fa-save mr-2"></i>Update Card
                </button>
            </div>
        </form>
    </div>
@endsection