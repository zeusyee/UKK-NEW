<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Board;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\CardAssignment;
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
        
        // Get all project members
        $projectMembers = $project->members()->with('user')->get();
        
        return view('leader.cards.create', compact('project', 'board', 'projectMembers'));
    }

    public function store(Request $request, Project $project, Board $board)
    {
        $this->checkLeaderAccess($project->project_id);

        $validated = $request->validate([
            'card_title' => 'required|string|max:100',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'status' => 'nullable|in:todo,in_progress,review,done',
            'priority' => 'required|in:low,medium,high',
            'estimated_hours' => 'nullable|numeric|min:0|max:9999',
            'position' => 'nullable|integer|min:0',
            'assigned_users' => 'nullable|array',
            'assigned_users.*' => 'exists:users,user_id'
        ]);

        // Create the card
        $card = Card::create([
            'board_id' => $board->board_id,
            'card_title' => $validated['card_title'],
            'description' => $validated['description'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
            'status' => $validated['status'] ?? null,
            'priority' => $validated['priority'],
            'estimated_hours' => $validated['estimated_hours'] ?? null,
            'position' => $validated['position'] ?? 0,
            'created_by' => Auth::id()
        ]);

        // Assign users to card if any
        if (!empty($validated['assigned_users'])) {
            foreach ($validated['assigned_users'] as $userId) {
                CardAssignment::create([
                    'card_id' => $card->card_id,
                    'user_id' => $userId,
                    'assignment_status' => 'not_assigned'
                ]);
            }
        }

        return redirect()
            ->route('leader.card.show', ['project' => $project, 'board' => $board, 'card' => $card])
            ->with('success', 'Card created successfully!');
    }

    public function show(Project $project, Board $board, Card $card)
    {
        $this->checkLeaderAccess($project->project_id);
        
        // Load all related data
        $card->load([
            'subtasks' => function($query) {
                $query->orderBy('position', 'asc');
            },
            'assignments.user', 
            'creator', 
            'comments.user'
        ]);
        
        $projectMembers = $project->members()->with('user')->get();
        
        return view('leader.cards.show', compact('project', 'board', 'card', 'projectMembers'));
    }

    public function edit(Project $project, Board $board, Card $card)
    {
        $this->checkLeaderAccess($project->project_id);
        
        $projectMembers = $project->members()->with('user')->get();
        $assignedUserIds = $card->assignments->pluck('user_id')->toArray();
        
        return view('leader.cards.edit', compact('project', 'board', 'card', 'projectMembers', 'assignedUserIds'));
    }

    public function update(Request $request, Project $project, Board $board, Card $card)
    {
        $this->checkLeaderAccess($project->project_id);

        $validated = $request->validate([
            'card_title' => 'required|string|max:100',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'status' => 'required|in:todo,in_progress,review,done',
            'priority' => 'required|in:low,medium,high',
            'estimated_hours' => 'nullable|numeric|min:0|max:9999',
            'actual_hours' => 'nullable|numeric|min:0|max:9999',
            'assigned_users' => 'nullable|array',
            'assigned_users.*' => 'exists:users,user_id'
        ]);

        // Update card data
        $card->update([
            'card_title' => $validated['card_title'],
            'description' => $validated['description'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
            'status' => $validated['status'] ?? null,
            'priority' => $validated['priority'],
            'estimated_hours' => $validated['estimated_hours'] ?? null,
            'actual_hours' => $validated['actual_hours'] ?? null
        ]);

        // Update card assignments
        // Delete existing assignments
        $card->assignments()->delete();
        
        // Add new assignments
        if (!empty($validated['assigned_users'])) {
            foreach ($validated['assigned_users'] as $userId) {
                CardAssignment::create([
                    'card_id' => $card->card_id,
                    'user_id' => $userId,
                    'assignment_status' => 'not_assigned'
                ]);
            }
        }

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