@extends('layouts.leader')

@section('title', 'Project Members')

@section('content')
    <div class="mb-6">
        <a href="{{ route('leader.project.details', $project) }}" class="text-green-600 hover:text-green-800 mb-4 inline-block">
            <i class="fas fa-arrow-left mr-2"></i>Back to Project
        </a>
    </div>

    <!-- Project Header -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3">
            <div class="flex-1">
                <h2 class="text-3xl font-bold text-gray-800 mb-2">{{ $project->project_name }}</h2>
                <p class="text-gray-600 text-sm">Team Members</p>
            </div>
            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold whitespace-nowrap self-start">
                <i class="fas fa-crown mr-1"></i>{{ ucfirst($member->role) }}
            </span>
        </div>
    </div>

    <!-- Members Count Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-blue-50 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Members</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $membersData->count() }}</p>
                </div>
                <i class="fas fa-users text-3xl text-blue-300"></i>
            </div>
        </div>

        <div class="bg-purple-50 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Members with Tasks</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $membersData->filter(fn($m) => $m['total_cards'] > 0)->count() }}</p>
                </div>
                <i class="fas fa-user-check text-3xl text-purple-300"></i>
            </div>
        </div>

        <div class="bg-green-50 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Cards Assigned</p>
                    <p class="text-2xl font-bold text-green-600">{{ $membersData->sum('total_cards') }}</p>
                </div>
                <i class="fas fa-tasks text-3xl text-green-300"></i>
            </div>
        </div>
    </div>

    <!-- Members List -->
    @if($membersData->isEmpty())
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
            <i class="fas fa-info-circle text-yellow-600 text-3xl mb-3"></i>
            <p class="text-yellow-800 font-semibold">No team members found</p>
            <p class="text-yellow-700 text-sm mt-1">Start by adding members to this project</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($membersData as $data)
                @php
                    $user = $data['user'];
                    $totalCards = $data['total_cards'];
                @endphp
                
                <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow overflow-hidden">
                    <!-- Member Card -->
                    <div class="p-6">
                        <div class="flex items-start space-x-4 mb-4">
                            <div class="h-16 w-16 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold text-2xl shadow-md">
                                {{ strtoupper(substr($user->full_name, 0, 1)) }}
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-gray-800">{{ $user->full_name }}</h3>
                                <p class="text-sm text-gray-600 mb-2">{{ $user->email }}</p>
                                <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded">
                                    <i class="fas fa-user mr-1"></i>Member
                                </span>
                            </div>
                        </div>

                        <!-- Cards Assigned Info -->
                        <div class="border-t border-gray-200 pt-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Cards Assigned</span>
                                <span class="text-2xl font-bold text-blue-600">{{ $totalCards }}</span>
                            </div>
                            
                            @if($totalCards > 0)
                                <div class="mt-3 space-y-2">
                                    @foreach($data['assigned_cards'] as $cardData)
                                        @php
                                            $card = $cardData['card'];
                                        @endphp
                                        
                                        <div class="bg-gray-50 border border-gray-200 rounded p-2 hover:bg-gray-100 transition-colors">
                                            <div class="flex items-start justify-between">
                                                <div class="flex-1">
                                                    <a href="{{ route('leader.card.show', ['project' => $project, 'board' => $cardData['board'], 'card' => $card]) }}" 
                                                       class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                                                        {{ $card->card_title }}
                                                    </a>
                                                    <p class="text-xs text-gray-500 mt-1">
                                                        <i class="fas fa-layer-group mr-1"></i>{{ $cardData['board']->board_name }}
                                                    </p>
                                                </div>
                                            </div>
                                            
                                            <!-- Card Priority & Status -->
                                            <div class="flex items-center space-x-2 mt-2">
                                                @php
                                                    $priorityColor = match($card->priority) {
                                                        'high' => 'bg-red-100 text-red-700',
                                                        'medium' => 'bg-yellow-100 text-yellow-700',
                                                        'low' => 'bg-green-100 text-green-700',
                                                        default => 'bg-gray-100 text-gray-700'
                                                    };
                                                    $statusColor = match($card->status) {
                                                        'done' => 'bg-green-100 text-green-700',
                                                        'in_progress' => 'bg-blue-100 text-blue-700',
                                                        'todo' => 'bg-gray-100 text-gray-700',
                                                        default => 'bg-gray-100 text-gray-700'
                                                    };
                                                @endphp
                                                
                                                <span class="px-2 py-0.5 rounded text-xs {{ $priorityColor }}">
                                                    {{ ucfirst($card->priority) }}
                                                </span>
                                                
                                                <span class="px-2 py-0.5 rounded text-xs {{ $statusColor }}">
                                                    {{ str_replace('_', ' ', ucfirst($card->status)) }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                
                                @php
                                    // Check if member has completed all cards
                                    $hasCompletedCard = collect($data['assigned_cards'])->contains(fn($cardData) => $cardData['card']->status === 'done');
                                @endphp
                                
                                @if($hasCompletedCard)
                                    <div class="mt-3 pt-3 border-t border-gray-200">
                                        <a href="{{ route('leader.card.create', ['project' => $project, 'board' => $project->boards->first()]) }}?assigned_user_id={{ $user->user_id }}" 
                                           class="block w-full text-center bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded font-semibold text-sm transition-colors">
                                            <i class="fas fa-plus-circle mr-1"></i>Add New Card
                                        </a>
                                    </div>
                                @endif
                            @else
                                <div class="mt-3 text-center py-4 bg-gray-50 rounded">
                                    <i class="fas fa-inbox text-gray-400 text-2xl mb-2"></i>
                                    <p class="text-sm text-gray-500">No cards assigned</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Back Button -->
    <div class="mt-8">
        <a href="{{ route('leader.project.details', $project) }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Back to Project
        </a>
    </div>
@endsection
