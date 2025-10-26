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
        
        // Get all projects where user is a member
        $projectIds = ProjectMember::where('user_id', $user->user_id)
            ->pluck('project_id');
        
        $assignedProjects = Project::whereIn('project_id', $projectIds)
            ->with(['creator'])
            ->get();
        
        return view('member.dashboard', compact('assignedProjects'));
    }

    public function projectDetails(Project $project)
    {
        $user = Auth::user();
        
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