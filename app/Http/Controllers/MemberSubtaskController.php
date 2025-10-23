<?php

namespace App\Http\Controllers;

use App\Models\Subtask;
use App\Models\Card;
use App\Models\Board;
use App\Models\Project;
use App\Models\CardAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MemberSubtaskController extends Controller
{
    private function checkMemberAccess($cardId)
    {
        // Check if member is assigned to this card
        $assignment = CardAssignment::where('card_id', $cardId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$assignment) {
            abort(403, 'You are not assigned to this task.');
        }

        return $assignment;
    }

    public function store(Request $request, Project $project, Board $board, Card $card)
    {
        $this->checkMemberAccess($card->card_id);

        $request->validate([
            'subtask_title' => 'required|string|max:100',
            'description' => 'nullable|string',
            'estimated_hours' => 'nullable|numeric|min:0',
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

        return redirect()->route('member.task.show', ['project' => $project, 'board' => $board, 'card' => $card])
            ->with('success', 'Subtask created successfully.');
    }

    public function startSubtask(Request $request, Project $project, Board $board, Card $card, Subtask $subtask)
    {
        $this->checkMemberAccess($card->card_id);

        if ($subtask->created_by !== Auth::id()) {
            return back()->with('error', 'You can only start your own subtasks.');
        }

        if ($subtask->status !== 'todo') {
            return back()->with('error', 'Subtask must be in "To Do" status to start.');
        }

        $subtask->update(['status' => 'in_progress']);

        return back()->with('success', 'Subtask started successfully!');
    }

    public function submitSubtask(Request $request, Project $project, Board $board, Card $card, Subtask $subtask)
    {
        $this->checkMemberAccess($card->card_id);

        if ($subtask->created_by !== Auth::id()) {
            return back()->with('error', 'You can only submit your own subtasks.');
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
        $this->checkMemberAccess($card->card_id);

        if ($subtask->created_by !== Auth::id()) {
            return back()->with('error', 'You can only edit your own subtasks.');
        }

        if ($subtask->status === 'review' || $subtask->status === 'done') {
            return back()->with('error', 'Cannot edit subtask that is in review or completed.');
        }

        $request->validate([
            'subtask_title' => 'required|string|max:100',
            'description' => 'nullable|string',
            'estimated_hours' => 'nullable|numeric|min:0'
        ]);

        $subtask->update([
            'subtask_title' => $request->subtask_title,
            'description' => $request->description,
            'estimated_hours' => $request->estimated_hours
        ]);

        return back()->with('success', 'Subtask updated successfully.');
    }

    public function destroy(Project $project, Board $board, Card $card, Subtask $subtask)
    {
        $this->checkMemberAccess($card->card_id);

        if ($subtask->created_by !== Auth::id()) {
            return back()->with('error', 'You can only delete your own subtasks.');
        }

        if ($subtask->status === 'review' || $subtask->status === 'done') {
            return back()->with('error', 'Cannot delete subtask that is in review or completed.');
        }

        $subtask->delete();

        return back()->with('success', 'Subtask deleted successfully.');
    }
}