<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Projects - {{ $periodTitle }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        table th { background: #f5f5f5; padding: 8px; text-align: left; border: 1px solid #ddd; }
        table td { padding: 6px; border: 1px solid #ddd; font-size: 10px; }
        .badge { padding: 2px 6px; border-radius: 3px; font-size: 9px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN PROJECTS</h1>
        <p>Periode: {{ $periodTitle }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>Project</th>
                <th>Created By</th>
                <th>Members</th>
                <th>Deadline</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($projects as $project)
            <tr>
                <td><strong>{{ $project->project_name }}</strong></td>
                <td>{{ $project->creator->full_name }}</td>
                <td>{{ $project->members->count() }} members</td>
                <td>{{ $project->deadline ? date('d M Y', strtotime($project->deadline)) : '-' }}</td>
                <td>
                    <span class="badge">{{ ucfirst($project->status) }}</span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
