<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Cards - {{ $periodTitle }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        table th { background: #f5f5f5; padding: 8px; text-align: left; border: 1px solid #ddd; }
        table td { padding: 6px; border: 1px solid #ddd; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN CARDS</h1>
        <p>Periode: {{ $periodTitle }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>Card</th>
                <th>Project</th>
                <th>Assigned To</th>
                <th>Progress</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cards as $card)
            <tr>
                <td><strong>{{ $card->card_title }}</strong></td>
                <td>{{ $card->board->project->project_name ?? 'N/A' }}</td>
                <td>{{ $card->assignedUser->full_name ?? '-' }}</td>
                <td>
                    @php
                        $total = $card->subtasks->count();
                        $done = $card->subtasks->where('status', 'done')->count();
                        $progress = $total > 0 ? round(($done / $total) * 100) : 0;
                    @endphp
                    {{ $progress }}% ({{ $done }}/{{ $total }})
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
