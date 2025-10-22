@extends('layouts.leader')

@section('title', 'Edit Board')

@section('content')
    <div class="mb-6">
        <a href="{{ route('leader.project.details', $project) }}" class="text-green-600 hover:text-green-800">
            <i class="fas fa-arrow-left mr-2"></i>Back to Project
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Board</h2>

        <form action="{{ route('leader.board.update', ['project' => $project, 'board' => $board]) }}" method="POST">
            @csrf
            @method('PUT')

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="space-y-4">
                <div>
                    <label for="board_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Board Name *
                    </label>
                    <input type="text" 
                           id="board_name" 
                           name="board_name" 
                           value="{{ old('board_name', $board->board_name) }}" 
                           required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description
                    </label>
                    <textarea id="description" 
                              name="description" 
                              rows="4"
                              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">{{ old('description', $board->description) }}</textarea>
                </div>

                <div>
                    <label for="position" class="block text-sm font-medium text-gray-700 mb-2">
                        Position (Order)
                    </label>
                    <input type="number" 
                           id="position" 
                           name="position" 
                           value="{{ old('position', $board->position) }}" 
                           min="0"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-6">
                <a href="{{ route('leader.project.details', $project) }}" 
                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-6 rounded-lg">
                    Cancel
                </a>
                <button type="submit" 
                        class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-6 rounded-lg">
                    <i class="fas fa-save mr-2"></i>Update Board
                </button>
            </div>
        </form>
    </div>
@endsection