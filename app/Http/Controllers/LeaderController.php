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
        
        // Get projects where user is leader or admin
        $leadProjects = ProjectMember::where('user_id', $user->user_id)
            ->whereIn('role', ['admin', 'leader'])
            ->with(['project.creator', 'project.members'])
            ->get()
            ->pluck('project');
        
        return view('leader.dashboard', compact('leadProjects'));
    }

    public function projectDetails(Project $project)
    {
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
            'boards.cards.subtasks',
            'boards.cards.assignments.user',
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

        return view('leader.project-details', compact('project', 'projectStats', 'member'));
    }
}