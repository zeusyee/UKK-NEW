<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\Subtask;
use App\Models\TimeLog;
use Illuminate\Http\Request;

class MemberApiController extends Controller
{
    /**
     * Ambil dashboard data untuk member
     *
     * @response 200 {"dashboard": {...}}
     */
    public function dashboard(Request $request)
    {
        $user = $request->user();

        // Total card
        $totalCards = Card::where('assigned_user_id', $user->id)->count();
        
        // Card by status
        $cardsInProgress = Card::where('assigned_user_id', $user->id)
            ->where('status', 'in_progress')
            ->count();
        $cardsCompleted = Card::where('assigned_user_id', $user->id)
            ->where('status', 'completed')
            ->count();
        $cardsPending = Card::where('assigned_user_id', $user->id)
            ->where('status', 'pending')
            ->count();

        // Total subtasks
        $totalSubtasks = Subtask::whereHas('card', function ($query) use ($user) {
            $query->where('assigned_user_id', $user->id);
        })->count();

        $subtasksCompleted = Subtask::whereHas('card', function ($query) use ($user) {
            $query->where('assigned_user_id', $user->id);
        })->where('status', 'completed')->count();

        // Recent cards
        $recentCards = Card::where('assigned_user_id', $user->id)
            ->with(['board', 'cardAssignments'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Recent subtasks
        $recentSubtasks = Subtask::whereHas('card', function ($query) use ($user) {
            $query->where('assigned_user_id', $user->id);
        })
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'dashboard' => [
                'total_cards' => $totalCards,
                'cards_in_progress' => $cardsInProgress,
                'cards_completed' => $cardsCompleted,
                'cards_pending' => $cardsPending,
                'total_subtasks' => $totalSubtasks,
                'subtasks_completed' => $subtasksCompleted,
                'recent_cards' => $recentCards->map(fn($card) => [
                    'id' => $card->id,
                    'title' => $card->title,
                    'status' => $card->status,
                    'priority' => $card->priority,
                    'board' => $card->board->name,
                    'created_at' => $card->created_at,
                ]),
                'recent_subtasks' => $recentSubtasks->map(fn($subtask) => [
                    'id' => $subtask->id,
                    'title' => $subtask->title,
                    'status' => $subtask->status,
                    'card_title' => $subtask->card->title,
                    'updated_at' => $subtask->updated_at,
                ]),
            ]
        ], 200);
    }

    /**
     * Ambil statistik member
     *
     * @queryParam period string Periode (today, week, month, all) - default: month
     * @response 200 {"statistics": {...}}
     */
    public function statistics(Request $request)
    {
        $user = $request->user();
        $period = $request->period ?? 'month';

        // Calculate date range
        $dateRange = match ($period) {
            'today' => [now()->startOfDay(), now()->endOfDay()],
            'week' => [now()->startOfWeek(), now()->endOfWeek()],
            'month' => [now()->startOfMonth(), now()->endOfMonth()],
            'all' => [null, null],
        };

        $query = TimeLog::whereHas('subtask', function ($q) use ($user) {
            $q->whereHas('card', function ($q2) use ($user) {
                $q2->where('assigned_user_id', $user->id);
            });
        });

        if ($dateRange[0]) {
            $query->whereBetween('start_time', $dateRange);
        }

        $timeLogs = $query->get();

        // Calculate total duration
        $totalMinutes = $timeLogs->sum(function ($log) {
            return $log->end_time ? $log->end_time->diffInMinutes($log->start_time) : 0;
        });

        // Calculate by day
        $byDay = $timeLogs->groupBy(function ($log) {
            return $log->start_time->format('Y-m-d');
        })->map(function ($logs) {
            return $logs->sum(function ($log) {
                return $log->end_time ? $log->end_time->diffInMinutes($log->start_time) : 0;
            });
        });

        // Completed subtasks
        $completedSubtasks = Subtask::whereHas('card', function ($q) use ($user) {
            $q->where('assigned_user_id', $user->id);
        })
            ->where('status', 'completed');

        if ($dateRange[0]) {
            $completedSubtasks->whereBetween('updated_at', $dateRange);
        }

        $completedCount = $completedSubtasks->count();

        // Completed cards
        $completedCards = Card::where('assigned_user_id', $user->id)
            ->where('status', 'completed');

        if ($dateRange[0]) {
            $completedCards->whereBetween('updated_at', $dateRange);
        }

        $completedCardsCount = $completedCards->count();

        return response()->json([
            'statistics' => [
                'period' => $period,
                'total_duration_minutes' => $totalMinutes,
                'total_duration_hours' => round($totalMinutes / 60, 2),
                'total_duration_days' => round($totalMinutes / 60 / 24, 2),
                'completed_subtasks' => $completedCount,
                'completed_cards' => $completedCardsCount,
                'average_duration_per_subtask' => $completedCount > 0 ? round($totalMinutes / $completedCount, 2) : 0,
                'daily_breakdown' => $byDay->map(function ($minutes, $day) {
                    return [
                        'date' => $day,
                        'duration_minutes' => $minutes,
                        'duration_hours' => round($minutes / 60, 2),
                    ];
                })->values(),
            ]
        ], 200);
    }
}
