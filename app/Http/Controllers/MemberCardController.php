<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\CardAssignment;
use App\Models\Project;
use App\Models\Board;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MemberCardController extends Controller
{
    public function myTasks()
    {
        $user = Auth::user();
        
        // Get all card assignments for the current user
        $assignments = CardAssignment::with(['card.board.project', 'card.creator'])
            ->where('user_id', $user->user_id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('member.my-tasks', compact('assignments'));
    }

    public function showTask(Project $project, Board $board, Card $card)
    {
        // Check if user is assigned to this card
        $assignment = CardAssignment::where('card_id', $card->card_id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$assignment) {
            return redirect()->route('member.my-tasks')
                ->with('error', 'You are not assigned to this task.');
        }

        $card->load(['board', 'creator', 'subtasks', 'assignments.user', 'reviewer']);
        
        return view('member.task-detail', compact('project', 'board', 'card', 'assignment'));
    }

    public function startTask(Project $project, Board $board, Card $card)
    {
        // Check if user is assigned to this card
        $assignment = CardAssignment::where('card_id', $card->card_id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$assignment) {
            return back()->with('error', 'You are not assigned to this task.');
        }

        DB::beginTransaction();
        try {
            // Update assignment status
            $assignment->update([
                'assignment_status' => 'in_progress',
                'started_at' => now()
            ]);

            // Update card status
            $card->update([
                'status' => 'in_progress'
            ]);

            DB::commit();
            
            return back()->with('success', 'Task started successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to start task: ' . $e->getMessage());
        }
    }

    public function submitTask(Request $request, Project $project, Board $board, Card $card)
    {
        $request->validate([
            'actual_hours' => 'nullable|numeric|min:0',
            'completion_notes' => 'nullable|string'
        ]);

        // Check if user is assigned to this card
        $assignment = CardAssignment::where('card_id', $card->card_id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$assignment) {
            return back()->with('error', 'You are not assigned to this task.');
        }

        if ($assignment->assignment_status !== 'in_progress') {
            return back()->with('error', 'Task must be in progress before submitting.');
        }

        DB::beginTransaction();
        try {
            // Update assignment status
            $assignment->update([
                'assignment_status' => 'completed',
                'completed_at' => now()
            ]);

            // Update card status and actual hours
            $card->update([
                'status' => 'review',
                'actual_hours' => $request->actual_hours ?? $card->actual_hours,
                'review_notes' => $request->completion_notes
            ]);

            DB::commit();
            
            return back()->with('success', 'Task submitted for review successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to submit task: ' . $e->getMessage());
        }
    }
}