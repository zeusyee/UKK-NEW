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
            'total_projects' => TimeLog::whereNotNull('time_logs.card_id')
                ->join('cards', 'time_logs.card_id', '=', 'cards.card_id')
                ->join('boards', 'cards.board_id', '=', 'boards.board_id')
                ->select('boards.project_id')
                ->distinct()
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
                $timeByUser[$user->full_name] = $duration;
            }
        }
        
        $timeByProject = [];
        $projects = Project::whereHas('boards.cards.timeLogs', function($query) use ($startDate) {
            $query->where('start_time', '>=', $startDate);
        })->get();
        
        foreach ($projects as $project) {
            $duration = 0;
            foreach ($project->boards as $board) {
                foreach ($board->cards as $card) {
                    $cardLogs = $card->timeLogs()
                        ->where('start_time', '>=', $startDate)
                        ->get();
                    
                    foreach ($cardLogs as $log) {
                        $duration += $log->end_time 
                            ? $log->end_time->diffInMinutes($log->start_time) 
                            : 0;
                    }
                }
            }
            
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
                ->whereNotNull('time_logs.card_id')
                ->join('cards', 'time_logs.card_id', '=', 'cards.card_id')
                ->join('boards', 'cards.board_id', '=', 'boards.board_id')
                ->select('boards.project_id')
                ->distinct()
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

    public function report(Request $request)
    {
        $query = TimeLog::with(['card.board.project', 'subtask', 'user']);

        if (request('user_id')) {
            $query->where('user_id', request('user_id'));
        }

        if (request('project_id')) {
            $query->whereHas('card.board', function($q) {
                $q->where('project_id', request('project_id'));
            });
        }

        if (request('start_date')) {
            $query->whereDate('start_time', '>=', request('start_date'));
        }

        if (request('end_date')) {
            $query->whereDate('start_time', '<=', request('end_date'));
        }

        $timeLogs = $query->orderBy('start_time', 'desc')->paginate(20);

        $totalHours = $timeLogs->getCollection()->sum(function($log) {
            return $log->end_time ? $log->end_time->diffInMinutes($log->start_time) / 60 : 0;
        });

        $totalSessions = $timeLogs->total();
        $avgHours = $totalSessions > 0 ? $totalHours / $totalSessions : 0;
        $activeUsers = $timeLogs->getCollection()->pluck('user_id')->unique()->count();

        return view('admin.time-logs.report', compact('timeLogs', 'totalHours', 'totalSessions', 'avgHours', 'activeUsers'));
    }

    public function teamProductivity(Request $request)
    {
        $period = $request->get('period', 'week');
        $startDate = $this->getStartDate($period);

        $timeLogs = TimeLog::where('start_time', '>=', $startDate)
            ->with(['card.board.project', 'subtask', 'user'])
            ->get();

        $totalHours = $timeLogs->sum(function($log) {
            return $log->end_time ? $log->end_time->diffInMinutes($log->start_time) / 60 : 0;
        });

        $totalSessions = $timeLogs->count();
        $activeUsers = $timeLogs->pluck('user_id')->unique()->count();
        $avgHoursPerUser = $activeUsers > 0 ? $totalHours / $activeUsers : 0;

        $userStats = $timeLogs->groupBy('user_id')
            ->map(function($logs) {
                $totalMinutes = $logs->sum(function($log) {
                    return $log->end_time ? $log->end_time->diffInMinutes($log->start_time) : 0;
                });
                return (object)[
                    'user' => $logs->first()->user,
                    'total_hours' => $totalMinutes / 60,
                    'sessions' => $logs->count(),
                    'avg_hours' => $logs->count() > 0 ? ($totalMinutes / 60) / $logs->count() : 0
                ];
            })
            ->sortByDesc('total_hours');

        $projectStats = $timeLogs->filter(fn($log) => $log->card)
            ->groupBy(fn($log) => $log->card->board->project_id)
            ->map(function($logs) {
                $totalMinutes = $logs->sum(function($log) {
                    return $log->end_time ? $log->end_time->diffInMinutes($log->start_time) : 0;
                });
                return (object)[
                    'project' => $logs->first()->card->board->project,
                    'total_hours' => $totalMinutes / 60,
                    'sessions' => $logs->count(),
                    'members' => $logs->pluck('user_id')->unique()->count()
                ];
            })
            ->sortByDesc('total_hours')
            ->take(10);

        $stats = [
            'total_hours' => $totalHours,
            'total_sessions' => $totalSessions,
            'active_users' => $activeUsers,
            'avg_hours_per_user' => $avgHoursPerUser
        ];

        return view('admin.team-productivity', compact('stats', 'userStats', 'projectStats', 'period'));
    }
}
