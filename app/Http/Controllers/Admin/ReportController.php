<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subtask;
use App\Models\Card;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function subtasksReport(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:daily,monthly,custom',
            'date' => 'required_if:report_type,daily|date',
            'month' => 'required_if:report_type,monthly',
            'start_date' => 'required_if:report_type,custom|date',
            'end_date' => 'required_if:report_type,custom|date|after_or_equal:start_date',
            'member_id' => 'nullable|exists:users,user_id',
            'project_id' => 'nullable|exists:projects,project_id'
        ]);

        $query = Subtask::with(['card.board.project', 'assignedUser', 'reviewer'])
            ->whereNotNull('completed_at');

        // Filter by report type
        switch ($request->report_type) {
            case 'daily':
                $date = Carbon::parse($request->date);
                $query->whereDate('completed_at', $date);
                $periodTitle = $date->format('d M Y');
                break;
            
            case 'monthly':
                $monthYear = Carbon::parse($request->month . '-01');
                $query->whereYear('completed_at', $monthYear->year)
                      ->whereMonth('completed_at', $monthYear->month);
                $periodTitle = $monthYear->format('F Y');
                break;
            
            case 'custom':
                $startDate = Carbon::parse($request->start_date);
                $endDate = Carbon::parse($request->end_date);
                $query->whereBetween('completed_at', [$startDate->startOfDay(), $endDate->endOfDay()]);
                $periodTitle = $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y');
                break;
        }

        // Filter by member
        if ($request->member_id) {
            $query->where('assigned_user_id', $request->member_id);
        }

        // Filter by project
        if ($request->project_id) {
            $query->whereHas('card.board', function($q) use ($request) {
                $q->where('project_id', $request->project_id);
            });
        }

        $subtasks = $query->orderBy('completed_at', 'desc')->get();

        // Group by member
        $memberStats = $subtasks->groupBy('assigned_user_id')->map(function($tasks, $userId) {
            $user = User::find($userId);
            return [
                'user' => $user,
                'total_tasks' => $tasks->count(),
                'approved' => $tasks->where('status', 'approved')->count(),
                'rejected' => $tasks->where('status', 'rejected')->count(),
                'pending' => $tasks->whereIn('status', ['done', 'in_progress'])->count(),
                'tasks' => $tasks
            ];
        })->sortByDesc('total_tasks');

        $data = [
            'subtasks' => $subtasks,
            'memberStats' => $memberStats,
            'periodTitle' => $periodTitle,
            'reportType' => $request->report_type,
            'filters' => $request->all(),
            'totalTasks' => $subtasks->count(),
            'totalApproved' => $subtasks->where('status', 'approved')->count(),
            'totalRejected' => $subtasks->where('status', 'rejected')->count(),
            'totalPending' => $subtasks->whereIn('status', ['done', 'in_progress'])->count()
        ];

        if ($request->has('print')) {
            $pdf = Pdf::loadView('admin.reports.subtasks-print', $data);
            return $pdf->download('Laporan-Subtasks-' . $periodTitle . '.pdf');
        }

        return view('admin.reports.subtasks', $data);
    }

    public function cardsReport(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:daily,monthly,custom',
            'date' => 'required_if:report_type,daily|date',
            'month' => 'required_if:report_type,monthly',
            'start_date' => 'required_if:report_type,custom|date',
            'end_date' => 'required_if:report_type,custom|date|after_or_equal:start_date',
            'project_id' => 'nullable|exists:projects,project_id'
        ]);

        $query = Card::with(['board.project', 'assignedUser', 'subtasks']);

        // Filter by report type
        switch ($request->report_type) {
            case 'daily':
                $date = Carbon::parse($request->date);
                $query->whereDate('created_at', $date);
                $periodTitle = $date->format('d M Y');
                break;
            
            case 'monthly':
                $monthYear = Carbon::parse($request->month . '-01');
                $query->whereYear('created_at', $monthYear->year)
                      ->whereMonth('created_at', $monthYear->month);
                $periodTitle = $monthYear->format('F Y');
                break;
            
            case 'custom':
                $startDate = Carbon::parse($request->start_date);
                $endDate = Carbon::parse($request->end_date);
                $query->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()]);
                $periodTitle = $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y');
                break;
        }

        // Filter by project
        if ($request->project_id) {
            $query->whereHas('board', function($q) use ($request) {
                $q->where('project_id', $request->project_id);
            });
        }

        $cards = $query->orderBy('created_at', 'desc')->get();

        $data = [
            'cards' => $cards,
            'periodTitle' => $periodTitle,
            'reportType' => $request->report_type,
            'filters' => $request->all(),
            'totalCards' => $cards->count(),
            'completedCards' => $cards->filter(fn($card) => $card->isCompleted())->count(),
            'inProgressCards' => $cards->filter(fn($card) => !$card->isCompleted())->count()
        ];

        if ($request->has('print')) {
            $pdf = Pdf::loadView('admin.reports.cards-print', $data);
            return $pdf->download('Laporan-Cards-' . $periodTitle . '.pdf');
        }

        return view('admin.reports.cards', $data);
    }

    public function projectsReport(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:daily,monthly,custom',
            'date' => 'required_if:report_type,daily|date',
            'month' => 'required_if:report_type,monthly',
            'start_date' => 'required_if:report_type,custom|date',
            'end_date' => 'required_if:report_type,custom|date|after_or_equal:start_date',
            'status' => 'nullable|in:active,completed'
        ]);

        $query = Project::with(['creator', 'boards.cards', 'members.user']);

        // Filter by report type
        switch ($request->report_type) {
            case 'daily':
                $date = Carbon::parse($request->date);
                $query->whereDate('created_at', $date);
                $periodTitle = $date->format('d M Y');
                break;
            
            case 'monthly':
                $monthYear = Carbon::parse($request->month . '-01');
                $query->whereYear('created_at', $monthYear->year)
                      ->whereMonth('created_at', $monthYear->month);
                $periodTitle = $monthYear->format('F Y');
                break;
            
            case 'custom':
                $startDate = Carbon::parse($request->start_date);
                $endDate = Carbon::parse($request->end_date);
                $query->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()]);
                $periodTitle = $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y');
                break;
        }

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $projects = $query->orderBy('created_at', 'desc')->get();

        $data = [
            'projects' => $projects,
            'periodTitle' => $periodTitle,
            'reportType' => $request->report_type,
            'filters' => $request->all(),
            'totalProjects' => $projects->count(),
            'activeProjects' => $projects->where('status', 'active')->count(),
            'completedProjects' => $projects->where('status', 'completed')->count()
        ];

        if ($request->has('print')) {
            $pdf = Pdf::loadView('admin.reports.projects-print', $data);
            return $pdf->download('Laporan-Projects-' . $periodTitle . '.pdf');
        }

        return view('admin.reports.projects', $data);
    }
}
