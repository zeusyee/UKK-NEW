<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\User;

class ProjectMonitoringController extends Controller
{
    public function index()
    {
        $projects = Project::with(['members', 'leader'])->get();
        
        $statistics = [
            'total_projects' => $projects->count(),
            'ongoing_projects' => $projects->where('status', 'ongoing')->count(),
            'completed_projects' => $projects->where('status', 'completed')->count(),
            'total_members' => User::where('role', 'member')->count(),
            'active_members' => User::where('role', 'member')->where('status', 'active')->count()
        ];

        return view('admin.monitoring.index', compact('projects', 'statistics'));
    }

    public function show(Project $project)
    {
        $project->load(['members', 'leader', 'tasks']);
        
        $projectStats = [
            'total_tasks' => $project->tasks->count(),
            'completed_tasks' => $project->tasks->where('status', 'completed')->count(),
            'active_members' => $project->members->where('status', 'active')->count(),
            'progress_percentage' => $project->tasks->count() > 0 
                ? round(($project->tasks->where('status', 'completed')->count() / $project->tasks->count()) * 100)
                : 0
        ];

        return view('admin.monitoring.show', compact('project', 'projectStats'));
    }
}
