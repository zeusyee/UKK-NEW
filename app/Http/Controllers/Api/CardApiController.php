<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\CardAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CardApiController extends Controller
{
    /**
     * Ambil semua card yang ditugaskan ke member
     *
     * @queryParam status string Filter status (assigned, in_progress, completed)
     * @queryParam page int Halaman (default: 1)
     * @response 200 {"data": [...], "pagination": {...}}
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $cards = Card::where('assigned_user_id', $user->id)
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->with(['board', 'assignedUser', 'cardAssignments'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 10);

        return response()->json([
            'data' => $cards->items(),
            'pagination' => [
                'total' => $cards->total(),
                'per_page' => $cards->perPage(),
                'current_page' => $cards->currentPage(),
                'last_page' => $cards->lastPage(),
            ]
        ], 200);
    }

    /**
     * Ambil card yang ditugaskan ke member (alias dari index)
     */
    public function myTasks(Request $request)
    {
        return $this->index($request);
    }

    /**
     * Ambil detail card
     *
     * @urlParam card int ID card
     * @response 200 {"card": {...}}
     * @response 404 {"message": "Card tidak ditemukan"}
     */
    public function show(Request $request, Card $card)
    {
        $user = $request->user();

        // Cek apakah card ditugaskan ke user ini
        if ($card->assigned_user_id !== $user->id) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $card->load(['board', 'assignedUser', 'subtasks', 'cardAssignments']);

        return response()->json([
            'card' => $this->formatCard($card)
        ], 200);
    }

    /**
     * Ambil card berdasarkan board
     *
     * @urlParam board int ID board
     * @response 200 {"cards": [...]}
     */
    public function cardsByBoard(Request $request, $boardId)
    {
        $user = $request->user();

        $cards = Card::where('board_id', $boardId)
            ->where('assigned_user_id', $user->id)
            ->with(['subtasks', 'cardAssignments'])
            ->get();

        return response()->json([
            'cards' => $cards->map(fn($card) => $this->formatCard($card))
        ], 200);
    }

    /**
     * Mulai mengerjakan card
     *
     * @urlParam card int ID card
     * @response 200 {"message": "Card dimulai", "card": {...}}
     */
    public function startCard(Request $request, Card $card)
    {
        $user = $request->user();

        // Cek apakah card ditugaskan ke user ini
        if ($card->assigned_user_id !== $user->id) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        // Update status card
        $card->update(['status' => 'in_progress']);

        // Auto-create assignment jika belum ada
        if (!$card->cardAssignments()->exists()) {
            CardAssignment::createAssignment($card->id, $user->id);
        }

        // Start active assignment
        $assignment = $card->getActiveAssignment();
        if ($assignment && $assignment->isPending()) {
            $assignment->startAssignment();
        }

        return response()->json([
            'message' => 'Card dimulai',
            'card' => $this->formatCard($card->fresh())
        ], 200);
    }

    /**
     * Ambil assignment card
     *
     * @urlParam card int ID card
     * @response 200 {"assignment": {...}}
     */
    public function getAssignment(Request $request, Card $card)
    {
        $user = $request->user();

        if ($card->assigned_user_id !== $user->id) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $assignment = $card->getActiveAssignment();

        if (!$assignment) {
            return response()->json(['message' => 'Tidak ada assignment aktif'], 404);
        }

        return response()->json([
            'assignment' => $this->formatAssignment($assignment)
        ], 200);
    }

    /**
     * Format card data
     */
    private function formatCard($card)
    {
        $activeAssignment = $card->getActiveAssignment();

        return [
            'id' => $card->id,
            'title' => $card->title,
            'description' => $card->description,
            'priority' => $card->priority,
            'status' => $card->status,
            'board' => [
                'id' => $card->board->id,
                'name' => $card->board->name,
            ],
            'assigned_user' => $card->assignedUser ? [
                'id' => $card->assignedUser->id,
                'name' => $card->assignedUser->name,
            ] : null,
            'subtasks_count' => $card->subtasks->count(),
            'completed_subtasks' => $card->subtasks->where('status', 'completed')->count(),
            'active_assignment' => $activeAssignment ? $this->formatAssignment($activeAssignment) : null,
            'created_at' => $card->created_at,
            'updated_at' => $card->updated_at,
        ];
    }

    /**
     * Format assignment data
     */
    private function formatAssignment($assignment)
    {
        return [
            'id' => $assignment->assignment_id,
            'status' => $assignment->assignment_status,
            'assigned_at' => $assignment->assigned_at,
            'started_at' => $assignment->started_at,
            'completed_at' => $assignment->completed_at,
            'duration_minutes' => $assignment->getDurationInMinutes(),
            'duration_hours' => $assignment->getDurationInHours(),
            'human_duration' => $assignment->getHumanDuration(),
            'is_overdue' => $assignment->isOverdue(),
        ];
    }
}
