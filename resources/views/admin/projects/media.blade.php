@php
    $groups = [
        'gallery'   => ['label' => 'Key screens', 'hint' => 'Product and interface screens for the main gallery.'],
        'figma'     => ['label' => 'Figma', 'hint' => 'Frames, components and design files.'],
        'elementor' => ['label' => 'Elementor build', 'hint' => 'The WordPress build in progress or finished.'],
    ];
@endphp

<section class="card" style="margin-top:22px" aria-labelledby="media-heading">
    <div class="card-head">
        <div>
            <h2 id="media-heading">Case-study media</h2>
            <p>Drag tiles to reorder. Captions save on blur; uploads convert to WebP automatically.</p>
        </div>
        <span class="mono small faint" data-file-summary>JPEG · PNG · WebP · AVIF · 4 MB max</span>
    </div>

    <div class="card-body">
        <div data-tabs="project-media">
            <div class="seg" role="tablist" aria-label="Media type" style="margin-bottom:16px">
                @foreach ($groups as $type => $group)
                    <button class="{{ $loop->first ? 'is-active' : '' }}" type="button" data-tab="media-{{ $type }}"
                            role="tab" aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                        {{ $group['label'] }}
                        <span class="count">{{ $project->media->where('type', $type)->count() }}</span>
                    </button>
                @endforeach
            </div>

            @foreach ($groups as $type => $group)
                @php $items = $project->media->where('type', $type); @endphp

                <div data-tab-panel="media-{{ $type }}" role="tabpanel" @if (! $loop->first) hidden @endif>
                    <p class="help" style="margin-bottom:12px">{{ $group['hint'] }}</p>

                    <form method="POST" action="{{ route('admin.projects.media.store', $project) }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="type" value="{{ $type }}">
                        <div class="dropzone" data-dropzone>
                            <div class="dropzone-copy">
                                <span class="dropzone-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 16V4M7 9l5-5 5 5M4 16v3a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-3"/></svg>
                                </span>
                                <div>
                                    <strong>Drop {{ strtolower($group['label']) }} images here</strong>
                                    <span data-file-summary>or click to choose up to 12 files</span>
                                </div>
                            </div>
                            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                                <input class="file-input" type="file" name="files[]" accept="image/jpeg,image/png,image/webp,image/avif" multiple required>
                                <button class="abtn abtn-sm" type="submit">Upload</button>
                            </div>
                        </div>
                    </form>

                    @error('type')<span class="error" style="margin-top:8px">{{ $message }}</span>@enderror
                    @error('files')<span class="error" style="margin-top:8px">{{ $message }}</span>@enderror
                    @error('files.*')<span class="error" style="margin-top:8px">One of the files was rejected: {{ $message }}</span>@enderror

                    @if ($items->isEmpty())
                        <p class="empty-inline" style="margin-top:12px">No {{ strtolower($group['label']) }} images yet. The case study falls back to a generated preview tile.</p>
                    @else
                        <div class="media-grid" style="margin-top:12px" data-media-grid
                             data-reorder-url="{{ route('admin.projects.media.reorder', $project) }}">
                            @foreach ($items as $media)
                                <article class="media-card" draggable="true" data-media-id="{{ $media->id }}">
                                    <span class="media-drag" aria-hidden="true">Drag</span>
                                    <img src="{{ $media->thumbUrl() }}" alt="{{ $media->caption ?: 'Project media' }}" loading="lazy" width="640" height="480">
                                    <button class="media-remove" type="button" aria-label="Remove image"
                                            onclick="if (confirm('Remove this image?')) { document.getElementById('delete-media-{{ $media->id }}').submit(); }">
                                        <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
                                    </button>
                                    <div class="media-card-body">
                                        <label class="sr-only" for="caption-{{ $media->id }}">Caption</label>
                                        <input class="media-caption" id="caption-{{ $media->id }}" type="text"
                                               value="{{ $media->caption }}" maxlength="255" placeholder="Add a caption…"
                                               data-caption-url="{{ route('admin.media.update', $media) }}">
                                    </div>
                                </article>

                                <form id="delete-media-{{ $media->id }}" method="POST" action="{{ route('admin.media.destroy', $media) }}" hidden>
                                    @csrf
                                    @method('DELETE')
                                </form>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>
