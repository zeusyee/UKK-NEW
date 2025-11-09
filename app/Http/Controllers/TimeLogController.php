<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TimeLog;
use App\Models\Project;
use App\Models\User;

class TimeLogController extends Controller
{
    public function adminIndex()
    {
        $timeLogs = TimeLog::with(['card.board.project', 'subtask', 'user'])
            ->orderBy('start_time', 'desc')
            ->paginate(20);
        
        $stats = [
            'total_duration' => TimeLog::all()->sum(function($log) {
                return $log->end_time 
                    ? $log->end_time->diffInMinutes($log->start_time) 
                    : 0;
            }),
            'total_sessions' => TimeLog::count(),
            'total_users' => TimeLog::distinct('user_id')->count(),
            'total_projects' => TimeLog::whereNotNull('card_id')
                ->join('cards', 'time_logs.card_id', '=', 'cards.card_id')
                ->join('boards', 'cards.board_id', '=', 'boards.board_id')
                ->distinct('boards.project_id')
                ->count()
        ];

        return view('admin.time-logs.index', compact('timeLogs', 'stats'));
    }

    public function adminAnalytics(Request $request)
    {
        $period = $request->get('period', 'week');
        $startDate = $this->getStartDate($period);
        
        $timeLogs = TimeLog::where('start_time', '>=', $startDate)
            ->with(['card.board.project', 'subtask', 'user'])
            ->get();
        
        $logsByDate = $timeLogs->groupBy(function($log) {
            return $log->start_time->format('Y-m-d');
        });
        
        $totalDuration = $timeLogs->sum(function($log) {
            return $log->end_time 
                ? $log->end_time->diffInMinutes($log->start_time) 
                : 0;
        });

        $analytics = [
            'total_duration' => $totalDuration,
            'total_sessions' => $timeLogs->count(),
            'avg_session_duration' => $timeLogs->count() > 0 
                ? round($totalDuration / $timeLogs->count())
                : 0,
            'logs_by_date' => $logsByDate,
        ];
        
        $timeByUser = [];
        $users = User::whereHas('timeLogs', function($query) use ($startDate) {
            $query->where('start_time', '>=', $startDate);
        })->get();
        
        foreach ($users as $user) {
            $duration = $user->timeLogs()
                ->where('start_time', '>=', $startDate)
                ->get()
                ->sum(function($log) {
                    return $log->end_time 
                        ? $log->end_time->diffInMinutes($log->start_time) 
                        : 0;
                });
            
            if ($duration > 0) {
                $timeByUser[$user->name] = $duration;
            }
        }
        
        $timeByProject = [];
        $projects = Project::whereHas('cards.timeLogs', function($query) use ($startDate) {
            $query->where('start_time', '>=', $startDate);
        })->get();
        
        foreach ($projects as $project) {
            $duration = $project->cards()
                ->join('time_logs', 'cards.card_id', '=', 'time_logs.card_id')
                ->where('time_logs.start_time', '>=', $startDate)
                ->selectRaw('time_logs.*')
                ->get()
                ->sum(function($log) {
                    return $log->end_time 
                        ? $log->end_time->diffInMinutes($log->start_time) 
                        : 0;
                });
            
            if ($duration > 0) {
                $timeByProject[$project->project_name] = $duration;
            }
        }

        return view('admin.time-logs.analytics', compact('analytics', 'timeByUser', 'timeByProject', 'period'));
    }

    public function adminExportCSV()
    {
        $timeLogs = TimeLog::with(['card', 'subtask', 'user'])
            ->orderBy('start_time', 'asc')
            ->get();
        
        $csvFileName = 'time-logs-admin-' . date('Y-m-d-H-i-s') . '.csv';
        $file = fopen('php://memory', 'w');
        
        fputcsv($file, ['Date', 'Start Time', 'End Time', 'Duration (minutes)', 'User', 'Card', 'Subtask', 'Description']);
        
        foreach ($timeLogs as $log) {
            fputcsv($file, [
                $log->start_time->format('Y-m-d'),
                $log->start_time->format('H:i:s'),
                $log->end_time ? $log->end_time->format('H:i:s') : '-',
                $log->end_time ? $log->end_time->diffInMinutes($log->start_time) : 'Ongoing',
                $log->user ? $log->user->name : '-',
                $log->card ? $log->card->card_title : '-',
                $log->subtask ? $log->subtask->subtask_title : '-',
                $log->description ?? '-'
            ]);
        }
        
        rewind($file);
        $csv = stream_get_contents($file);
        fclose($file);
        
        return response()->streamDownload(function () use ($csv) {
            echo $csv;
        }, $csvFileName);
    }

    public function projectTimeLogs(Project $project)
    {
        $timeLogs = TimeLog::whereHas('card', function($query) use ($project) {
            $query->whereHas('board', function($q) use ($project) {
                $q->where('project_id', $project->project_id);
            });
        })
        ->with(['card.board.project', 'subtask', 'user'])
        ->orderBy('start_time', 'desc')
        ->paginate(20);
        
        $stats = [
            'total_duration' => $timeLogs->getCollection()->sum(function($log) {
                return $log->end_time ? $log->end_time->diffInMinutes($log->start_time) : 0;
            }),
            'total_sessions' => $timeLogs->total(),
            'team_members' => $project->members()->distinct('user_id')->count()
        ];

        return view('admin.time-logs.project', compact('timeLogs', 'project', 'stats'));
    }

    public function userTimeLogs(User $user)
    {
        $timeLogs = TimeLog::where('user_id', $user->user_id)
            ->with(['card.board.project', 'subtask', 'user'])
            ->orderBy('start_time', 'desc')
            ->paginate(20);
        
        $stats = [
            'total_duration' => $timeLogs->getCollection()->sum(function($log) {
                return $log->end_time ? $log->end_time->diffInMinutes($log->start_time) : 0;
            }),
            'total_sessions' => $timeLogs->total(),
            'projects_involved' => TimeLog::where('user_id', $user->user_id)
                ->whereNotNull('card_id')
                ->join('cards', 'time_logs.card_id', '=', 'cards.card_id')
                ->join('boards', 'cards.board_id', '=', 'boards.board_id')
                ->distinct('boards.project_id')
                ->count()
        ];

        return view('admin.time-logs.user', compact('timeLogs', 'user', 'stats'));
    }

    private function getStartDate($period)
    {
        switch ($period) {
            case 'week':
                return now()->startOfWeek();
            case 'month':
                return now()->startOfMonth();
            case 'year':
                return now()->startOfYear();
            default:
                return now()->startOfWeek();
        }
    }
}