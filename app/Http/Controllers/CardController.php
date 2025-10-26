<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Board;
use App\Models\Project;
use App\Models\ProjectMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CardController extends Controller
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

    public function create(Project $project, Board $board)
    {
        $this->checkLeaderAccess($project->project_id);
        
        // Get all project members with role 'member'
        $projectMembers = $project->members()
            ->with('user')
            ->where('role', 'member')
            ->get();
        
        // Get IDs of members who are already assigned to cards in this project
        $assignedUserIds = Card::whereHas('board', function($query) use ($project) {
            $query->where('project_id', $project->project_id);
        })
        ->whereNotNull('assigned_user_id')
        ->pluck('assigned_user_id')
        ->toArray();
        
        return view('leader.cards.create', compact('project', 'board', 'projectMembers', 'assignedUserIds'));
    }

    public function store(Request $request, Project $project, Board $board)
    {
        $this->checkLeaderAccess($project->project_id);

        $validated = $request->validate([
            'card_title' => 'required|string|max:100',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'required|in:low,medium,high',
            'estimated_hours' => 'nullable|numeric|min:0|max:9999',
            'position' => 'nullable|integer|min:0',
            'assigned_user_id' => 'nullable|exists:users,user_id'
        ]);

        // Verify that assigned user is a member of the project
        if ($request->assigned_user_id) {
            $isMember = ProjectMember::where('project_id', $project->project_id)
                ->where('user_id', $request->assigned_user_id)
                ->where('role', 'member')
                ->exists();

            if (!$isMember) {
                return back()->with('error', 'The selected user is not a member of this project.');
            }

            // Check if member is already assigned to another card in this project
            $isAlreadyAssigned = Card::whereHas('board', function($query) use ($project) {
                $query->where('project_id', $project->project_id);
            })
            ->where('assigned_user_id', $request->assigned_user_id)
            ->exists();

            if ($isAlreadyAssigned) {
                return back()->with('error', 'This member is already assigned to another card in this project.');
            }
        }

        // Create the card
        $card = Card::create([
            'board_id' => $board->board_id,
            'card_title' => $validated['card_title'],
            'description' => $validated['description'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
            'status' => 'todo',
            'priority' => $validated['priority'],
            'estimated_hours' => $validated['estimated_hours'] ?? null,
            'position' => $validated['position'] ?? 0,
            'assigned_user_id' => $validated['assigned_user_id'] ?? null,
            'created_by' => Auth::id()
        ]);

        return redirect()
            ->route('leader.card.show', ['project' => $project, 'board' => $board, 'card' => $card])
            ->with('success', 'Card created successfully!');
    }

    public function show(Project $project, Board $board, Card $card)
    {
        $this->checkLeaderAccess($project->project_id);
        
        $card->load([
            'subtasks' => function($query) {
                $query->with('assignedUser', 'creator')->orderBy('position', 'asc');
            },
            'assignedUser',
            'creator', 
            'comments.user'
        ]);
        
        $projectMembers = $project->members()
            ->with('user')
            ->where('role', 'member')
            ->get();
        
        return view('leader.cards.show', compact('project', 'board', 'card', 'projectMembers'));
    }

    public function edit(Project $project, Board $board, Card $card)
    {
        $this->checkLeaderAccess($project->project_id);
        
        // Get all project members with role 'member'
        $projectMembers = $project->members()
            ->with('user')
            ->where('role', 'member')
            ->get();
        
        // Get IDs of members who are already assigned to other cards in this project
        // (excluding the current card being edited)
        $assignedUserIds = Card::whereHas('board', function($query) use ($project) {
            $query->where('project_id', $project->project_id);
        })
        ->where('card_id', '!=', $card->card_id)
        ->whereNotNull('assigned_user_id')
        ->pluck('assigned_user_id')
        ->toArray();
        
        return view('leader.cards.edit', compact('project', 'board', 'card', 'projectMembers', 'assignedUserIds'));
    }

    public function update(Request $request, Project $project, Board $board, Card $card)
    {
        $this->checkLeaderAccess($project->project_id);

        $validated = $request->validate([
            'card_title' => 'required|string|max:100',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'required|in:low,medium,high',
            'estimated_hours' => 'nullable|numeric|min:0|max:9999',
            'actual_hours' => 'nullable|numeric|min:0|max:9999',
            'assigned_user_id' => 'nullable|exists:users,user_id'
        ]);

        // Verify that assigned user is a member of the project
        if ($request->assigned_user_id) {
            $isMember = ProjectMember::where('project_id', $project->project_id)
                ->where('user_id', $request->assigned_user_id)
                ->where('role', 'member')
                ->exists();

            if (!$isMember) {
                return back()->with('error', 'The selected user is not a member of this project.');
            }

            // Check if member is already assigned to another card in this project
            // (excluding the current card being edited)
            $isAlreadyAssigned = Card::whereHas('board', function($query) use ($project) {
                $query->where('project_id', $project->project_id);
            })
            ->where('card_id', '!=', $card->card_id)
            ->where('assigned_user_id', $request->assigned_user_id)
            ->exists();

            if ($isAlreadyAssigned) {
                return back()->with('error', 'This member is already assigned to another card in this project.');
            }
        }

        // Update card data (status is auto-calculated from subtasks)
        $card->update([
            'card_title' => $validated['card_title'],
            'description' => $validated['description'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
            'priority' => $validated['priority'],
            'estimated_hours' => $validated['estimated_hours'] ?? null,
            'actual_hours' => $validated['actual_hours'] ?? null,
            'assigned_user_id' => $validated['assigned_user_id'] ?? null
        ]);

        return redirect()
            ->route('leader.card.show', ['project' => $project, 'board' => $board, 'card' => $card])
            ->with('success', 'Card updated successfully!');
    }

    public function destroy(Project $project, Board $board, Card $card)
    {
        $this->checkLeaderAccess($project->project_id);
        
        $card->delete();

        return redirect()
            ->route('leader.project.details', $project)
            ->with('success', 'Card deleted successfully!');
    }
}