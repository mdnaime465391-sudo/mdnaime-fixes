@php
    $editorId = $id ?? $name;
    $editorValue = old($name, $value ?? '');
@endphp

<div class="field">
    <label class="label" for="{{ $editorId }}">
        {{ $label }}
        @if (! empty($optional))<span class="opt">Optional</span>@endif
    </label>

    <div class="editor" data-editor>
        <div class="editor-toolbar" role="toolbar" aria-label="{{ $label }} formatting">
            <button type="button" data-cmd="bold" aria-label="Bold" title="Bold"><strong>B</strong></button>
            <button type="button" data-cmd="italic" aria-label="Italic" title="Italic"><em>I</em></button>
            <span class="sep" aria-hidden="true"></span>
            <button type="button" data-cmd="formatBlock" data-value="h2" aria-label="Heading 2" title="Heading 2">H2</button>
            <button type="button" data-cmd="formatBlock" data-value="h3" aria-label="Heading 3" title="Heading 3">H3</button>
            <button type="button" data-cmd="formatBlock" data-value="p" aria-label="Paragraph" title="Paragraph">P</button>
            <span class="sep" aria-hidden="true"></span>
            <button type="button" data-cmd="insertUnorderedList" aria-label="Bulleted list" title="Bulleted list">• List</button>
            <button type="button" data-cmd="insertOrderedList" aria-label="Numbered list" title="Numbered list">1. List</button>
            <button type="button" data-cmd="createLink" aria-label="Add link" title="Add link">Link</button>
            <span class="sep" aria-hidden="true"></span>
            <button type="button" data-cmd="removeFormat" aria-label="Clear formatting" title="Clear formatting">Clear</button>
        </div>

        <div class="editor-body" id="{{ $editorId }}" data-editor-body contenteditable="true" role="textbox" aria-multiline="true" data-placeholder="Write {{ strtolower($label) }}…">{!! $editorValue !!}</div>

        <textarea id="{{ $editorId }}_source" name="{{ $name }}" data-editor-input hidden>{{ $editorValue }}</textarea>
    </div>

    @if (! empty($help))
        <span class="help">{{ $help }}</span>
    @endif
    @error($name)<span class="error">{{ $message }}</span>@enderror
</div>
