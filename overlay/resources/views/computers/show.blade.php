<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Computer Detail</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 2rem; }
        .status-success { color: #1f9d55; }
        .status-warning { color: #d69e2e; }
        .status-error { color: #e3342f; }
        .status-info, .status-other_unmatched { color: #4a5568; }
        a { color: #2b6cb0; text-decoration: none; }
    </style>
</head>
<body>
    <p><a href="{{ route('dashboard.index') }}">← Zurück zum Dashboard</a></p>

    <h1>{{ $computer->display_name ?: $computer->hostname }}</h1>
    <p><strong>Hostname:</strong> {{ $computer->hostname }}</p>
    <p><strong>Mandant:</strong> {{ $computer->tenant?->name ?: '-' }}</p>
    <p><strong>Status:</strong> {{ strtoupper($computer->last_status) }}</p>

    <h2>Aliasse</h2>
    <ul>
        @forelse ($computer->aliases as $alias)
            <li>{{ $alias->alias }}</li>
        @empty
            <li>Keine Aliasse hinterlegt.</li>
        @endforelse
    </ul>

    <h2>Verlauf der Sicherungsergebnisse</h2>
    <ul>
        @forelse ($computer->backupEvents as $event)
            <li>
                <strong>{{ $event->event_at->toDateTimeString() }}</strong>
                <span class="status-{{ $event->status }}">[{{ strtoupper($event->status) }}]</span>
                {{ $event->summary }}
                @if($event->raw_email_ref)
                    <small>(Originalmail: {{ $event->raw_email_ref }})</small>
                @endif
            </li>
        @empty
            <li>Keine Sicherungsereignisse vorhanden.</li>
        @endforelse
    </ul>
</body>
</html>
