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
        
        // Get all cards assigned to the current user with subtasks loaded
        $myCards = Card::with(['board.project', 'creator', 'subtasks' => function($query) {
            $query->orderBy('position', 'asc');
        }])
            ->where('assigned_user_id', $user->user_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('member.my-tasks', compact('myCards'));
    }

    public function showTask(Project $project, Board $board, Card $card)
    {
        // Check if the user is assigned to this card
        if ($card->assigned_user_id !== Auth::id()) {
            return redirect()->route('member.my-tasks')
                ->with('error', 'You do not have access to this task.');
        }

        $card->load(['board', 'creator', 'subtasks' => function($query) {
            // Load subtasks created by the current user
            $query->where('created_by', Auth::id())->with('creator');
        }]);
        
        return view('member.task-detail', compact('project', 'board', 'card'));
    }
}