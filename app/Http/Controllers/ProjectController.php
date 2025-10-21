<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with('creator')->get();
        return view('admin.projects.index', compact('projects'));
    }

    public function create()
    {
        return view('admin.projects.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'deadline' => 'nullable|date',
        ]);

        $project = Project::create([
            'project_name' => $request->project_name,
            'description' => $request->description,
            'deadline' => $request->deadline,
            'created_by' => Auth::id(),
        ]);

        // Add the creator as a super admin of the project
        ProjectMember::create([
            'project_id' => $project->project_id,
            'user_id' => Auth::id(),
            'role' => 'admin'
        ]);

        return redirect()->route('admin.projects.index')
            ->with('success', 'Project created successfully.');
    }

    public function edit(Project $project)
    {
        return view('admin.projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $request->validate([
            'project_name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'deadline' => 'nullable|date',
        ]);

        $project->update([
            'project_name' => $request->project_name,
            'description' => $request->description,
            'deadline' => $request->deadline,
        ]);

        return redirect()->route('admin.projects.index')
            ->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('admin.projects.index')
            ->with('success', 'Project deleted successfully.');
    }

    public function show(Project $project)
    {
        $project->load(['creator', 'members.user', 'boards']);
        return view('admin.projects.show', compact('project'));
    }

    public function members(Project $project)
    {
        $project->load(['members.user']);
        $availableUsers = \App\Models\User::whereNotIn('user_id', $project->members->pluck('user_id'))
            ->where('current_task_status', 'idle')
            ->get();
        return view('admin.projects.members', compact('project', 'availableUsers'));
    }

    public function addMember(Request $request, Project $project)
    {
        $request->validate([
            'user_id' => 'required|exists:users,user_id',
            'role' => 'required|in:admin,leader,member'
        ]);

        // Check if user is already in any project
        $user = \App\Models\User::find($request->user_id);
        
        if ($user->current_task_status === 'working') {
            return back()->with('error', 'User is already assigned to another project.');
        }

        $exists = ProjectMember::where('project_id', $project->project_id)
            ->where('user_id', $request->user_id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'User is already a member of this project.');
        }

        // Create project member
        ProjectMember::create([
            'project_id' => $project->project_id,
            'user_id' => $request->user_id,
            'role' => $request->role
        ]);

        // Update user status to working
        $user->update([
            'current_task_status' => 'working'
        ]);

        return back()->with('success', 'Member added successfully.');
    }

    public function removeMember(Project $project, ProjectMember $member)
    {
        if ($member->role === 'admin') {
            return back()->with('error', 'Cannot remove the project administrator.');
        }

        // Update user status back to idle
        $user = \App\Models\User::find($member->user_id);
        $user->update([
            'current_task_status' => 'idle'
        ]);

        $member->delete();
        return back()->with('success', 'Member removed successfully.');
    }
}