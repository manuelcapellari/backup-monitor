<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rohmail Detail</title>
    <style>
        pre { white-space: pre-wrap; background: #f8f8f8; border: 1px solid #ddd; padding: 1rem; }
    </style>
</head>
<body>
    <p><a href="{{ route('raw-emails.index') }}">← Zurück</a></p>
    <h1>Rohmail #{{ $rawEmail->id }}</h1>

    <p><strong>Betreff:</strong> {{ $rawEmail->subject ?: '-' }}</p>
    <p><strong>Von:</strong> {{ $rawEmail->from_address ?: '-' }}</p>
    <p><strong>Message-ID:</strong> {{ $rawEmail->message_id ?: '-' }}</p>
    <p><strong>Empfangen:</strong> {{ optional($rawEmail->received_at)?->toDateTimeString() ?: '-' }}</p>
    <p><strong>Konto:</strong> {{ $rawEmail->mailAccount?->name ?: '-' }}</p>

    <h2>Parser Trace / Audit</h2>
    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
            <tr>
                <th>Zeit</th><th>Regel</th><th>Match</th><th>Reason</th><th>Result</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rawEmail->parserTraces as $trace)
                <tr>
                    <td>{{ $trace->created_at->toDateTimeString() }}</td>
                    <td>{{ $trace->parserRule?->name ?: '-' }}</td>
                    <td>{{ $trace->matched ? 'Ja' : 'Nein' }}</td>
                    <td>{{ $trace->reason ?: '-' }}</td>
                    <td><pre>{{ json_encode($trace->result_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre></td>
                </tr>
            @empty
                <tr><td colspan="5">Noch keine Parser-Traces vorhanden.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Header (JSON)</h2>
    <pre>{{ json_encode($rawEmail->headers_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>

    <h2>Text-Body</h2>
    <pre>{{ $rawEmail->body_text }}</pre>

    @if($rawEmail->body_html)
        <h2>HTML-Body (roh)</h2>
        <pre>{{ $rawEmail->body_html }}</pre>
    @endif
</body>
</html>
