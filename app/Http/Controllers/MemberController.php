<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Models\ProjectMember;

class MemberController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        
        // Get active projects where user is a member
        $projectIds = ProjectMember::where('user_id', $user->user_id)
            ->whereHas('project', function($query) {
                $query->where('status', 'active');
            })
            ->pluck('project_id');
        
        $activeProjects = Project::whereIn('project_id', $projectIds)
            ->where('status', 'active')
            ->with(['creator'])
            ->get();
        
        // Get completed projects history
        $completedProjectIds = ProjectMember::where('user_id', $user->user_id)
            ->whereHas('project', function($query) {
                $query->where('status', 'completed');
            })
            ->pluck('project_id');
        
        $completedProjects = Project::whereIn('project_id', $completedProjectIds)
            ->where('status', 'completed')
            ->with(['creator', 'completedBy'])
            ->orderBy('completed_at', 'desc')
            ->take(5)
            ->get();
        
        return view('member.dashboard', compact('activeProjects', 'completedProjects'));
    }

    public function history()
    {
        $userId = Auth::id();
        
        // Get completed projects where user was a member
        $completedProjects = Project::where('status', 'completed')
            ->whereHas('members', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->with(['creator', 'completedBy', 'members' => function($query) use ($userId) {
                $query->where('user_id', $userId);
            }])
            ->orderBy('completed_at', 'desc')
            ->get();
        
        // Calculate task statistics
        $totalTasks = 0;
        $completedTasks = 0;
        
        foreach ($completedProjects as $project) {
            $userMember = $project->members->first();
            if ($userMember) {
                $boards = $project->boards;
                foreach ($boards as $board) {
                    $cards = $board->cards;
                    foreach ($cards as $card) {
                        $subtasks = $card->subtasks()->where('assigned_user_id', $userId)->get();
                        $totalTasks += $subtasks->count();
                        $completedTasks += $subtasks->where('is_complete', true)->count();
                    }
                }
            }
        }
        
        return view('member.history', compact(
            'completedProjects',
            'totalTasks',
            'completedTasks'
        ));
    }

    public function projectDetails(Project $project)
    {
        $user = Auth::user();
        
        // Check if project is completed
        if ($project->status === 'completed') {
            return redirect()->route('member.dashboard')
                ->with('error', 'This project has been completed and is now in history.');
        }
        
        // Check if the user is a member of this project
        $isMember = ProjectMember::where('project_id', $project->project_id)
            ->where('user_id', $user->user_id)
            ->exists();

        if (!$isMember) {
            return redirect()->route('member.dashboard')
                ->with('error', 'You are not a member of this project.');
        }

        // Load project with boards and cards assigned to the user
        $project->load([
            'creator', 
            'members.user', 
            'boards.cards' => function($query) use ($user) {
                $query->where('assigned_user_id', $user->user_id)
                      ->with(['assignedUser', 'subtasks']);
            }
        ]);
        
        // Calculate total cards and completed cards for this user
        $totalCards = 0;
        $completedCards = 0;
        $totalSubtasks = 0;
        $completedSubtasks = 0;
        
        foreach ($project->boards as $board) {
            foreach ($board->cards as $card) {
                $totalCards++;
                if ($card->status === 'done') {
                    $completedCards++;
                }
                $totalSubtasks += $card->subtasks->count();
                $completedSubtasks += $card->subtasks->where('status', 'done')->count();
            }
        }
        
        $projectStats = [
            'total_cards' => $totalCards,
            'completed_cards' => $completedCards,
            'total_subtasks' => $totalSubtasks,
            'completed_subtasks' => $completedSubtasks,
            'active_members' => $project->members->count(),
            'card_progress' => $totalCards > 0 
                ? round(($completedCards / $totalCards) * 100) 
                : 0,
            'subtask_progress' => $totalSubtasks > 0 
                ? round(($completedSubtasks / $totalSubtasks) * 100) 
                : 0
        ];

        return view('member.project-details', compact('project', 'projectStats'));
    }
}