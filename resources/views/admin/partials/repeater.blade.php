@php
    $items = old($name, $values ?? []);
    if (is_string($items)) {
        $items = array_filter(array_map('trim', explode("\n", $items)));
    }
    $items = is_array($items) ? $items : [];
    if (count($items) === 0) {
        $items = [null];
    }
    $pair = ($type ?? 'single') === 'pair';
    $fields = $fields ?? ['title', 'body'];
@endphp

<div class="field" data-repeater>
    <label class="label">
        {{ $label }}
        @if (! empty($optional))<span class="opt">Optional</span>@endif
    </label>

    @if (! empty($help))<span class="help" style="margin-top:-2px">{{ $help }}</span>@endif

    <div class="repeater" data-repeater-list>
        @foreach ($items as $item)
            <div class="{{ $pair ? 'repeater-pair' : 'repeater-row' }}" data-repeater-row>
                @if ($pair)
                    <input class="input" type="text" name="{{ $name }}[][{{ $fields[0] }}]"
                           value="{{ is_array($item) ? ($item[$fields[0]] ?? '') : '' }}"
                           placeholder="{{ $placeholders[0] ?? 'Title' }}" aria-label="{{ $label }} item title">
                    <input class="input" type="text" name="{{ $name }}[][{{ $fields[1] }}]"
                           value="{{ is_array($item) ? ($item[$fields[1]] ?? '') : '' }}"
                           placeholder="{{ $placeholders[1] ?? 'Description' }}" aria-label="{{ $label }} item description">
                @else
                    <input class="input" type="text" name="{{ $name }}[]" value="{{ is_array($item) ? '' : $item }}"
                           placeholder="{{ $placeholder ?? 'Add an item' }}" aria-label="{{ $label }} item">
                @endif

                <button class="repeater-remove" type="button" data-remove-row aria-label="Remove this item">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
                </button>
            </div>
        @endforeach
    </div>

    <button class="abtn abtn-sm repeater-add" type="button" data-add-row>+ Add row</button>

    <template data-repeater-template>
        <div class="{{ $pair ? 'repeater-pair' : 'repeater-row' }}" data-repeater-row>
            @if ($pair)
                <input class="input" type="text" name="{{ $name }}[][{{ $fields[0] }}]" placeholder="{{ $placeholders[0] ?? 'Title' }}">
                <input class="input" type="text" name="{{ $name }}[][{{ $fields[1] }}]" placeholder="{{ $placeholders[1] ?? 'Description' }}">
            @else
                <input class="input" type="text" name="{{ $name }}[]" placeholder="{{ $placeholder ?? 'Add an item' }}">
            @endif
            <button class="repeater-remove" type="button" data-remove-row aria-label="Remove this item">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
            </button>
        </div>
    </template>

    @error($name)<span class="error">{{ $message }}</span>@enderror
</div>
