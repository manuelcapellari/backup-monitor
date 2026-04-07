<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parser-Regel {{ $mode === 'create' ? 'anlegen' : 'bearbeiten' }}</title>
</head>
<body>
    <p><a href="{{ route('parser-rules.index') }}">← Zurück</a></p>
    <h1>Parser-Regel {{ $mode === 'create' ? 'anlegen' : 'bearbeiten' }}</h1>

    <p><strong>Orientierung:</strong> Veeam/Backup Exec sind als vordefinierte Regeln enthalten.
    Du kannst sie als Vorlage duplizieren und für eigene Sonderfälle anpassen.</p>

    <form method="post" action="{{ $mode === 'create' ? route('parser-rules.store') : route('parser-rules.update', $rule) }}">
        @csrf
        @if($mode === 'edit') @method('put') @endif

        <label>Name<br><input type="text" name="name" value="{{ old('name', $rule->name) }}"></label><br><br>
        <label>Vendor-Label<br><input type="text" name="vendor" value="{{ old('vendor', $rule->vendor) }}"></label><br><br>

        <label>Match Feld<br>
            <select name="match_field">
                @foreach(['from', 'subject', 'body'] as $field)
                    <option value="{{ $field }}" {{ old('match_field', $rule->match_field) === $field ? 'selected' : '' }}>{{ $field }}</option>
                @endforeach
            </select>
        </label><br><br>

        <label>Match Pattern (einfacher contains-match)<br>
            <input type="text" name="match_pattern" value="{{ old('match_pattern', $rule->match_pattern) }}">
        </label><br><br>


        <label>Priorität (kleiner = früher)
            <br><input type="number" name="priority" value="{{ old('priority', $rule->priority) }}" min="1" max="10000">
        </label><br><br>
        <label>Status<br>
            <select name="status">
                @foreach(['success', 'warning', 'error', 'info', 'other_unmatched'] as $status)
                    <option value="{{ $status }}" {{ old('status', $rule->status) === $status ? 'selected' : '' }}>{{ $status }}</option>
                @endforeach
            </select>
        </label><br><br>

        <label>Hostname Regex (optional, erste Gruppe = Hostname)<br>
            <input type="text" name="hostname_regex" value="{{ old('hostname_regex', $rule->hostname_regex) }}">
        </label><br><br>

        <label>Jobname Regex (optional, erste Gruppe = Jobname)<br>
            <input type="text" name="job_name_regex" value="{{ old('job_name_regex', $rule->job_name_regex) }}">
        </label><br><br>

        <label><input type="checkbox" name="is_active" value="1" {{ old('is_active', $rule->is_active) ? 'checked' : '' }}> Aktiv</label><br><br>

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
