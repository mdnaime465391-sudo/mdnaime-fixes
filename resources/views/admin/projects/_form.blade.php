@php
    $editing = $project->exists;
    $action = $editing ? route('admin.projects.update', $project) : route('admin.projects.store');
    $catValue = old('categories', $project->categories ?? []);
    $catValue = is_array($catValue) ? implode(', ', $catValue) : (string) $catValue;
    $accent = old('accent_color', $project->accent_color ?: '');
@endphp

<form id="project-form" method="POST" action="{{ $action }}" enctype="multipart/form-data" data-lock-submit>
    @csrf
    @if ($editing)
        @method('PUT')
    @endif

    <div class="form-layout">
        <div class="form-main">
            <div class="card" data-tabs="project-fields">
                <div class="tabs" role="tablist" aria-label="Project fields">
                    <button class="tab is-active" type="button" data-tab="overview" role="tab" aria-selected="true" aria-controls="panel-overview">Overview</button>
                    <button class="tab" type="button" data-tab="narrative" role="tab" aria-selected="false" aria-controls="panel-narrative">Story</button>
                    <button class="tab" type="button" data-tab="outcome" role="tab" aria-selected="false" aria-controls="panel-outcome">Outcome</button>
                    <button class="tab" type="button" data-tab="preview" role="tab" aria-selected="false" aria-controls="panel-preview">Live preview</button>
                </div>

                <div class="card-body">
                    <div id="panel-overview" data-tab-panel="overview" role="tabpanel">
                        <div class="form-grid">
                            <div class="field field--full">
                                <label class="label" for="title">Project title</label>
                                <input class="input @error('title') is-invalid @enderror" id="title" name="title" type="text"
                                       value="{{ old('title', $project->title) }}" required maxlength="191"
                                       data-slug-source placeholder="Lumière Aesthetics">
                                @error('title')<span class="error">{{ $message }}</span>@enderror
                            </div>

                            <div class="field field--full">
                                <label class="label" for="slug">URL slug <span class="opt">/projects/…</span></label>
                                <input class="input @error('slug') is-invalid @enderror" id="slug" name="slug" type="text"
                                       value="{{ old('slug', $project->slug) }}" maxlength="191" data-slug-target
                                       placeholder="lumiere-aesthetics">
                                <span class="help">Generated from the title until you edit it. Lowercase letters, numbers and hyphens.</span>
                                @error('slug')<span class="error">{{ $message }}</span>@enderror
                            </div>

                            <div class="field field--full">
                                <label class="label" for="summary">One-line summary <span class="opt">Case-study hero</span></label>
                                <input class="input @error('summary') is-invalid @enderror" id="summary" name="summary" type="text"
                                       value="{{ old('summary', $project->summary) }}" maxlength="255"
                                       placeholder="A calm, conversion-led site for a med spa in Gulshan.">
                                <span class="help">Shown under the case-study title. Keep it to one sentence.</span>
                                @error('summary')<span class="error">{{ $message }}</span>@enderror
                            </div>

                            <div class="field field--full">
                                <label class="label" for="categories">Categories <span class="opt">Comma separated</span></label>
                                <input class="input @error('categories') is-invalid @enderror" id="categories" name="categories" type="text"
                                       value="{{ $catValue }}" list="category-options" placeholder="Med Spa Websites, Figma UI/UX"
                                       aria-describedby="categories-help">
                                <datalist id="category-options">
                                    @foreach ($categories as $category)
                                        <option value="{{ $category }}"></option>
                                    @endforeach
                                </datalist>
                                <span class="help" id="categories-help">These become the filter chips on the Projects page.</span>
                                @error('categories')<span class="error">{{ $message }}</span>@enderror
                                @error('categories.*')<span class="error">{{ $message }}</span>@enderror
                            </div>

                            <div class="field">
                                <label class="label" for="accent_color">Card accent <span class="opt">Optional</span></label>
                                <div style="display:flex;gap:8px;align-items:center">
                                    <span aria-hidden="true" style="width:34px;height:34px;flex:none;border-radius:7px;border:1px solid var(--border);background:{{ $accent ?: 'oklch(80% 0.125 76)' }}"></span>
                                    <input class="input @error('accent_color') is-invalid @enderror" id="accent_color" name="accent_color" type="text"
                                           value="{{ $accent }}" placeholder="oklch(80% 0.125 76)">
                                </div>
                                <span class="help">Used by the generated preview tile when there is no cover image.</span>
                                @error('accent_color')<span class="error">{{ $message }}</span>@enderror
                            </div>

                            <div class="field">
                                <label class="label" for="sort_order">Sort order <span class="opt">Lower first</span></label>
                                <input class="input" id="sort_order" name="sort_order" type="number" min="0"
                                       value="{{ old('sort_order', $project->sort_order ?? 0) }}">
                                @error('sort_order')<span class="error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>

                    <div id="panel-narrative" data-tab-panel="narrative" role="tabpanel" hidden>
                        <div class="stack">
                            @include('admin.partials.editor', ['name' => 'background', 'label' => 'Background', 'value' => $project->background, 'help' => 'The business, the market and why the project existed.', 'optional' => true])

                            @include('admin.partials.editor', ['name' => 'challenge', 'label' => 'The challenge', 'value' => $project->challenge, 'optional' => true])
                            @include('admin.partials.editor', ['name' => 'design_approach', 'label' => 'Design approach', 'value' => $project->design_approach, 'optional' => true])
                        </div>
                    </div>

                    <div id="panel-outcome" data-tab-panel="outcome" role="tabpanel" hidden>
                        <div class="stack">
                            @include('admin.partials.repeater', [
                                'name' => 'features',
                                'label' => 'Features',
                                'values' => $project->features ?? [],
                                'placeholder' => 'e.g. Online consultation requests',
                                'help' => 'One feature per row. Rendered as a numbered list on the case study.',
                            ])

                            @include('admin.partials.repeater', [
                                'name' => 'tech_stack',
                                'label' => 'Tech stack',
                                'values' => $project->tech_stack ?? [],
                                'placeholder' => 'e.g. Laravel 12',
                                'help' => 'Short labels shown as tags.',
                            ])

                            @include('admin.partials.editor', ['name' => 'solution', 'label' => 'The solution', 'value' => $project->solution, 'optional' => true])
                            @include('admin.partials.editor', ['name' => 'result', 'label' => 'Result / projected outcome', 'value' => $project->result, 'optional' => true])
                        </div>
                    </div>

                    <div id="panel-preview" data-tab-panel="preview" role="tabpanel" hidden>
                        <div class="form-grid">
                            <div class="field field--full">
                                <label class="label" for="live_preview_url">Live preview URL <span class="opt">Optional</span></label>
                                <input class="input @error('live_preview_url') is-invalid @enderror" id="live_preview_url" name="live_preview_url" type="url"
                                       value="{{ old('live_preview_url', $project->live_preview_url) }}" maxlength="255"
                                       placeholder="https://app.clinic.com">
                                <span class="help">When set, the case study shows a browser frame with the live app. Sites that block iframes fall back to an “Open live app” card.</span>
                                @error('live_preview_url')<span class="error">{{ $message }}</span>@enderror
                            </div>

                            <div class="field field--full">
                                <label class="label" for="demo_credentials">Demo credentials <span class="opt">Optional</span></label>
                                <input class="input" id="demo_credentials" name="demo_credentials" type="text"
                                       value="{{ old('demo_credentials', $project->demo_credentials) }}" maxlength="255"
                                       placeholder="demo@clinic.com / demo1234">
                                <span class="help">Shown in the preview card. Do not use real client credentials.</span>
                            </div>

                            <div class="field field--full">
                                <label class="label" for="demo_notes">Demo notes <span class="opt">Optional</span></label>
                                <textarea class="textarea" id="demo_notes" name="demo_notes" rows="4" maxlength="2000"
                                          placeholder="Use the staff login to see the scheduling dashboard. Data resets nightly.">{{ old('demo_notes', $project->demo_notes) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <aside class="form-side" aria-label="Publish and media">
            <section class="card">
                <div class="card-head"><h2>Publish</h2></div>
                <div class="card-body">
                    <div class="publish-state">
                        <div>
                            <strong>{{ old('is_published', $project->is_published) ? 'Visible on the site' : 'Draft — hidden' }}</strong>
                            <span>{{ $project->published_at ? 'First published '.$project->published_at->format('M j, Y') : 'Not published yet' }}</span>
                        </div>
                        <label class="switch">
                            <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $project->is_published))>
                            <span class="switch-track" aria-hidden="true"></span>
                            <span class="sr-only">Publish this project</span>
                        </label>
                    </div>

                    <button class="abtn abtn-primary abtn-block" type="submit">{{ $editing ? 'Save changes' : 'Create project' }}</button>

                    @if ($editing && $project->is_published)
                        <a class="abtn abtn-ghost abtn-block abtn-sm" href="{{ route('projects.show', $project->slug) }}" target="_blank" rel="noopener" style="margin-top:8px">Open the case study ↗</a>
                    @endif

                    @if ($editing)
                        <dl class="meta-list">
                            <div class="meta-row"><dt>Created</dt><dd>{{ $project->created_at?->format('M j, Y') }}</dd></div>
                            <div class="meta-row"><dt>Updated</dt><dd>{{ $project->updated_at?->diffForHumans() }}</dd></div>
                            <div class="meta-row"><dt>Media</dt><dd>{{ $project->media->count() }} files</dd></div>
                        </dl>
                    @endif
                </div>
            </section>

            <section class="card">
                <div class="card-head"><h2>Cover</h2></div>
                <div class="card-body">
                    <div class="image-drop">
                        <span class="image-preview" id="cover-preview" aria-hidden="true">
                            @if ($project->coverUrl())
                                <img src="{{ $project->coverUrl() }}" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:7px">
                            @else
                                <span>3:4</span>
                            @endif
                        </span>
                        <div style="min-width:0">
                            <label class="label" for="cover_image">Cover image</label>
                            <input class="file-input" id="cover_image" name="cover_image" type="file" accept="image/jpeg,image/png,image/webp,image/avif"
                                   data-image-input="#cover-preview">
                        </div>
                    </div>
                    <p class="help" style="margin-top:10px">Resized to 1920px and converted to WebP. A 640px thumbnail is generated automatically.</p>
                    @error('cover_image')<span class="error">{{ $message }}</span>@enderror

                    <div class="field" style="margin-top:14px">
                        <label class="label" for="thumbnail">Custom thumbnail <span class="opt">Optional</span></label>
                        <input class="file-input" id="thumbnail" name="thumbnail" type="file" accept="image/jpeg,image/png,image/webp,image/avif">
                        <span class="help">Only if the auto crop does not work for this project.</span>
                        @error('thumbnail')<span class="error">{{ $message }}</span>@enderror
                    </div>
                </div>
            </section>
        </aside>
    </div>
</form>
