<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Subtasks - {{ $periodTitle }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .stats {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .stat-box {
            display: table-cell;
            width: 25%;
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }
        .stat-box .label {
            font-size: 10px;
            color: #666;
            margin-bottom: 5px;
        }
        .stat-box .value {
            font-size: 20px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th {
            background: #f5f5f5;
            padding: 8px;
            text-align: left;
            font-size: 11px;
            border: 1px solid #ddd;
        }
        table td {
            padding: 6px;
            border: 1px solid #ddd;
            font-size: 10px;
        }
        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-danger { background: #f8d7da; color: #721c24; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN SUBTASKS</h1>
        <p><strong>Periode:</strong> {{ $periodTitle }}</p>
        <p><strong>Tanggal Cetak:</strong> {{ now()->format('d M Y H:i') }}</p>
    </div>

    <div class="stats">
        <div class="stat-box">
            <div class="label">Total Subtasks</div>
            <div class="value">{{ $totalTasks }}</div>
        </div>
        <div class="stat-box">
            <div class="label">Approved</div>
            <div class="value" style="color: #28a745;">{{ $totalApproved }}</div>
        </div>
        <div class="stat-box">
            <div class="label">Rejected</div>
            <div class="value" style="color: #dc3545;">{{ $totalRejected }}</div>
        </div>
        <div class="stat-box">
            <div class="label">Pending</div>
            <div class="value" style="color: #ffc107;">{{ $totalPending }}</div>
        </div>
    </div>

    @if($memberStats->count() > 0)
    <h3 style="margin-top: 20px; margin-bottom: 10px;">Statistik Per Member</h3>
    <table>
        <thead>
            <tr>
                <th style="width: 30%;">Member</th>
                <th style="width: 15%;">Total</th>
                <th style="width: 15%;">Approved</th>
                <th style="width: 15%;">Rejected</th>
                <th style="width: 15%;">Pending</th>
                <th style="width: 10%;">Success %</th>
            </tr>
        </thead>
        <tbody>
            @foreach($memberStats as $stat)
            <tr>
                <td><strong>{{ $stat['user']->full_name }}</strong></td>
                <td style="text-align: center;"><strong>{{ $stat['total_tasks'] }}</strong></td>
                <td style="text-align: center;">{{ $stat['approved'] }}</td>
                <td style="text-align: center;">{{ $stat['rejected'] }}</td>
                <td style="text-align: center;">{{ $stat['pending'] }}</td>
                <td style="text-align: center;">
                    @php $successRate = $stat['total_tasks'] > 0 ? round(($stat['approved'] / $stat['total_tasks']) * 100, 1) : 0; @endphp
                    {{ $successRate }}%
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <h3 style="margin-top: 20px; margin-bottom: 10px;">Detail Subtasks ({{ $subtasks->count() }})</h3>
    @if($subtasks->count() > 0)
    <table>
        <thead>
            <tr>
                <th style="width: 25%;">Subtask</th>
                <th style="width: 20%;">Card / Project</th>
                <th style="width: 15%;">Member</th>
                <th style="width: 15%;">Completed At</th>
                <th style="width: 15%;">Status</th>
                <th style="width: 10%;">Reviewed By</th>
            </tr>
        </thead>
        <tbody>
            @foreach($subtasks as $subtask)
            <tr>
                <td>
                    <strong>{{ $subtask->subtask_title }}</strong>
                    @if($subtask->description)
                    <br><small style="color: #666;">{{ Str::limit($subtask->description, 40) }}</small>
                    @endif
                </td>
                <td>
                    <strong>{{ $subtask->card->card_title ?? 'N/A' }}</strong>
                    <br><small style="color: #666;">{{ $subtask->card->board->project->project_name ?? 'N/A' }}</small>
                </td>
                <td>{{ $subtask->assignedUser->full_name ?? 'N/A' }}</td>
                <td>{{ $subtask->completed_at ? $subtask->completed_at->format('d M Y H:i') : '-' }}</td>
                <td>
                    @if($subtask->status === 'approved')
                        <span class="badge badge-success">Approved</span>
                    @elseif($subtask->status === 'rejected')
                        <span class="badge badge-danger">Rejected</span>
                    @else
                        <span class="badge badge-warning">{{ ucfirst($subtask->status) }}</span>
                    @endif
                </td>
                <td>{{ $subtask->reviewer->full_name ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p style="text-align: center; padding: 20px; color: #999;">Tidak ada data untuk periode ini</p>
    @endif

    <div class="footer">
        <p>Dokumen ini digenerate otomatis oleh sistem pada {{ now()->format('d M Y H:i:s') }}</p>
        <p>&copy; {{ now()->year }} Project Management System</p>
    </div>
</body>
</html>
