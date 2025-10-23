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
        
        // Get projects where user has at least one assigned card
        $projectIds = \App\Models\Card::where('assigned_user_id', $user->user_id)
            ->with('board.project')
            ->get()
            ->pluck('board.project_id')
            ->unique();
        
        $assignedProjects = Project::whereIn('project_id', $projectIds)
            ->with(['creator'])
            ->get();
        
        return view('member.dashboard', compact('assignedProjects'));
    }

    public function projectDetails(Project $project)
    {
        $user = Auth::user();
        
        // Check if the user has any assigned cards in this project
        $hasAssignedCards = \App\Models\Card::whereHas('board', function($query) use ($project) {
            $query->where('project_id', $project->project_id);
        })
        ->where('assigned_user_id', $user->user_id)
        ->exists();

        if (!$hasAssignedCards) {
            return redirect()->route('member.dashboard')
                ->with('error', 'You do not have access to this project.');
        }

        $project->load(['creator', 'members.user', 'boards.cards' => function($query) use ($user) {
            // Only load cards assigned to the current user
            $query->where('assigned_user_id', $user->user_id);
        }]);
        
        // Calculate total tasks (only user's cards) and completed tasks
        $totalCards = 0;
        $completedCards = 0;
        
        foreach ($project->boards as $board) {
            $userCards = $board->cards->where('assigned_user_id', $user->user_id);
            $totalCards += $userCards->count();
            $completedCards += $userCards->where('status', 'done')->count();
        }
        
        $projectStats = [
            'total_boards' => $project->boards->count(),
            'total_tasks' => $totalCards,
            'completed_tasks' => $completedCards,
            'active_members' => $project->members->count(),
            'progress_percentage' => $totalCards > 0 
                ? round(($completedCards / $totalCards) * 100) 
                : 0
        ];

        return view('member.project-details', compact('project', 'projectStats'));
    }
}