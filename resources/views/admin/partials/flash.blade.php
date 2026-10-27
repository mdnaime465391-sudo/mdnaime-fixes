@if (session('status'))
    <div class="flash flash--ok" role="status">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 12.5l5 5L20 6.5"/></svg>
        <span>{{ session('status') }}</span>
    </div>
@endif

@if ($errors->any())
    <div class="flash flash--bad" role="alert">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 8v5M12 17v.01"/><circle cx="12" cy="12" r="9"/></svg>
        <div>
            <strong>Please fix {{ $errors->count() === 1 ? 'one field' : $errors->count().' fields' }}.</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
