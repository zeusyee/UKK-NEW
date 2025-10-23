@extends('layouts.leader')

@section('title', 'Subtask Review')

@push('styles')
<style>
    .badge-notification {
        position: relative;
        top: -2px;
    }
</style>
@endpush

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Subtasks Pending Review</h2>
        <p class="text-gray-600 mt-2">Review and approve completed subtasks from your team</p>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-purple-50 rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Pending Reviews</p>
                    <p class="text-3xl font-bold text-purple-600">{{ $subtasksInReview->count() }}</p>
                </div>
                <i class="fas fa-clipboard-check text-4xl text-purple-300"></i>
            </div>
        </div>

        <div class="bg-blue-50 rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">From Projects</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $subtasksInReview->pluck('card.board.project_id')->unique()->count() }}</p>
                </div>
                <i class="fas fa-project-diagram text-4xl text-blue-300"></i>
            </div>
        </div>

        <div class="bg-green-50 rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Team Members</p>
                    <p class="text-3xl font-bold text-green-600">{{ $subtasksInReview->pluck('created_by')->unique()->count() }}</p>
                </div>
                <i class="fas fa-users text-4xl text-green-300"></i>
            </div>
        </div>
    </div>

    <!-- Subtasks List -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Subtasks in Review</h3>
        </div>
        
        @if($subtasksInReview->isEmpty())
            <div class="p-12 text-center">
                <i class="fas fa-check-double text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg">No subtasks pending review.</p>
                <p class="text-gray-400 text-sm mt-2">All subtasks are up to date!</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hours</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($subtasksInReview as $subtask)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $subtask->subtask_title }}</div>
                                    @if($subtask->description)
                                        <div class="text-xs text-gray-500">{{ Str::limit($subtask->description, 50) }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $subtask->card->card_title }}</div>
                                    <div class="text-xs text-gray-500">{{ $subtask->card->board->project->project_name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center text-white text-xs font-semibold mr-2">
                                            {{ strtoupper(substr($subtask->creator->full_name, 0, 1)) }}
                                        </div>
                                        <span class="text-sm text-gray-900">{{ $subtask->creator->full_name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($subtask->estimated_hours)
                                        Est: {{ $subtask->estimated_hours }}h
                                    @endif
                                    @if($subtask->actual_hours)
                                        <br>Actual: {{ $subtask->actual_hours }}h
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $subtask->updated_at->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('leader.review.show', ['project' => $subtask->card->board->project, 'board' => $subtask->card->board, 'card' => $subtask->card, 'subtask' => $subtask]) }}" 
                                       class="text-green-600 hover:text-green-900">
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