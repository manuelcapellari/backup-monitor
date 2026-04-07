<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mandant {{ $mode === 'create' ? 'anlegen' : 'bearbeiten' }}</title>
</head>
<body>
    <p><a href="{{ route('tenants.index') }}">← Zurück</a></p>
    <h1>Mandant {{ $mode === 'create' ? 'anlegen' : 'bearbeiten' }}</h1>

    <form method="post" action="{{ $mode === 'create' ? route('tenants.store') : route('tenants.update', $tenant) }}">
        @csrf
        @if($mode === 'edit') @method('put') @endif

        <label>Name<br><input type="text" name="name" value="{{ old('name', $tenant->name) }}"></label><br><br>
        <label>Slug<br><input type="text" name="slug" value="{{ old('slug', $tenant->slug) }}"></label><br><br>
        <label><input type="checkbox" name="is_active" value="1" {{ old('is_active', $tenant->is_active) ? 'checked' : '' }}> Aktiv</label><br><br>

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
