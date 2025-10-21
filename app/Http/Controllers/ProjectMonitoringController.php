<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectMonitoringController extends Controller
{
    public function index()
    {
        $projects = Project::with(['creator', 'members.user'])
            ->withCount('members')
            ->get();

        $workingUsers = User::where('current_task_status', 'working')->count();
        $idleUsers = User::where('current_task_status', 'idle')->count();

        // Calculate project statistics
        $projectStats = [
            'total' => $projects->count(),
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

        // Get member distribution
        $memberDistribution = $projects->mapWithKeys(function($project) {
            return [$project->project_name => $project->members_count];
        });

        return view('admin.monitoring.index', compact(
            'projects', 
            'projectStats', 
            'workingUsers', 
            'idleUsers',
            'memberDistribution'
        ));
    }

    public function projectDetails($projectId)
    {
        $project = Project::with(['creator', 'members.user'])
            ->findOrFail($projectId);

        return view('admin.monitoring.project-details', compact('project'));
    }
}
