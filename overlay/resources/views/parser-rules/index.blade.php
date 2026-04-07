<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parser-Regeln</title>
</head>
<body>
    <p><a href="{{ route('dashboard.index') }}">← Dashboard</a></p>
    <h1>Parser-Regeln</h1>

    @if (session('status'))
        <p>{{ session('status') }}</p>
    @endif

    <p>
        <a href="{{ route('parser-rules.create') }}">Neue Parser-Regel anlegen</a> |
        <a href="{{ route('parser-rules.test-form') }}">Regel testen</a>
    </p>

    <p><strong>Hinweis:</strong> Veeam und Backup Exec sind hier als vordefinierte Regeln angelegt. Du kannst diese als Vorlage nutzen und anpassen.</p>

    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
            <tr>
                <th>Prio</th><th>Name</th><th>Vendor</th><th>Match Feld</th><th>Pattern</th><th>Status</th><th>Aktiv</th><th>Aktion</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rules as $rule)
                <tr>
                    <td>{{ $rule->priority }}</td>
                    <td>{{ $rule->name }}</td>
                    <td>{{ $rule->vendor }}</td>
                    <td>{{ $rule->match_field }}</td>
                    <td><code>{{ $rule->match_pattern }}</code></td>
                    <td>{{ $rule->status }}</td>
                    <td>{{ $rule->is_active ? 'Ja' : 'Nein' }}</td>
                    <td>
                        <a href="{{ route('parser-rules.edit', $rule) }}">Bearbeiten</a>
                        <form method="post" action="{{ route('parser-rules.duplicate', $rule) }}" style="display:inline;">
                            @csrf
                            <button type="submit">Duplizieren</button>
                        </form>
                        <form method="post" action="{{ route('parser-rules.toggle', $rule) }}" style="display:inline;">
                            @csrf
                            <button type="submit">{{ $rule->is_active ? 'Deaktivieren' : 'Aktivieren' }}</button>
                        </form>
                        <form method="post" action="{{ route('parser-rules.move-up', $rule) }}" style="display:inline;">
                            @csrf
                            <button type="submit">↑</button>
                        </form>
                        <form method="post" action="{{ route('parser-rules.move-down', $rule) }}" style="display:inline;">
                            @csrf
                            <button type="submit">↓</button>
                        </form>
                        <form method="post" action="{{ route('parser-rules.destroy', $rule) }}" style="display:inline;" onsubmit="return confirm('Regel wirklich löschen?')">
                            @csrf
                            @method('delete')
                            <button type="submit">Löschen</button>
                        </form>
                        <a href="{{ route('parser-rules.test-form', $rule) }}">Testen</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8">Keine Parser-Regeln vorhanden.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
