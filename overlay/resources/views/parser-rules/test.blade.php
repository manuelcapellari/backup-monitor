<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parser-Regel testen</title>
</head>
<body>
    <p><a href="{{ route('parser-rules.index') }}">← Zurück zu Parser-Regeln</a></p>
    <h1>Parser-Regel testen</h1>

    <form method="post" action="{{ route('parser-rules.test-run') }}">
        @csrf

        <label>Testmodus:</label>
        <label><input type="radio" name="mode" value="single" {{ old('mode', $input['mode']) === 'single' ? 'checked' : '' }}> Einzelregel</label>
        <label><input type="radio" name="mode" value="all" {{ old('mode', $input['mode']) === 'all' ? 'checked' : '' }}> Alle aktiven Regeln</label>
        <br><br>

        <label>Regel auswählen (nur bei Einzelregel)<br>
            <select name="parser_rule_id">
                <option value="">-- bitte wählen --</option>
                @foreach($rules as $rule)
                    <option value="{{ $rule->id }}" {{ (string) old('parser_rule_id', $selectedRule?->id) === (string) $rule->id ? 'selected' : '' }}>
                        [{{ $rule->priority }}] {{ $rule->name }} ({{ $rule->vendor }})
                    </option>
                @endforeach
            </select>
        </label><br><br>

        <label>From<br><input type="text" name="from" value="{{ old('from', $input['from']) }}" style="width: 600px;"></label><br><br>
        <label>Subject<br><input type="text" name="subject" value="{{ old('subject', $input['subject']) }}" style="width: 600px;"></label><br><br>
        <label>Body<br><textarea name="body" rows="12" cols="100">{{ old('body', $input['body']) }}</textarea></label><br><br>

        <button type="submit">Regel testen</button>
    </form>

    @if($errors->any())
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    @if($testResult !== null)
        <h2>Ergebnis (Einzelregel)</h2>
        @if($testResult['matched'])
            <p><strong>Match:</strong> Ja</p>
            <pre>{{ json_encode($testResult['result'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        @else
            <p><strong>Match:</strong> Nein</p>
        @endif
    @endif

    @if($allRuleResults !== null)
        <h2>Ergebnis (Alle Regeln)</h2>
        <table border="1" cellpadding="6" cellspacing="0">
            <thead>
                <tr><th>Regel</th><th>Match</th><th>Result</th></tr>
            </thead>
            <tbody>
                @foreach($allRuleResults as $row)
                    <tr>
                        <td>{{ $row['rule_name'] }} (#{{ $row['rule_id'] }})</td>
                        <td>{{ $row['matched'] ? 'Ja' : 'Nein' }}</td>
                        <td><pre>{{ json_encode($row['result'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
