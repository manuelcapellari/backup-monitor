<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug</title>
</head>
<body>
    <p><a href="{{ route('dashboard.index') }}">← Dashboard</a></p>
    <h1>Debug / Systemstatus</h1>

    <ul>
        @foreach($stats as $key => $value)
            <li><strong>{{ $key }}:</strong> {{ $value }}</li>
        @endforeach
    </ul>

    <h2>Letzte Parser/Ingester Fehler</h2>
    <table border="1" cellpadding="6" cellspacing="0">
        <thead><tr><th>ID</th><th>Zeit</th><th>Betreff</th><th>Fehler</th></tr></thead>
        <tbody>
            @forelse($latestRawErrors as $mail)
                <tr>
                    <td>{{ $mail->id }}</td>
                    <td>{{ $mail->updated_at->toDateTimeString() }}</td>
                    <td>{{ $mail->subject }}</td>
                    <td>{{ $mail->ingest_error }}</td>
                </tr>
            @empty
                <tr><td colspan="4">Keine Fehler.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
