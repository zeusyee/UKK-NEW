<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\Subtask;
use App\Models\TimeLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubtaskApiController extends Controller
{
    /**
     * Buat subtask baru
     *
     * @urlParam card int ID card
     * @bodyParam title string required Judul subtask
     * @bodyParam description string Deskripsi subtask
     * @response 201 {"message": "Subtask berhasil dibuat", "subtask": {...}}
     * @response 422 {"errors": {...}}
     */
    public function store(Request $request, $cardId)
    {
        $user = $request->user();
        $card = Card::findOrFail($cardId);

        // Cek apakah card ditugaskan ke user
        if ($card->assigned_user_id !== $user->id) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $subtask = Subtask::create([
            'card_id' => $cardId,
            'title' => $request->title,
            'description' => $request->description,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Subtask berhasil dibuat',
            'subtask' => $this->formatSubtask($subtask)
        ], 201);
    }

    /**
     * Ambil detail subtask
     *
     * @urlParam subtask int ID subtask
     * @response 200 {"subtask": {...}}
     */
    public function show(Request $request, Subtask $subtask)
    {
        $user = $request->user();

        // Cek akses
        if ($subtask->card->assigned_user_id !== $user->id) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $subtask->load(['card', 'timeLogs']);

        return response()->json([
            'subtask' => $this->formatSubtask($subtask, true)
        ], 200);
    }

    /**
     * Update subtask
     *
     * @urlParam subtask int ID subtask
     * @bodyParam title string Judul subtask
     * @bodyParam description string Deskripsi subtask
     * @response 200 {"message": "Subtask berhasil diupdate", "subtask": {...}}
     */
    public function update(Request $request, Subtask $subtask)
    {
        $user = $request->user();

        if ($subtask->card->assigned_user_id !== $user->id) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string|nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $subtask->update($request->only(['title', 'description']));

        return response()->json([
            'message' => 'Subtask berhasil diupdate',
            'subtask' => $this->formatSubtask($subtask)
        ], 200);
    }

    /**
     * Hapus subtask
     *
     * @urlParam subtask int ID subtask
     * @response 200 {"message": "Subtask berhasil dihapus"}
     */
    public function destroy(Request $request, Subtask $subtask)
    {
        $user = $request->user();

        if ($subtask->card->assigned_user_id !== $user->id) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $subtask->delete();

        return response()->json(['message' => 'Subtask berhasil dihapus'], 200);
    }

    /**
     * Mulai mengerjakan subtask
     *
     * @urlParam subtask int ID subtask
     * @response 200 {"message": "Subtask dimulai", "subtask": {...}}
     */
    public function start(Request $request, Subtask $subtask)
    {
        $user = $request->user();

        if ($subtask->card->assigned_user_id !== $user->id) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $subtask->startSubtask($user->id);

        return response()->json([
            'message' => 'Subtask dimulai',
            'subtask' => $this->formatSubtask($subtask->fresh())
        ], 200);
    }

    /**
     * Pause subtask yang sedang dikerjakan
     *
     * @urlParam subtask int ID subtask
     * @response 200 {"message": "Subtask dijeda", "subtask": {...}}
     */
    public function pause(Request $request, Subtask $subtask)
    {
        $user = $request->user();

        if ($subtask->card->assigned_user_id !== $user->id) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $subtask->pauseSubtask();

        return response()->json([
            'message' => 'Subtask dijeda',
            'subtask' => $this->formatSubtask($subtask->fresh())
        ], 200);
    }

    /**
     * Resume subtask yang sudah dijeda
     *
     * @urlParam subtask int ID subtask
     * @response 200 {"message": "Subtask dilanjutkan", "subtask": {...}}
     */
    public function resume(Request $request, Subtask $subtask)
    {
        $user = $request->user();

        if ($subtask->card->assigned_user_id !== $user->id) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $subtask->resumeSubtask();

        return response()->json([
            'message' => 'Subtask dilanjutkan',
            'subtask' => $this->formatSubtask($subtask->fresh())
        ], 200);
    }

    /**
     * Submit subtask (selesaikan dan tunggu review)
     *
     * @urlParam subtask int ID subtask
     * @bodyParam notes string Catatan atau penjelasan
     * @response 200 {"message": "Subtask disubmit", "subtask": {...}}
     */
    public function submit(Request $request, Subtask $subtask)
    {
        $user = $request->user();

        if ($subtask->card->assigned_user_id !== $user->id) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $validator = Validator::make($request->all(), [
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $subtask->submitSubtask($request->notes);

        return response()->json([
            'message' => 'Subtask disubmit',
            'subtask' => $this->formatSubtask($subtask->fresh())
        ], 200);
    }

    /**
     * Ambil time logs user (semua subtask)
     *
     * @queryParam card_id int Filter berdasarkan card
     * @queryParam limit int Jumlah limit (default: 50)
     * @response 200 {"time_logs": [...]}
     */
    public function timeLogs(Request $request)
    {
        $user = $request->user();

        $query = TimeLog::whereHas('subtask', function ($q) use ($user) {
            $q->whereHas('card', function ($q2) use ($user) {
                $q2->where('assigned_user_id', $user->id);
            });
        });

        if ($request->card_id) {
            $query->whereHas('subtask', function ($q) use ($request) {
                $q->where('card_id', $request->card_id);
            });
        }

        $timeLogs = $query->orderBy('start_time', 'desc')
            ->limit($request->limit ?? 50)
            ->get();

        return response()->json([
            'time_logs' => $timeLogs->map(fn($log) => $this->formatTimeLog($log))
        ], 200);
    }

    /**
     * Ambil time logs untuk subtask tertentu
     *
     * @urlParam subtask int ID subtask
     * @response 200 {"time_logs": [...]}
     */
    public function subtaskTimeLogs(Request $request, Subtask $subtask)
    {
        $user = $request->user();

        if ($subtask->card->assigned_user_id !== $user->id) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $timeLogs = $subtask->timeLogs()
            ->orderBy('start_time', 'desc')
            ->get();

        return response()->json([
            'time_logs' => $timeLogs->map(fn($log) => $this->formatTimeLog($log))
        ], 200);
    }

    /**
     * Format subtask data
     */
    private function formatSubtask($subtask, $detailed = false)
    {
        $data = [
            'id' => $subtask->id,
            'title' => $subtask->title,
            'description' => $subtask->description,
            'status' => $subtask->status,
            'priority' => $subtask->priority,
            'card_id' => $subtask->card_id,
            'created_at' => $subtask->created_at,
            'updated_at' => $subtask->updated_at,
        ];

        if ($detailed) {
            $data['time_logs'] = $subtask->timeLogs->map(fn($log) => $this->formatTimeLog($log));
            $data['total_duration_minutes'] = $subtask->timeLogs->sum(function ($log) {
                return $log->end_time ? $log->end_time->diffInMinutes($log->start_time) : 0;
            });
        }

        return $data;
    }

    /**
     * Format time log data
     */
    private function formatTimeLog($log)
    {
        return [
            'id' => $log->id,
            'start_time' => $log->start_time,
            'end_time' => $log->end_time,
            'duration_minutes' => $log->end_time ? $log->end_time->diffInMinutes($log->start_time) : null,
        ];
    }
}
