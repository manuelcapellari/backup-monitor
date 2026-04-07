<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mandanten</title>
</head>
<body>
    <p><a href="{{ route('dashboard.index') }}">← Dashboard</a></p>
    <h1>Mandanten</h1>

    @if (session('status'))
        <p>{{ session('status') }}</p>
    @endif

    <p><a href="{{ route('tenants.create') }}">Neuen Mandanten anlegen</a></p>

    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
            <tr><th>Name</th><th>Slug</th><th>Aktiv</th><th>Aktion</th></tr>
        </thead>
        <tbody>
            @forelse($tenants as $tenant)
                <tr>
                    <td>{{ $tenant->name }}</td>
                    <td>{{ $tenant->slug }}</td>
                    <td>{{ $tenant->is_active ? 'Ja' : 'Nein' }}</td>
                    <td><a href="{{ route('tenants.edit', $tenant) }}">Bearbeiten</a></td>
                </tr>
            @empty
                <tr><td colspan="4">Keine Mandanten vorhanden.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
