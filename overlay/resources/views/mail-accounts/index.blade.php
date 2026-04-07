<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mailkonten</title>
</head>
<body>
    <p><a href="{{ route('dashboard.index') }}">← Dashboard</a></p>
    <h1>Mailkonten</h1>

    @if (session('status'))
        <p>{{ session('status') }}</p>
    @endif

    <p><a href="{{ route('mail-accounts.create') }}">Neues Mailkonto anlegen</a></p>

    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
            <tr>
                <th>Name</th><th>Mandant</th><th>Protokoll</th><th>Server</th><th>User</th><th>Aktiv</th><th>Aktion</th>
            </tr>
        </thead>
        <tbody>
            @forelse($mailAccounts as $account)
                <tr>
                    <td>{{ $account->name }}</td>
                    <td>{{ $account->tenant?->name ?: '-' }}</td>
                    <td>{{ strtoupper($account->protocol) }}</td>
                    <td>{{ $account->host }}:{{ $account->port }}</td>
                    <td>{{ $account->username }}</td>
                    <td>{{ $account->is_active ? 'Ja' : 'Nein' }}</td>
                    <td><a href="{{ route('mail-accounts.edit', $account) }}">Bearbeiten</a></td>
                </tr>
            @empty
                <tr><td colspan="7">Keine Mailkonten vorhanden.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
