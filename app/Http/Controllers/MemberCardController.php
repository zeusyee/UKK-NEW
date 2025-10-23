<?php

namespace App\Http\Controllers;

use App\Models\Card;
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
        
        // Get all cards assigned to the current user only
        $myCards = Card::with(['board.project', 'creator'])
            ->where('assigned_user_id', $user->user_id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('member.my-tasks', compact('myCards'));
    }

    public function showTask(Project $project, Board $board, Card $card)
    {
        // Check if the card is assigned to the current user
        if ($card->assigned_user_id !== Auth::id()) {
            return redirect()->route('member.my-tasks')
                ->with('error', 'You do not have access to this task.');
        }

        $card->load(['board', 'creator', 'subtasks' => function($query) {
            // Only load subtasks created by the current user
            $query->where('created_by', Auth::id());
        }, 'assignedUser']);
        
        return view('member.task-detail', compact('project', 'board', 'card'));
    }

    public function startTask(Project $project, Board $board, Card $card)
    {
        // Check if the card is assigned to the current user
        if ($card->assigned_user_id !== Auth::id()) {
            return back()->with('error', 'You do not have access to this task.');
        }

        if ($card->assignment_status !== 'not_assigned') {
            return back()->with('error', 'Task has already been started.');
        }

        DB::beginTransaction();
        try {
            // Update card status
            $card->update([
                'assignment_status' => 'in_progress',
                'started_at' => now(),
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
        // Check if the card is assigned to the current user
        if ($card->assigned_user_id !== Auth::id()) {
            return back()->with('error', 'You do not have access to this task.');
        }

        if ($card->assignment_status !== 'in_progress') {
            return back()->with('error', 'Task must be in progress before submitting.');
        }

        $request->validate([
            'actual_hours' => 'nullable|numeric|min:0',
            'completion_notes' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            // Update card status
            $card->update([
                'assignment_status' => 'completed',
                'completed_at' => now(),
                'status' => 'done',
                'actual_hours' => $request->actual_hours ?? $card->actual_hours
            ]);

            DB::commit();
            
            return back()->with('success', 'Task completed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to complete task: ' . $e->getMessage());
        }
    }
}