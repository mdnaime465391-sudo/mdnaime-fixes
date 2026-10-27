@php
    $editing = $service->exists;
    $action = $editing ? route('admin.services.update', $service) : route('admin.services.store');
@endphp

<form id="service-form" method="POST" action="{{ $action }}" data-lock-submit>
    @csrf
    @if ($editing) @method('PUT') @endif

    <div class="form-layout">
        <div class="form-main">
            <section class="card">
                <div class="card-head"><h2>Service details</h2></div>
                <div class="card-body">
                    <div class="form-grid">
                        <div class="field field--full">
                            <label class="label" for="title">Title</label>
                            <input class="input @error('title') is-invalid @enderror" id="title" name="title" type="text"
                                   value="{{ old('title', $service->title) }}" required maxlength="191" data-slug-source
                                   placeholder="Med Spa Website Design">
                            @error('title')<span class="error">{{ $message }}</span>@enderror
                        </div>

                        <div class="field field--full">
                            <label class="label" for="slug">Anchor slug <span class="opt">/services#…</span></label>
                            <input class="input @error('slug') is-invalid @enderror" id="slug" name="slug" type="text"
                                   value="{{ old('slug', $service->slug) }}" maxlength="191" data-slug-target
                                   placeholder="med-spa-website-design">
                            @error('slug')<span class="error">{{ $message }}</span>@enderror
                        </div>

                        <div class="field field--full">
                            <label class="label" for="short_description">Short description</label>
                            <input class="input @error('short_description') is-invalid @enderror" id="short_description" name="short_description"
                                   type="text" value="{{ old('short_description', $service->short_description) }}" required maxlength="255"
                                   placeholder="Premium clinic websites that build trust and turn visits into consultations.">
                            @error('short_description')<span class="error">{{ $message }}</span>@enderror
                        </div>

                        <div class="field">
                            <label class="label" for="icon">Icon key <span class="opt">Optional</span></label>
                            <input class="input" id="icon" name="icon" type="text" value="{{ old('icon', $service->icon) }}" maxlength="64" placeholder="spa">
                            <span class="help">spa · layout · booking · code</span>
                            @error('icon')<span class="error">{{ $message }}</span>@enderror
                        </div>

                        <div class="field">
                            <label class="label" for="sort_order">Sort order</label>
                            <input class="input" id="sort_order" name="sort_order" type="number" min="0"
                                   value="{{ old('sort_order', $service->sort_order ?? 0) }}">
                        </div>
                    </div>
                </div>
            </section>

            <section class="card">
                <div class="card-head">
                    <div>
                        <h2>Long description</h2>
                        <p>The body copy on the Services page. Keep it specific and short.</p>
                    </div>
                </div>
                <div class="card-body">
                    @include('admin.partials.editor', ['name' => 'long_description', 'label' => 'Long description', 'value' => $service->long_description, 'optional' => true])
                </div>
            </section>

            <section class="card">
                <div class="card-head"><h2>Included features</h2></div>
                <div class="card-body">
                    @include('admin.partials.repeater', [
                        'name' => 'features',
                        'label' => 'Features',
                        'values' => $service->features ?? [],
                        'placeholder' => 'e.g. Booking flow optimised for consultation requests',
                        'help' => 'Each row becomes a bullet in the service card and detail section.',
                    ])
                </div>
            </section>
        </div>

        <aside class="form-side">
            <section class="card">
                <div class="card-head"><h2>Publish</h2></div>
                <div class="card-body">
                    <div class="publish-state">
                        <div>
                            <strong>{{ old('is_published', $service->is_published) ? 'Visible on the site' : 'Draft — hidden' }}</strong>
                            <span>{{ $editing ? 'Public reads are cached for 10 minutes, then refreshed.' : 'You can publish after review.' }}</span>
                        </div>
                        <label class="switch">
                            <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $service->is_published))>
                            <span class="switch-track" aria-hidden="true"></span>
                            <span class="sr-only">Publish this service</span>
                        </label>
                    </div>

                    <button class="abtn abtn-primary abtn-block" type="submit">{{ $editing ? 'Save service' : 'Create service' }}</button>

                    @if ($editing)
                        <dl class="meta-list">
                            <div class="meta-row"><dt>Created</dt><dd>{{ $service->created_at?->format('M j, Y') }}</dd></div>
                            <div class="meta-row"><dt>Updated</dt><dd>{{ $service->updated_at?->diffForHumans() }}</dd></div>
                        </dl>
                    @endif
                </div>
            </section>
        </aside>
    </div>
</form>
