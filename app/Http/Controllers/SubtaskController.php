<?php

namespace App\Http\Controllers;

use App\Models\Subtask;
use App\Models\Card;
use App\Models\Board;
use App\Models\Project;
use App\Models\ProjectMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubtaskController extends Controller
{
    /**
     * Check if user is a leader/admin of the project
     */
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

    /**
     * Check if user is a member assigned to the card
     */
    private function checkMemberAccess($cardId)
    {
        $card = Card::where('card_id', $cardId)
            ->where('assigned_user_id', Auth::id())
            ->first();

        if (!$card) {
            abort(403, 'You are not assigned to this task.');
        }

        return $card;
    }

    /**
     * Store a new subtask (Member only)
     */
    public function storeMember(Request $request, Project $project, Board $board, Card $card)
    {
        $this->checkMemberAccess($card->card_id);

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

    /**
     * Start a subtask (Member only)
     */
    public function startSubtask(Request $request, Project $project, Board $board, Card $card, Subtask $subtask)
    {
        $this->checkMemberAccess($card->card_id);

        // Check if the member is the creator of this subtask
        if ($subtask->created_by !== Auth::id()) {
            return back()->with('error', 'You can only start your own subtasks.');
        }

        if ($subtask->status !== 'todo') {
            return back()->with('error', 'Subtask must be in "To Do" status to start.');
        }

        $subtask->update(['status' => 'in_progress']);

        return back()->with('success', 'Subtask started successfully!');
    }

    /**
     * Submit subtask for review (Member only)
     */
    public function submitSubtask(Request $request, Project $project, Board $board, Card $card, Subtask $subtask)
    {
        $this->checkMemberAccess($card->card_id);

        // Check if the member is the creator of this subtask
        if ($subtask->created_by !== Auth::id()) {
            return back()->with('error', 'You can only submit your own subtasks.');
        }

        if ($subtask->status !== 'in_progress') {
            return back()->with('error', 'Subtask must be in progress to submit for review.');
        }

        $request->validate([
            'actual_hours' => 'nullable|numeric|min:0|max:9999',
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

    /**
     * Update a subtask (Member only)
     */
    public function updateMember(Request $request, Project $project, Board $board, Card $card, Subtask $subtask)
    {
        $this->checkMemberAccess($card->card_id);

        // Check if the member is the creator of this subtask
        if ($subtask->created_by !== Auth::id()) {
            return back()->with('error', 'You can only edit your own subtasks.');
        }

        // Cannot edit if in review or done
        if ($subtask->status === 'review' || $subtask->status === 'done') {
            return back()->with('error', 'Cannot edit subtask that is in review or completed.');
        }

        $request->validate([
            'subtask_title' => 'required|string|max:100',
            'description' => 'nullable|string',
            'estimated_hours' => 'nullable|numeric|min:0|max:9999'
        ]);

        $subtask->update([
            'subtask_title' => $request->subtask_title,
            'description' => $request->description,
            'estimated_hours' => $request->estimated_hours
        ]);

        return back()->with('success', 'Subtask updated successfully.');
    }

    /**
     * Delete a subtask (Member only)
     */
    public function destroyMember(Project $project, Board $board, Card $card, Subtask $subtask)
    {
        $this->checkMemberAccess($card->card_id);

        // Check if the member is the creator of this subtask
        if ($subtask->created_by !== Auth::id()) {
            return back()->with('error', 'You can only delete your own subtasks.');
        }

        // Cannot delete if in review or done
        if ($subtask->status === 'review' || $subtask->status === 'done') {
            return back()->with('error', 'Cannot delete subtask that is in review or completed.');
        }

        $subtask->delete();

        return back()->with('success', 'Subtask deleted successfully.');
    }

    /**
     * Get all subtasks pending review (Leader only)
     */
    public function reviewIndex()
    {
        $user = Auth::user();
        
        // Get all projects where user is leader or admin
        $projectIds = ProjectMember::where('user_id', $user->user_id)
            ->whereIn('role', ['admin', 'leader'])
            ->pluck('project_id');

        // Get all subtasks in review status from those projects
        $subtasksInReview = Subtask::with(['card.board.project', 'creator'])
            ->whereHas('card.board.project', function($query) use ($projectIds) {
                $query->whereIn('project_id', $projectIds);
            })
            ->where('status', 'review')
            ->orderBy('updated_at', 'desc')
            ->get();
        
        return view('leader.review.index', compact('subtasksInReview'));
    }

    /**
     * Show subtask review detail (Leader only)
     */
    public function reviewShow(Project $project, Board $board, Card $card, Subtask $subtask)
    {
        $this->checkLeaderAccess($project->project_id);

        if ($subtask->status !== 'review') {
            return redirect()->route('leader.review.index')
                ->with('error', 'This subtask is not in review status.');
        }

        $subtask->load(['card', 'creator']);
        
        return view('leader.review.show', compact('project', 'board', 'card', 'subtask'));
    }

    /**
     * Approve subtask (Leader only)
     */
    public function approve(Request $request, Project $project, Board $board, Card $card, Subtask $subtask)
    {
        $this->checkLeaderAccess($project->project_id);

        $request->validate([
            'leader_notes' => 'nullable|string'
        ]);

        if ($subtask->status !== 'review') {
            return back()->with('error', 'This subtask is not in review status.');
        }

        DB::beginTransaction();
        try {
            // Update subtask status to done
            $subtask->update([
                'status' => 'done',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
                'review_notes' => $request->leader_notes
            ]);

            // Check if all subtasks are done, update card status
            $allSubtasksDone = $card->subtasks()
                ->where('status', '!=', 'done')
                ->count() === 0;

            if ($allSubtasksDone && $card->subtasks()->count() > 0) {
                $card->update(['status' => 'done']);
            }

            DB::commit();
            
            return redirect()->route('leader.review.index')
                ->with('success', 'Subtask approved successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to approve subtask: ' . $e->getMessage());
        }
    }

    /**
     * Reject subtask (Leader only)
     */
    public function reject(Request $request, Project $project, Board $board, Card $card, Subtask $subtask)
    {
        $this->checkLeaderAccess($project->project_id);

        $request->validate([
            'rejection_reason' => 'required|string'
        ]);

        if ($subtask->status !== 'review') {
            return back()->with('error', 'This subtask is not in review status.');
        }

        DB::beginTransaction();
        try {
            // Update subtask status back to in_progress
            $subtask->update([
                'status' => 'in_progress',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
                'review_notes' => $request->rejection_reason
            ]);

            DB::commit();
            
            return redirect()->route('leader.review.index')
                ->with('success', 'Subtask returned for revision.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to reject subtask: ' . $e->getMessage());
        }
    }

    /**
     * Delete a subtask (Leader only - for management purposes)
     */
    public function destroyLeader(Project $project, Board $board, Card $card, Subtask $subtask)
    {
        $this->checkLeaderAccess($project->project_id);
        
        // Only allow deletion if subtask is in todo or in_progress status
        if ($subtask->status === 'review' || $subtask->status === 'done') {
            return back()->with('error', 'Cannot delete subtask that is in review or completed.');
        }
        
        $subtask->delete();

        return redirect()->route('leader.card.show', [
            'project' => $project,
            'board' => $board,
            'card' => $card
        ])->with('success', 'Subtask deleted successfully.');
    }
}