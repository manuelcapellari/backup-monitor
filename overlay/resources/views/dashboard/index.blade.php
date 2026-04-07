<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backup Monitor Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 2rem; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border-bottom: 1px solid #ddd; padding: 0.75rem; text-align: left; }
        .badge { padding: 0.2rem 0.45rem; border-radius: 0.4rem; color: #fff; font-weight: bold; }
        .green { background: #1f9d55; }
        .yellow { background: #d69e2e; }
        .red { background: #e3342f; }
        .gray { background: #6c757d; }
        nav a { margin-right: 1rem; }
        a { color: #2b6cb0; text-decoration: none; }
    </style>
</head>
<body>
    <h1>Backup Monitor</h1>
    <nav>
        <a href="{{ route('dashboard.index') }}">Dashboard</a>
        <a href="{{ route('tenants.index') }}">Mandanten</a>
        <a href="{{ route('mail-accounts.index') }}">Mailkonten</a>
        <a href="{{ route('raw-emails.index') }}">Rohmails</a>
        <a href="{{ route('parser-rules.index') }}">Parser-Regeln</a>
        <a href="{{ route('debug.index') }}">Debug</a>
    </nav>
    <p>Übersicht aller Computer mit letztem Sicherungsstatus.</p>

    <table>
        <thead>
            <tr>
                <th>Alias</th>
                <th>Hostname</th>
                <th>Mandant</th>
                <th>Status</th>
                <th>Letzte Meldung</th>
                <th>Details</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($computers as $computer)
                <tr>
                    <td>{{ $computer->display_name ?: '-' }}</td>
                    <td>{{ $computer->hostname }}</td>
                    <td>{{ $computer->tenant?->name ?: '-' }}</td>
                    <td><span class="badge {{ $computer->last_status }}">{{ strtoupper($computer->last_status) }}</span></td>
                    <td>{{ optional($computer->last_event_at)?->toDateTimeString() ?: '-' }}</td>
                    <td><a href="{{ route('computers.show', $computer) }}">Öffnen</a></td>
                </tr>
            @empty
                <tr><td colspan="6">Keine Computer vorhanden.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $computers->links() }}
</body>
</html>
