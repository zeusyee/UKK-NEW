@extends('layouts.admin')

@section('title', 'Task Review')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Tasks Pending Review</h2>
        <p class="text-gray-600 mt-2">Review and approve completed tasks</p>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-purple-50 rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Pending Reviews</p>
                    <p class="text-3xl font-bold text-purple-600">{{ $cardsInReview->count() }}</p>
                </div>
                <i class="fas fa-clipboard-check text-4xl text-purple-300"></i>
            </div>
        </div>

        <div class="bg-blue-50 rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">High Priority</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $cardsInReview->where('priority', 'high')->count() }}</p>
                </div>
                <i class="fas fa-exclamation-circle text-4xl text-blue-300"></i>
            </div>
        </div>

        <div class="bg-orange-50 rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Overdue</p>
                    <p class="text-3xl font-bold text-orange-600">
                        {{ $cardsInReview->filter(function($card) {
                            return $card->due_date && $card->due_date->isPast();
                        })->count() }}
                    </p>
                </div>
                <i class="fas fa-calendar-times text-4xl text-orange-300"></i>
            </div>
        </div>
    </div>

    <!-- Tasks List -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Tasks in Review</h3>
        </div>
        
        @if($cardsInReview->isEmpty())
            <div class="p-12 text-center">
                <i class="fas fa-check-double text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg">No tasks pending review.</p>
                <p class="text-gray-400 text-sm mt-2">All tasks are up to date!</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project / Board</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned To</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($cardsInReview as $card)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $card->card_title }}</div>
                                    @if($card->description)
                                        <div class="text-xs text-gray-500">{{ Str::limit($card->description, 50) }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $card->board->project->project_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $card->board->board_name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex -space-x-2">
                                        @foreach($card->assignments->take(3) as $assignment)
                                            <div class="h-8 w-8 rounded-full bg-blue-500 border-2 border-white flex items-center justify-center text-white text-xs font-semibold"
                                                 title="{{ $assignment->user->full_name }}">
                                                {{ strtoupper(substr($assignment->user->full_name, 0, 1)) }}
                                            </div>
                                        @endforeach
                                        @if($card->assignments->count() > 3)
                                            <div class="h-8 w-8 rounded-full bg-gray-400 border-2 border-white flex items-center justify-center text-white text-xs">
                                                +{{ $card->assignments->count() - 3 }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                        {{ $card->priority === 'high' ? 'bg-red-100 text-red-800' : 
                                           ($card->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                        {{ ucfirst($card->priority) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $card->updated_at->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('admin.review.show', $card) }}" 
                                       class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye mr-1"></i>Review
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection