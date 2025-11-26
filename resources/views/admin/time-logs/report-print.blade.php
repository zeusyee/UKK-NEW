<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Time Logs Report - {{ $periodTitle }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 3px solid #3b82f6;
        }
        .header h1 {
            color: #1e40af;
            font-size: 24px;
            margin-bottom: 8px;
        }
        .header .period {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 5px;
        }
        .header .print-date {
            color: #9ca3af;
            font-size: 11px;
        }
        .stats-section {
            margin-bottom: 25px;
            padding: 15px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
        }
        .stats-section h2 {
            font-size: 14px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 2px solid #3b82f6;
        }
        .stats-table {
            width: 100%;
            border-collapse: collapse;
        }
        .stats-table td {
            padding: 8px 12px;
        }
        .stat-label {
            font-size: 11px;
            color: #6b7280;
            font-weight: 600;
            width: 25%;
        }
        .stat-value {
            font-size: 16px;
            color: #1f2937;
            font-weight: bold;
            width: 25%;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #1f2937;
            margin: 20px 0 15px 0;
            padding-bottom: 8px;
            border-bottom: 2px solid #e5e7eb;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table thead {
            background: #f9fafb;
        }
        table th {
            padding: 10px 8px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            color: #374151;
            border-bottom: 2px solid #d1d5db;
            text-transform: uppercase;
        }
        table td {
            padding: 8px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 11px;
            vertical-align: top;
        }
        table tbody tr:nth-child(even) {
            background: #f9fafb;
        }
        table tbody tr:hover {
            background: #f3f4f6;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
        }
        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }
        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }
        .text-muted {
            color: #9ca3af;
            font-size: 10px;
        }
        .duration {
            font-weight: 600;
            color: #1f2937;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
        }
        .no-data {
            text-align: center;
            padding: 40px 20px;
            color: #9ca3af;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Time Logs</h1>
        <div class="period">Periode: {{ $periodTitle }}</div>
        <div class="print-date">Dicetak pada: {{ now()->format('d F Y, H:i') }}</div>
    </div>

    <div class="stats-section">
        <h2>Ringkasan Statistik</h2>
        <table class="stats-table">
            <tr>
                <td class="stat-label">Total Jam:</td>
                <td class="stat-value">{{ number_format($totalHours, 1) }} jam</td>
                <td class="stat-label">Total Sesi:</td>
                <td class="stat-value">{{ $totalSessions }} sesi</td>
            </tr>
            <tr>
                <td class="stat-label">Rata-rata:</td>
                <td class="stat-value">{{ number_format($avgHours, 1) }} jam/sesi</td>
                <td class="stat-label">Pengguna Aktif:</td>
                <td class="stat-value">{{ $activeUsers }} pengguna</td>
            </tr>
        </table>
    </div>

    <div class="section-title">Detail Time Logs</div>

    @if($timeLogs->count() > 0)
    <table>
        <thead>
            <tr>
                <th style="width: 15%;">Pengguna</th>
                <th style="width: 20%;">Proyek</th>
                <th style="width: 20%;">Card/Subtask</th>
                <th style="width: 15%;">Mulai</th>
                <th style="width: 15%;">Selesai</th>
                <th style="width: 15%;">Durasi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($timeLogs as $log)
            <tr>
                <td>{{ $log->user->full_name ?? 'N/A' }}</td>
                <td>
                    @if($log->card && $log->card->board && $log->card->board->project)
                        {{ $log->card->board->project->project_name }}
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td>
                    @if($log->subtask)
                        <strong>Subtask:</strong> {{ \Illuminate\Support\Str::limit($log->subtask->subtask_title, 30) }}
                    @elseif($log->card)
                        <strong>Card:</strong> {{ \Illuminate\Support\Str::limit($log->card->card_title, 30) }}
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td>{{ $log->start_time->format('d/m/Y H:i') }}</td>
                <td>
                    @if($log->end_time)
                        {{ $log->end_time->format('d/m/Y H:i') }}
                    @else
                        <span class="badge badge-warning">Sedang Berlangsung</span>
                    @endif
                </td>
                <td>
                    @if($log->end_time)
                        @php
                            $minutes = $log->end_time->diffInMinutes($log->start_time);
                            $hours = floor($minutes / 60);
                            $mins = $minutes % 60;
                        @endphp
                        <span class="duration">{{ $hours }}h {{ $mins }}m</span>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="no-data">
        Tidak ada data time logs untuk periode yang dipilih
    </div>
    @endif

    <div class="footer">
        <div>© {{ date('Y') }} UKK Project Management System</div>
        <div>Laporan ini dibuat secara otomatis oleh sistem</div>
    </div>
</body>
</html>
