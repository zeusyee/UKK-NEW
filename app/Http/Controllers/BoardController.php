<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\Project;
use App\Models\ProjectMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BoardController extends Controller
{
    private function checkLeaderAccess($projectId)
    {
        $member = ProjectMember::where('project_id', $projectId)
            ->where('user_id', Auth::id())
            ->whereIn('role', ['admin', 'leader'])
            ->first();

        if (!$member) {
            abort(403, 'You do not have permission to manage this project.');
        }

        return $member;
    }

    public function create(Project $project)
    {
        $this->checkLeaderAccess($project->project_id);
        return view('leader.boards.create', compact('project'));
    }

    public function store(Request $request, Project $project)
    {
        $this->checkLeaderAccess($project->project_id);

        $request->validate([
            'board_name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'position' => 'nullable|integer|min:0'
        ]);

        Board::create([
            'project_id' => $project->project_id,
            'board_name' => $request->board_name,
            'description' => $request->description,
            'position' => $request->position ?? 0
        ]);

        return redirect()->route('leader.project.details', $project)
            ->with('success', 'Board created successfully.');
    }

    public function edit(Project $project, Board $board)
    {
        $this->checkLeaderAccess($project->project_id);
        return view('leader.boards.edit', compact('project', 'board'));
    }

    public function update(Request $request, Project $project, Board $board)
    {
        $this->checkLeaderAccess($project->project_id);

        $request->validate([
            'board_name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'position' => 'nullable|integer|min:0'
        ]);

        $board->update([
            'board_name' => $request->board_name,
            'description' => $request->description,
            'position' => $request->position ?? $board->position
        ]);

        return redirect()->route('leader.project.details', $project)
            ->with('success', 'Board updated successfully.');
    }

    public function destroy(Project $project, Board $board)
    {
        $this->checkLeaderAccess($project->project_id);
        $board->delete();

        return redirect()->route('leader.project.details', $project)
            ->with('success', 'Board deleted successfully.');
    }
}