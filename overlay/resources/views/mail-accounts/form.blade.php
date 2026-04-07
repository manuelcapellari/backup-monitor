<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mailkonto {{ $mode === 'create' ? 'anlegen' : 'bearbeiten' }}</title>
</head>
<body>
    <p><a href="{{ route('mail-accounts.index') }}">← Zurück</a></p>
    <h1>Mailkonto {{ $mode === 'create' ? 'anlegen' : 'bearbeiten' }}</h1>

    <form method="post" action="{{ $mode === 'create' ? route('mail-accounts.store') : route('mail-accounts.update', $mailAccount) }}">
        @csrf
        @if($mode === 'edit') @method('put') @endif

        <label>Name<br><input type="text" name="name" value="{{ old('name', $mailAccount->name) }}"></label><br><br>

        <label>Mandant<br>
            <select name="tenant_id">
                <option value="">-- global/ungeklärt --</option>
                @foreach($tenants as $tenant)
                    <option value="{{ $tenant->id }}" {{ (string) old('tenant_id', $mailAccount->tenant_id) === (string) $tenant->id ? 'selected' : '' }}>{{ $tenant->name }}</option>
                @endforeach
            </select>
        </label><br><br>

        <label>Protokoll<br>
            <select name="protocol">
                @foreach(['imap' => 'IMAP', 'pop3' => 'POP3'] as $value => $label)
                    <option value="{{ $value }}" {{ old('protocol', $mailAccount->protocol) === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </label><br><br>

        <label>Host<br><input type="text" name="host" value="{{ old('host', $mailAccount->host) }}"></label><br><br>
        <label>Port<br><input type="number" name="port" value="{{ old('port', $mailAccount->port) }}"></label><br><br>

        <label>Verschlüsselung<br>
            <select name="encryption">
                @foreach(['none', 'ssl', 'tls'] as $enc)
                    <option value="{{ $enc }}" {{ old('encryption', $mailAccount->encryption) === $enc ? 'selected' : '' }}>{{ strtoupper($enc) }}</option>
                @endforeach
            </select>
        </label><br><br>

        <label>Benutzername<br><input type="text" name="username" value="{{ old('username', $mailAccount->username) }}"></label><br><br>
        <label>Passwort (wird verschlüsselt gespeichert)<br><input type="password" name="password_plain" value=""></label><br><br>
        <label>Mailbox<br><input type="text" name="mailbox" value="{{ old('mailbox', $mailAccount->mailbox) }}"></label><br><br>
        <label>Intervall Minuten<br><input type="number" name="poll_interval_minutes" value="{{ old('poll_interval_minutes', $mailAccount->poll_interval_minutes) }}"></label><br><br>
        <label><input type="checkbox" name="is_active" value="1" {{ old('is_active', $mailAccount->is_active) ? 'checked' : '' }}> Aktiv</label><br><br>

        <button type="submit">Speichern</button>
    </form>

    @if($errors->any())
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif
</body>
</html>
