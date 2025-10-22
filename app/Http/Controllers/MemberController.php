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
        $assignedProjects = $user->projects()
            ->with(['project.creator'])
            ->get()
            ->pluck('project');
        
        return view('member.dashboard', compact('assignedProjects'));
    }

    public function projectDetails(Project $project)
    {
        // Check if the authenticated user is a member of this project
        $isMember = ProjectMember::where('project_id', $project->project_id)
            ->where('user_id', Auth::id())
            ->exists();

        if (!$isMember) {
            return redirect()->route('member.dashboard')
                ->with('error', 'You do not have access to this project.');
        }

        $project->load(['creator', 'members.user', 'boards.cards']);
        
        // Calculate total tasks (cards) and completed tasks
        $totalCards = 0;
        $completedCards = 0;
        
        foreach ($project->boards as $board) {
            $totalCards += $board->cards->count();
            $completedCards += $board->cards->where('status', 'done')->count();
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