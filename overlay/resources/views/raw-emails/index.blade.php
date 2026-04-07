<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rohmails</title>
</head>
<body>
    <p><a href="{{ route('dashboard.index') }}">← Dashboard</a></p>
    <h1>Rohmails</h1>

    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
            <tr>
                <th>Zeit</th><th>Mandant</th><th>Konto</th><th>Von</th><th>Betreff</th><th>Status</th><th>Aktion</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rawEmails as $mail)
                <tr>
                    <td>{{ optional($mail->received_at)?->toDateTimeString() ?: '-' }}</td>
                    <td>{{ $mail->tenant?->name ?: '-' }}</td>
                    <td>{{ $mail->mailAccount?->name ?: '-' }}</td>
                    <td>{{ $mail->from_address ?: '-' }}</td>
                    <td>{{ $mail->subject ?: '-' }}</td>
                    <td>{{ $mail->ingest_status }}</td>
                    <td><a href="{{ route('raw-emails.show', $mail) }}">Öffnen</a></td>
                </tr>
            @empty
                <tr><td colspan="7">Keine Rohmails vorhanden.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $rawEmails->links() }}
</body>
</html>
