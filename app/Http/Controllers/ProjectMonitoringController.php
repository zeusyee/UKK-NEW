<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectMonitoringController extends Controller
{
    public function index()
    {
        $projects = Project::with(['creator', 'members.user', 'boards.cards.subtasks'])
            ->withCount('members')
            ->get();

        $workingUsers = User::where('current_task_status', 'working')->count();
        $idleUsers = User::where('current_task_status', 'idle')->count();

        // Calculate project statistics
        $projectStats = [
            'total' => $projects->count(),
            'active' => $projects->where('status', 'active')->count(),
            'completed' => $projects->where('status', 'completed')->count(),
            'with_deadline' => $projects->filter(function($project) {
                return !is_null($project->deadline);
            })->count(),
            'no_deadline' => $projects->filter(function($project) {
                return is_null($project->deadline);
            })->count(),
            'deadline_approaching' => $projects->filter(function($project) {
                if (!$project->deadline) return false;
                $daysUntilDeadline = now()->diffInDays($project->deadline, false);
                return $daysUntilDeadline >= 0 && $daysUntilDeadline <= 7;
            })->count(),
            'overdue' => $projects->filter(function($project) {
                if (!$project->deadline) return false;
                return now()->isAfter($project->deadline);
            })->count()
        ];

        // Calculate project progress
        $projectProgress = $projects->map(function($project) {
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
                    
                    foreach ($card->subtasks as $subtask) {
                        $totalSubtasks++;
                        if ($subtask->status === 'done') {
                            $completedSubtasks++;
                        }
                    }
                }
            }
            
            // Calculate overall progress
            $overallProgress = 0;
            if ($totalSubtasks > 0) {
                $overallProgress = round(($completedSubtasks / $totalSubtasks) * 100);
            } elseif ($totalCards > 0) {
                $overallProgress = round(($completedCards / $totalCards) * 100);
            }
            
            return [
                'project_name' => $project->project_name,
                'progress' => $overallProgress,
                'status' => $project->status,
                'total_cards' => $totalCards,
                'completed_cards' => $completedCards,
                'total_subtasks' => $totalSubtasks,
                'completed_subtasks' => $completedSubtasks
            ];
        })->sortByDesc('progress');

        return view('admin.monitoring.index', compact(
            'projects', 
            'projectStats', 
            'workingUsers', 
            'idleUsers',
            'projectProgress'
        ));
    }

    public function projectDetails($projectId)
    {
        $project = Project::with(['creator', 'members.user'])
            ->findOrFail($projectId);

        return view('admin.monitoring.project-details', compact('project'));
    }
}
