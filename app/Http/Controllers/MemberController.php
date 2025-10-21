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

        $project->load(['creator', 'members.user', 'boards']);
        
        $projectStats = [
            'total_boards' => $project->boards->count(),
            'active_members' => $project->members->count(),
            'progress_percentage' => 0 // You'll need to calculate this based on your board/card structure
        ];

        return view('member.project-details', compact('project', 'projectStats'));
    }
}
