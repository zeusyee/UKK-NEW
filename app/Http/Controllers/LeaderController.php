<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaderController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        
        // Get active projects where user is leader or admin
        $leadProjects = ProjectMember::where('user_id', $user->user_id)
            ->whereIn('role', ['admin', 'leader'])
            ->whereHas('project', function($query) {
                $query->where('status', 'active');
            })
            ->with([
                'project.creator', 
                'project.members',
                'project.boards.cards.subtasks'
            ])
            ->get()
            ->pluck('project');
        
        // Get completed projects history where user was leader or admin
        $completedProjects = ProjectMember::where('user_id', $user->user_id)
            ->whereIn('role', ['admin', 'leader'])
            ->whereHas('project', function($query) {
                $query->where('status', 'completed');
            })
            ->with([
                'project.creator',
                'project.completedBy',
                'project.members'
            ])
            ->get()
            ->pluck('project')
            ->sortByDesc('completed_at')
            ->take(5);
        
        // If no active projects, redirect to member dashboard
        if ($leadProjects->isEmpty()) {
            return redirect()->route('member.dashboard')
                ->with('info', 'You are not currently assigned as a leader to any active project.');
        }
        
        // Calculate statistics for each project
        $projectsWithStats = $leadProjects->map(function ($project) {
            $totalCards = 0;
            $completedCards = 0;
            $inProgressCards = 0;
            $todoCards = 0;
            $totalSubtasks = 0;
            $completedSubtasks = 0;
            $inProgressSubtasks = 0;
            $reviewSubtasks = 0;
            $assignedMembers = collect();
            
            foreach ($project->boards as $board) {
                foreach ($board->cards as $card) {
                    $totalCards++;
                    
                    // Count cards by status
                    if ($card->status === 'done') {
                        $completedCards++;
                    } elseif ($card->status === 'in_progress') {
                        $inProgressCards++;
                    } else {
                        $todoCards++;
                    }
                    
                    // Track assigned members
                    if ($card->assigned_user_id) {
                        $assignedMembers->push($card->assigned_user_id);
                    }
                    
                    // Count subtasks
                    foreach ($card->subtasks as $subtask) {
                        $totalSubtasks++;
                        
                        if ($subtask->status === 'done') {
                            $completedSubtasks++;
                        } elseif ($subtask->status === 'in_progress') {
                            $inProgressSubtasks++;
                        } elseif ($subtask->status === 'review') {
                            $reviewSubtasks++;
                        }
                    }
                }
            }
            
            // Calculate progress percentages
            $cardProgress = $totalCards > 0 ? round(($completedCards / $totalCards) * 100) : 0;
            $subtaskProgress = $totalSubtasks > 0 ? round(($completedSubtasks / $totalSubtasks) * 100) : 0;
            $overallProgress = $totalSubtasks > 0 ? $subtaskProgress : $cardProgress;
            
            // Determine project health status
            $health = 'good';
            if ($project->deadline) {
                $daysUntil = now()->diffInDays($project->deadline, false);
                if ($daysUntil < 0) {
                    $health = 'critical';
                } elseif ($daysUntil <= 7 && $overallProgress < 80) {
                    $health = 'warning';
                } elseif ($daysUntil <= 14 && $overallProgress < 50) {
                    $health = 'warning';
                }
            }
            
            return [
                'project' => $project,
                'stats' => [
                    'total_boards' => $project->boards->count(),
                    'total_cards' => $totalCards,
                    'completed_cards' => $completedCards,
                    'in_progress_cards' => $inProgressCards,
                    'todo_cards' => $todoCards,
                    'total_subtasks' => $totalSubtasks,
                    'completed_subtasks' => $completedSubtasks,
                    'in_progress_subtasks' => $inProgressSubtasks,
                    'review_subtasks' => $reviewSubtasks,
                    'card_progress' => $cardProgress,
                    'subtask_progress' => $subtaskProgress,
                    'overall_progress' => $overallProgress,
                    'total_members' => $project->members->count(),
                    'assigned_members' => $assignedMembers->unique()->count(),
                    'health' => $health
                ]
            ];
        });
        
        return view('leader.dashboard', compact('projectsWithStats', 'completedProjects'));
    }

    public function projectDetails(Project $project)
    {
        // Check if project is completed
        if ($project->status === 'completed') {
            return redirect()->route('leader.dashboard')
                ->with('error', 'This project has been completed and is now in history. You can no longer access it until you are assigned as a leader to a new active project.');
        }
        
        // Check if the authenticated user is a leader/admin of this project
        $member = ProjectMember::where('project_id', $project->project_id)
            ->where('user_id', Auth::id())
            ->whereIn('role', ['admin', 'leader'])
            ->first();

        if (!$member) {
            return redirect()->route('leader.dashboard')
                ->with('error', 'You do not have permission to manage this project.');
        }

$project->load([
    'creator', 
    'members.user', 
    'boards' => function($query) {
        $query->orderBy('position', 'asc');
    },
    'boards.cards' => function($query) {
        $query->orderBy('position', 'asc');
    },
    'boards.cards.assignedUser',
    'boards.cards.subtasks',
    'boards.cards.creator'
]);
        
        // Calculate project statistics
        $totalCards = 0;
        $completedCards = 0;
        $totalSubtasks = 0;
        $completedSubtasks = 0;
        
        foreach ($project->boards as $board) {
            if ($board->cards) {
                foreach ($board->cards as $card) {
                    $totalCards++;
                    if ($card->status === 'done') {
                        $completedCards++;
                    }
                    
                    if ($card->subtasks) {
                        $totalSubtasks += $card->subtasks->count();
                        $completedSubtasks += $card->subtasks->where('status', 'done')->count();
                    }
                }
            }
        }
        
        $projectStats = [
            'total_boards' => $project->boards->count(),
            'total_cards' => $totalCards,
            'completed_cards' => $completedCards,
            'total_subtasks' => $totalSubtasks,
            'completed_subtasks' => $completedSubtasks,
            'active_members' => $project->members->count(),
            'card_progress' => $totalCards > 0 ? round(($completedCards / $totalCards) * 100) : 0,
            'subtask_progress' => $totalSubtasks > 0 ? round(($completedSubtasks / $totalSubtasks) * 100) : 0
        ];

        return view('leader.project-details', compact('project', 'boards'));
    }

    public function history()
    {
        $userId = Auth::id();
        
        // Get completed projects where user was leader or admin
        $completedProjects = Project::where('status', 'completed')
            ->whereHas('members', function($query) use ($userId) {
                $query->where('user_id', $userId)
                      ->whereIn('role', ['leader', 'admin']);
            })
            ->with(['creator', 'completedBy', 'members'])
            ->orderBy('completed_at', 'desc')
            ->get();
        
        // Count projects by role - initialize with 0
        $asLeader = 0;
        $asAdmin = 0;
        
        foreach ($completedProjects as $project) {
            $userMember = $project->members->firstWhere('user_id', $userId);
            if ($userMember) {
                if ($userMember->role === 'leader') {
                    $asLeader++;
                }
                if ($userMember->role === 'admin') {
                    $asAdmin++;
                }
            }
        }
        
        return view('leader.history', compact(
            'completedProjects',
            'asLeader',
            'asAdmin'
        ));
    }

    /**
     * Show monitoring view with subtask progress details
     */
    public function monitoring(Project $project)
    {
        // Check if project is completed
        if ($project->status === 'completed') {
            return redirect()->route('leader.dashboard')
                ->with('error', 'This project has been completed. Monitoring is only available for active projects.');
        }
        
        // Check if the authenticated user is a leader/admin of this project
        $member = ProjectMember::where('project_id', $project->project_id)
            ->where('user_id', Auth::id())
            ->whereIn('role', ['admin', 'leader'])
            ->first();

        if (!$member) {
            return redirect()->route('leader.dashboard')
                ->with('error', 'You do not have permission to view this project.');
        }

        // Load project with all related data
        $project->load([
            'boards' => function($query) {
                $query->orderBy('position', 'asc');
            },
            'boards.cards' => function($query) {
                $query->orderBy('position', 'asc');
            },
            'boards.cards.assignedUser',
            'boards.cards.subtasks.assignedUser',
            'boards.cards.subtasks.creator',
            'boards.cards.creator'
        ]);

        // Calculate detailed statistics for each card
        $cardsWithProgress = [];
        foreach ($project->boards as $board) {
            foreach ($board->cards as $card) {
                $subtasksCount = $card->getSubtasksCountByStatus();
                $cardsWithProgress[] = [
                    'card' => $card,
                    'board' => $board,
                    'progress' => $card->getProgressPercentage(),
                    'subtasks_count' => $subtasksCount,
                    'total_subtasks' => $subtasksCount['total'],
                    'completed_subtasks' => $subtasksCount['done'],
                ];
            }
        }

        // Sort by progress (lowest first to highlight cards that need attention)
        usort($cardsWithProgress, function($a, $b) {
            return $a['progress'] <=> $b['progress'];
        });

        return view('leader.monitoring', compact('project', 'cardsWithProgress', 'member'));
    }
}