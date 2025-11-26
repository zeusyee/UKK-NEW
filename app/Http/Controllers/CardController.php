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
        
        // Get all project members with role 'member'
        $projectMembers = $project->members()
            ->with('user')
            ->where('role', 'member')
            ->get();
        
        // Get IDs of members who are already assigned to cards that are NOT done yet
        // If a card is done, the member is available for a new assignment
        $assignedUserIds = Card::whereHas('board', function($query) use ($project) {
            $query->where('project_id', $project->project_id);
        })
        ->whereNotNull('assigned_user_id')
        ->where('status', '!=', 'done') // Only consider cards that are not done
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
            'due_date' => 'required|date|after_or_equal:today',
            'priority' => 'required|in:low,medium,high',
            'estimated_hours' => 'nullable|numeric|min:0|max:9999',
            'position' => 'nullable|integer|min:0',
            'assigned_user_id' => 'required|exists:users,user_id'
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

            // Check if member is already assigned to another active card in this project
            // Cards with status 'done' are completed, so member is available again
            $isAlreadyAssigned = Card::whereHas('board', function($query) use ($project) {
                $query->where('project_id', $project->project_id);
            })
            ->where('assigned_user_id', $request->assigned_user_id)
            ->where('status', '!=', 'done') // Only check for cards that are not done
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
            'due_date' => 'required|date|after_or_equal:today',
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

    public function markAsDone(Project $project, Board $board, Card $card)
    {
        $this->checkLeaderAccess($project->project_id);
        
        // Load subtasks
        $card->load('subtasks');
        
        // Check if card has subtasks
        if ($card->subtasks->isEmpty()) {
            return back()->with('error', 'Cannot mark card as done. No subtasks found.');
        }
        
        // Check if all subtasks are done
        $allDone = $card->subtasks->every(function ($subtask) {
            return $subtask->status === 'done';
        });
        
        if (!$allDone) {
            return back()->with('error', 'Cannot mark card as done. Not all subtasks are completed yet.');
        }
        
        // Check if card is already done
        if ($card->status === 'done') {
            return back()->with('info', 'This card is already marked as done.');
        }
        
        // Update card status to done
        $card->update([
            'status' => 'done'
        ]);
        
        return redirect()
            ->route('leader.card.show', ['project' => $project, 'board' => $board, 'card' => $card])
            ->with('success', 'Card marked as completed successfully! 🎉');
    }

    public function requestHelp(Request $request, Project $project, Board $board, Card $card)
    {
        // Check if user is the assigned member of this card or is a member of the project
        $userId = Auth::id();
        
        // Verify user is a member of the project
        $isMember = ProjectMember::where('project_id', $project->project_id)
            ->where('user_id', $userId)
            ->exists();
        
        if (!$isMember) {
            abort(403, 'You are not a member of this project.');
        }
        
        // Verify user is assigned to this card
        if ($card->assigned_user_id !== $userId) {
            abort(403, 'You are not assigned to this card.');
        }
        
        $validated = $request->validate([
            'help_message' => 'required|string|max:500'
        ]);
        
        $card->update([
            'help_requested' => true,
            'help_requested_at' => now(),
            'help_message' => $validated['help_message']
        ]);
        
        return back()->with('success', 'Help request sent to leader successfully!');
    }

    public function reassignCard(Request $request, Project $project, Board $board, Card $card)
    {
        $this->checkLeaderAccess($project->project_id);
        
        $validated = $request->validate([
            'new_assigned_user_id' => 'required|exists:users,user_id'
        ]);
        
        // Verify that new assigned user is a member of the project
        $isMember = ProjectMember::where('project_id', $project->project_id)
            ->where('user_id', $validated['new_assigned_user_id'])
            ->where('role', 'member')
            ->exists();

        if (!$isMember) {
            return back()->with('error', 'The selected user is not a member of this project.');
        }
        
        // Check if new member is already assigned to another active card
        $isAlreadyAssigned = Card::whereHas('board', function($query) use ($project) {
            $query->where('project_id', $project->project_id);
        })
        ->where('assigned_user_id', $validated['new_assigned_user_id'])
        ->where('status', '!=', 'done')
        ->where('card_id', '!=', $card->card_id)
        ->exists();

        if ($isAlreadyAssigned) {
            return back()->with('error', 'This member is already assigned to another active card.');
        }
        
        $oldAssignedUser = $card->assignedUser;
        
        $card->update([
            'assigned_user_id' => $validated['new_assigned_user_id'],
            'help_requested' => false,
            'help_requested_at' => null,
            'help_message' => null
        ]);
        
        return back()->with('success', "Card reassigned from {$oldAssignedUser->full_name} successfully!");
    }

    public function cancelHelpRequest(Project $project, Board $board, Card $card)
    {
        $this->checkLeaderAccess($project->project_id);
        
        // Check if help is actually requested
        if (!$card->help_requested) {
            return back()->with('info', 'No help request to cancel.');
        }
        
        $card->update([
            'help_requested' => false,
            'help_requested_at' => null,
            'help_message' => null
        ]);
        
        return back()->with('success', 'Help request cancelled successfully!');
    }
}
