<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Board;
use Illuminate\Http\Request;

class ProjectApiController extends Controller
{
    /**
     * Ambil semua project yang diikuti member
     *
     * @queryParam page int Halaman (default: 1)
     * @queryParam per_page int Jumlah per halaman (default: 10)
     * @response 200 {"data": [...], "pagination": {...}}
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Member hanya bisa lihat project yang dia ikuti
        $projects = Project::whereHas('members', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->with(['creator', 'members'])
            ->paginate($request->per_page ?? 10);

        return response()->json([
            'data' => $projects->items(),
            'pagination' => [
                'total' => $projects->total(),
                'per_page' => $projects->perPage(),
                'current_page' => $projects->currentPage(),
                'last_page' => $projects->lastPage(),
            ]
        ], 200);
    }

    /**
     * Ambil detail project beserta board dan card
     *
     * @urlParam project int ID project
     * @response 200 {"project": {...}}
     * @response 404 {"message": "Project tidak ditemukan"}
     */
    public function show(Request $request, Project $project)
    {
        $user = $request->user();

        // Cek apakah user adalah member project ini
        if (!$project->members()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $project->load(['creator', 'members', 'boards']);

        return response()->json([
            'project' => $this->formatProject($project)
        ], 200);
    }

    /**
     * Ambil semua board dalam project
     *
     * @urlParam project int ID project
     * @response 200 {"boards": [...]}
     */
    public function boards(Request $request, Project $project)
    {
        $user = $request->user();

        // Cek apakah user adalah member project ini
        if (!$project->members()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $boards = $project->boards()
            ->with(['cards' => function ($query) use ($user) {
                $query->where('assigned_user_id', $user->id)
                    ->orWhereNull('assigned_user_id');
            }])
            ->get();

        return response()->json([
            'boards' => $boards->map(fn($board) => $this->formatBoard($board))
        ], 200);
    }

    /**
     * Format project data
     */
    private function formatProject($project)
    {
        return [
            'id' => $project->id,
            'name' => $project->name,
            'description' => $project->description,
            'status' => $project->status,
            'created_at' => $project->created_at,
            'creator' => [
                'id' => $project->creator->id,
                'name' => $project->creator->name,
            ],
            'members_count' => $project->members->count(),
            'boards_count' => $project->boards->count(),
        ];
    }

    /**
     * Format board data
     */
    private function formatBoard($board)
    {
        return [
            'id' => $board->id,
            'name' => $board->name,
            'cards_count' => $board->cards->count(),
            'cards' => $board->cards->map(fn($card) => [
                'id' => $card->id,
                'title' => $card->title,
                'description' => $card->description,
                'assigned_user_id' => $card->assigned_user_id,
                'priority' => $card->priority,
                'status' => $card->status,
            ]),
        ];
    }
}
