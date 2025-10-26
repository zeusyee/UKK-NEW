<?php

namespace App\Http\Controllers;

use App\Models\Subtask;
use App\Models\Card;
use App\Models\Board;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MemberSubtaskController extends Controller
{
    private function checkMemberAccess($projectId, $card = null)
    {
        // Check if member is part of the project
        $member = \App\Models\ProjectMember::where('project_id', $projectId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$member) {
            abort(403, 'You are not a member of this project.');
        }

        // If card is provided, check if user is assigned to the card
        if ($card && $card->assigned_user_id !== Auth::id()) {
            abort(403, 'You are not assigned to this card.');
        }

        return $member;
    }

    /**
     * Store a new subtask (Member creates their own subtask)
     */
    public function store(Request $request, Project $project, Board $board, Card $card)
    {
        $this->checkMemberAccess($project->project_id, $card);

        $request->validate([
            'subtask_title' => 'required|string|max:100',
            'description' => 'nullable|string',
            'estimated_hours' => 'nullable|numeric|min:0|max:9999',
            'position' => 'nullable|integer|min:0'
        ]);

        Subtask::create([
            'card_id' => $card->card_id,
            'subtask_title' => $request->subtask_title,
            'description' => $request->description,
            'status' => 'todo',
            'estimated_hours' => $request->estimated_hours,
            'position' => $request->position ?? 0,
            'created_by' => Auth::id()
        ]);

        return redirect()->route('member.task.show', [
            'project' => $project,
            'board' => $board,
            'card' => $card
        ])->with('success', 'Subtask created successfully.');
    }

    public function startSubtask(Request $request, Project $project, Board $board, Card $card, Subtask $subtask)
    {
        $this->checkMemberAccess($project->project_id, $card);

        // Check if member created this subtask
        if ($subtask->created_by !== Auth::id()) {
            return back()->with('error', 'You can only start subtasks you created.');
        }

        if ($subtask->status !== 'todo') {
            return back()->with('error', 'Subtask must be in "To Do" status to start.');
        }

        DB::beginTransaction();
        try {
            // Use the model method to start the subtask (includes validation)
            $subtask->startSubtask(Auth::id());
            
            DB::commit();
            return back()->with('success', 'Subtask started successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function submitSubtask(Request $request, Project $project, Board $board, Card $card, Subtask $subtask)
    {
        $this->checkMemberAccess($project->project_id, $card);

        // Check if member created this subtask
        if ($subtask->created_by !== Auth::id()) {
            return back()->with('error', 'You can only submit subtasks you created.');
        }

        if ($subtask->status !== 'in_progress') {
            return back()->with('error', 'Subtask must be in progress to submit for review.');
        }

        $request->validate([
            'actual_hours' => 'nullable|numeric|min:0',
            'completion_notes' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $subtask->update([
                'status' => 'review',
                'actual_hours' => $request->actual_hours ?? $subtask->actual_hours,
                'completion_notes' => $request->completion_notes
            ]);

            DB::commit();
            
            return back()->with('success', 'Subtask submitted for review successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to submit subtask: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Project $project, Board $board, Card $card, Subtask $subtask)
    {
        $this->checkMemberAccess($project->project_id, $card);

        // Check if member created this subtask
        if ($subtask->created_by !== Auth::id()) {
            return back()->with('error', 'You can only edit subtasks you created.');
        }

        if ($subtask->status === 'review' || $subtask->status === 'done') {
            return back()->with('error', 'Cannot edit subtask that is in review or completed.');
        }

        $request->validate([
            'completion_notes' => 'nullable|string',
            'actual_hours' => 'nullable|numeric|min:0'
        ]);

        $subtask->update([
            'completion_notes' => $request->completion_notes,
            'actual_hours' => $request->actual_hours ?? $subtask->actual_hours
        ]);

        return back()->with('success', 'Subtask updated successfully.');
    }

    /**
     * Pause a subtask (Member can pause their own subtask)
     */
    public function pauseSubtask(Project $project, Board $board, Card $card, Subtask $subtask)
    {
        $this->checkMemberAccess($project->project_id, $card);

        // Check if member created this subtask
        if ($subtask->created_by !== Auth::id()) {
            return back()->with('error', 'You can only pause subtasks you created.');
        }

        if ($subtask->status !== 'in_progress') {
            return back()->with('error', 'Only in-progress subtasks can be paused.');
        }

        if ($subtask->paused_at) {
            return back()->with('error', 'Subtask is already paused.');
        }

        $subtask->update([
            'paused_at' => now()
        ]);

        return back()->with('success', 'Subtask paused successfully.');
    }

    /**
     * Resume a subtask (Member can resume their own subtask)
     */
    public function resumeSubtask(Project $project, Board $board, Card $card, Subtask $subtask)
    {
        $this->checkMemberAccess($project->project_id, $card);

        // Check if member created this subtask
        if ($subtask->created_by !== Auth::id()) {
            return back()->with('error', 'You can only resume subtasks you created.');
        }

        if ($subtask->status !== 'in_progress') {
            return back()->with('error', 'Only in-progress subtasks can be resumed.');
        }

        if (!$subtask->paused_at) {
            return back()->with('error', 'Subtask is not paused.');
        }

        // Calculate pause duration and add to total
        $pauseDuration = now()->diffInSeconds($subtask->paused_at);
        
        $subtask->update([
            'paused_at' => null,
            'total_paused_seconds' => $subtask->total_paused_seconds + $pauseDuration
        ]);

        return back()->with('success', 'Subtask resumed successfully.');
    }

    /**
     * Delete a subtask (Member can delete their own subtasks)
     */
    public function destroy(Project $project, Board $board, Card $card, Subtask $subtask)
    {
        $this->checkMemberAccess($project->project_id, $card);

        // Check if member created this subtask
        if ($subtask->created_by !== Auth::id()) {
            return back()->with('error', 'You can only delete subtasks you created.');
        }

        if ($subtask->status === 'review' || $subtask->status === 'done') {
            return back()->with('error', 'Cannot delete subtask that is in review or completed.');
        }

        $subtask->delete();

        return back()->with('success', 'Subtask deleted successfully.');
    }
}