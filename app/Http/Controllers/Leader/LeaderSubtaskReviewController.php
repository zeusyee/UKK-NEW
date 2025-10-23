<?php

namespace App\Http\Controllers\Leader;

use App\Http\Controllers\Controller;
use App\Models\Subtask;
use App\Models\Card;
use App\Models\Board;
use App\Models\Project;
use App\Models\ProjectMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LeaderSubtaskReviewController extends Controller
{
    private function checkLeaderAccess($projectId)
    {
        $member = ProjectMember::where('project_id', $projectId)
            ->where('user_id', Auth::id())
            ->whereIn('role', ['admin', 'leader'])
            ->first();

        if (!$member) {
            abort(403, 'You do not have permission to review this project.');
        }

        return $member;
    }

    public function index()
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

    public function show(Project $project, Board $board, Card $card, Subtask $subtask)
    {
        $this->checkLeaderAccess($project->project_id);

        if ($subtask->status !== 'review') {
            return redirect()->route('leader.review.index')
                ->with('error', 'This subtask is not in review status.');
        }

        $subtask->load(['card', 'creator']);
        
        return view('leader.review.show', compact('project', 'board', 'card', 'subtask'));
    }

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
}