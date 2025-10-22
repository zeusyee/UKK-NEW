<?php

namespace App\Http\Controllers;

use App\Models\Subtask;
use App\Models\Card;
use App\Models\Board;
use App\Models\Project;
use App\Models\ProjectMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubtaskController extends Controller
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

    public function store(Request $request, Project $project, Board $board, Card $card)
    {
        $this->checkLeaderAccess($project->project_id);

        $request->validate([
            'subtask_title' => 'required|string|max:100',
            'description' => 'nullable|string',
            'status' => 'required|in:todo,in_progress,done',
            'estimated_hours' => 'nullable|numeric|min:0',
            'position' => 'nullable|integer|min:0'
        ]);

        Subtask::create([
            'card_id' => $card->card_id,
            'subtask_title' => $request->subtask_title,
            'description' => $request->description,
            'status' => $request->status,
            'estimated_hours' => $request->estimated_hours,
            'position' => $request->position ?? 0
        ]);

        return redirect()->route('leader.card.show', ['project' => $project, 'board' => $board, 'card' => $card])
            ->with('success', 'Subtask created successfully.');
    }

    public function update(Request $request, Project $project, Board $board, Card $card, Subtask $subtask)
    {
        $this->checkLeaderAccess($project->project_id);

        $request->validate([
            'subtask_title' => 'required|string|max:100',
            'description' => 'nullable|string',
            'status' => 'required|in:todo,in_progress,done',
            'estimated_hours' => 'nullable|numeric|min:0',
            'actual_hours' => 'nullable|numeric|min:0',
            'position' => 'nullable|integer|min:0'
        ]);

        $subtask->update([
            'subtask_title' => $request->subtask_title,
            'description' => $request->description,
            'status' => $request->status,
            'estimated_hours' => $request->estimated_hours,
            'actual_hours' => $request->actual_hours,
            'position' => $request->position ?? $subtask->position
        ]);

        return redirect()->route('leader.card.show', ['project' => $project, 'board' => $board, 'card' => $card])
            ->with('success', 'Subtask updated successfully.');
    }

    public function destroy(Project $project, Board $board, Card $card, Subtask $subtask)
    {
        $this->checkLeaderAccess($project->project_id);
        $subtask->delete();

        return redirect()->route('leader.card.show', ['project' => $project, 'board' => $board, 'card' => $card])
            ->with('success', 'Subtask deleted successfully.');
    }
}