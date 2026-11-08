<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Weekly maintenance report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; }
        h1 { font-size: 20px; margin-bottom: 4px; }
        h2 { font-size: 15px; margin-top: 24px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background: #f0f0f0; }
        .muted { color: #777; }
    </style>
</head>
<body>
    <h1>{{ config('app.name') }}: Weekly maintenance report</h1>
    <p class="muted">{{ $periodStart->toFormattedDateString() }} to {{ $periodEnd->toFormattedDateString() }}</p>

    @foreach ([
        'Pending' => $pending,
        'In progress' => $inProgress,
        'Resolved this week' => $resolved,
    ] as $title => $maintenances)
        <h2>{{ $title }} ({{ $maintenances->count() }})</h2>

        @if ($maintenances->isEmpty())
            <p class="muted">Nothing here.</p>
        @else
            <table>
                <tr>
                    <th>#</th>
                    <th>Room</th>
                    <th>Issue</th>
                    <th>Reported</th>
                    <th>Last update</th>
                </tr>
                @foreach ($maintenances as $maintenance)
                    <tr>
                        <td>{{ $maintenance->id }}</td>
                        <td>{{ $maintenance->room->floor }}{{ $maintenance->room->room_number }}</td>
                        <td>{{ $maintenance->issue_description }}</td>
                        <td>{{ $maintenance->created_at->toDateString() }}</td>
                        <td>{{ $maintenance->updated_at->toDateString() }}</td>
                    </tr>
                @endforeach
            </table>
        @endif
    @endforeach
</body>
</html>