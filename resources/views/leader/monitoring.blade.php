<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Subtasks - {{ $project->project_name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .progress-card {
            border-left: 4px solid;
            transition: all 0.3s ease;
        }
        .progress-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .progress-card.low-progress {
            border-left-color: #dc3545;
        }
        .progress-card.medium-progress {
            border-left-color: #ffc107;
        }
        .progress-card.high-progress {
            border-left-color: #28a745;
        }
        .progress-card.complete {
            border-left-color: #17a2b8;
        }
        .stat-badge {
            font-size: 0.85rem;
            padding: 0.35rem 0.75rem;
        }
        .progress {
            height: 25px;
        }
        .progress-bar {
            font-weight: 600;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    @extends('layouts.leader')

    @section('content')
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-1"><i class="bi bi-graph-up"></i> Monitoring Subtasks</h2>
                        <p class="text-muted mb-0">{{ $project->project_name }}</p>
                    </div>
                    <div>
                        <a href="{{ route('leader.project.details', $project) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali ke Project
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Statistics -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-primary">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-2">Total Cards</h6>
                        <h2 class="mb-0 text-primary">{{ count($cardsWithProgress) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-success">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-2">Cards Completed</h6>
                        <h2 class="mb-0 text-success">
                            {{ collect($cardsWithProgress)->where('progress', 100)->count() }}
                        </h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-warning">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-2">In Progress</h6>
                        <h2 class="mb-0 text-warning">
                            {{ collect($cardsWithProgress)->where('progress', '>', 0)->where('progress', '<', 100)->count() }}
                        </h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-danger">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-2">Not Started</h6>
                        <h2 class="mb-0 text-danger">
                            {{ collect($cardsWithProgress)->where('progress', 0)->count() }}
                        </h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cards List with Progress -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-list-task"></i> Daftar Cards dan Progress Subtasks</h5>
                    </div>
                    <div class="card-body">
                        @if(count($cardsWithProgress) > 0)
                            <div class="row g-3">
                                @foreach($cardsWithProgress as $item)
                                    @php
                                        $card = $item['card'];
                                        $board = $item['board'];
                                        $progress = $item['progress'];
                                        $subtasksCount = $item['subtasks_count'];
                                        
                                        // Determine card class based on progress
                                        if ($progress == 100) {
                                            $cardClass = 'complete';
                                            $progressColor = 'info';
                                        } elseif ($progress >= 50) {
                                            $cardClass = 'high-progress';
                                            $progressColor = 'success';
                                        } elseif ($progress > 0) {
                                            $cardClass = 'medium-progress';
                                            $progressColor = 'warning';
                                        } else {
                                            $cardClass = 'low-progress';
                                            $progressColor = 'danger';
                                        }
                                    @endphp
                                    
                                    @php
                                        // Check due date status
                                        $dueStatus = '';
                                        $dueBadgeClass = '';
                                        if ($card->due_date) {
                                            $daysUntil = now()->diffInDays($card->due_date, false);
                                            if ($daysUntil < 0) {
                                                $dueStatus = 'Overdue';
                                                $dueBadgeClass = 'bg-danger';
                                            } elseif ($daysUntil <= 3) {
                                                $dueStatus = 'Due Soon';
                                                $dueBadgeClass = 'bg-warning';
                                            }
                                        }
                                    @endphp
                                    
                                    <div class="col-md-6 col-lg-4">
                                        <div class="card progress-card {{ $cardClass }} h-100">
                                            <div class="card-body">
                                                <!-- Card Title and Status -->
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h6 class="card-title mb-0 flex-grow-1">{{ $card->card_title }}</h6>
                                                    <div class="d-flex gap-1 flex-wrap">
                                                        <span class="badge 
                                                            @if($card->priority == 'high') bg-danger
                                                            @elseif($card->priority == 'medium') bg-warning
                                                            @else bg-success
                                                            @endif
                                                        " title="Priority">
                                                            <i class="bi bi-flag-fill"></i>
                                                        </span>
                                                        @if($dueStatus)
                                                            <span class="badge {{ $dueBadgeClass }}" title="{{ $dueStatus }}">
                                                                <i class="bi bi-exclamation-triangle-fill"></i>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                
                                                <!-- Status Badge -->
                                                <div class="mb-2">
                                                    <span class="badge 
                                                        @if($card->status == 'done') bg-success
                                                        @elseif($card->status == 'in_progress') bg-primary
                                                        @else bg-secondary
                                                        @endif
                                                    ">
                                                        @if($card->status == 'done')
                                                            <i class="bi bi-check-circle-fill"></i> Done
                                                        @elseif($card->status == 'in_progress')
                                                            <i class="bi bi-arrow-repeat"></i> In Progress
                                                        @else
                                                            <i class="bi bi-circle"></i> To Do
                                                        @endif
                                                    </span>
                                                </div>

                                                <!-- Board Name -->
                                                <p class="text-muted small mb-2">
                                                    <i class="bi bi-columns"></i> {{ $board->board_title }}
                                                </p>

                                                <!-- Progress Bar -->
                                                <div class="progress mb-3">
                                                    <div class="progress-bar bg-{{ $progressColor }}" 
                                                         role="progressbar" 
                                                         style="width: {{ $progress }}%"
                                                         aria-valuenow="{{ $progress }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100">
                                                        {{ $progress }}%
                                                    </div>
                                                </div>

                                                <!-- Subtasks Statistics -->
                                                <div class="row g-2 mb-3">
                                                    <div class="col-6">
                                                        <small class="text-muted">Total Subtasks</small>
                                                        <div class="fw-bold">{{ $subtasksCount['total'] }}</div>
                                                    </div>
                                                    <div class="col-6">
                                                        <small class="text-muted">Completed</small>
                                                        <div class="fw-bold text-success">{{ $subtasksCount['done'] }}</div>
                                                    </div>
                                                </div>

                                                <!-- Subtasks by Status -->
                                                <div class="d-flex gap-2 mb-3 flex-wrap">
                                                    @if($subtasksCount['todo'] > 0)
                                                        <span class="badge bg-secondary stat-badge">
                                                            <i class="bi bi-circle"></i> Todo: {{ $subtasksCount['todo'] }}
                                                        </span>
                                                    @endif
                                                    @if($subtasksCount['in_progress'] > 0)
                                                        <span class="badge bg-warning stat-badge">
                                                            <i class="bi bi-play-circle"></i> In Progress: {{ $subtasksCount['in_progress'] }}
                                                        </span>
                                                    @endif
                                                    @if($subtasksCount['review'] > 0)
                                                        <span class="badge bg-info stat-badge">
                                                            <i class="bi bi-eye"></i> Review: {{ $subtasksCount['review'] }}
                                                        </span>
                                                    @endif
                                                </div>

                                                <!-- Assigned User -->
                                                @if($card->assignedUser)
                                                    <div class="mb-2">
                                                        <small class="text-muted">
                                                            <i class="bi bi-person"></i> 
                                                            Assigned to: {{ $card->assignedUser->name }}
                                                        </small>
                                                    </div>
                                                @endif

                                                <!-- Due Date -->
                                                @if($card->due_date)
                                                    <div class="mb-2">
                                                        <small class="text-muted">
                                                            <i class="bi bi-calendar"></i> 
                                                            Due: {{ $card->due_date->format('d M Y') }}
                                                        </small>
                                                    </div>
                                                @endif

                                                <!-- Action Button -->
                                                <div class="mt-3">
                                                    <a href="{{ route('leader.card.show', ['project' => $project, 'board' => $board, 'card' => $card]) }}" 
                                                       class="btn btn-sm btn-outline-primary w-100">
                                                        <i class="bi bi-eye"></i> Lihat Detail
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> Belum ada cards dalam project ini.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @endsection
</body>
</html>

