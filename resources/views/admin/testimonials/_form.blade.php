@php
    $editing = $testimonial->exists;
    $action = $editing ? route('admin.testimonials.update', $testimonial) : route('admin.testimonials.store');
@endphp

<form id="testimonial-form" method="POST" action="{{ $action }}" enctype="multipart/form-data" data-lock-submit>
    @csrf
    @if ($editing) @method('PUT') @endif

    <div class="form-layout">
        <div class="form-main">
            <section class="card">
                <div class="card-head"><h2>Attribution</h2></div>
                <div class="card-body">
                    <div class="form-grid">
                        <div class="field">
                            <label class="label" for="name">Name</label>
                            <input class="input @error('name') is-invalid @enderror" id="name" name="name" type="text"
                                   value="{{ old('name', $testimonial->name) }}" required maxlength="191" placeholder="Dr. Ayesha Rahman">
                            @error('name')<span class="error">{{ $message }}</span>@enderror
                        </div>

                        <div class="field">
                            <label class="label" for="role">Role <span class="opt">Optional</span></label>
                            <input class="input" id="role" name="role" type="text" value="{{ old('role', $testimonial->role) }}" maxlength="191" placeholder="Founder">
                        </div>

                        <div class="field">
                            <label class="label" for="company">Company <span class="opt">Optional</span></label>
                            <input class="input" id="company" name="company" type="text" value="{{ old('company', $testimonial->company) }}" maxlength="191" placeholder="Lumière Aesthetics">
                        </div>

                        <div class="field">
                            <label class="label" for="sort_order">Sort order</label>
                            <input class="input" id="sort_order" name="sort_order" type="number" min="0"
                                   value="{{ old('sort_order', $testimonial->sort_order ?? 0) }}">
                        </div>

                        <div class="field field--full">
                            <label class="label" for="quote">Quote</label>
                            <textarea class="textarea @error('quote') is-invalid @enderror" id="quote" name="quote" rows="6" required maxlength="1200"
                                      placeholder="What changed for the business, in their words.">{{ old('quote', $testimonial->quote) }}</textarea>
                            <span class="help">Aim for 25–45 words. Keep it in the client's voice.</span>
                            @error('quote')<span class="error">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <aside class="form-side">
            <section class="card">
                <div class="card-head"><h2>Publish</h2></div>
                <div class="card-body">
                    <div class="publish-state">
                        <div>
                            <strong>{{ old('is_published', $testimonial->is_published) ? 'Visible on the site' : 'Draft — hidden' }}</strong>
                            <span>Shown on the home, services and about pages.</span>
                        </div>
                        <label class="switch">
                            <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $testimonial->is_published))>
                            <span class="switch-track" aria-hidden="true"></span>
                            <span class="sr-only">Publish this testimonial</span>
                        </label>
                    </div>

                    <button class="abtn abtn-primary abtn-block" type="submit">{{ $editing ? 'Save testimonial' : 'Create testimonial' }}</button>
                </div>
            </section>

            <section class="card">
                <div class="card-head"><h2>Avatar</h2></div>
                <div class="card-body">
                    <div class="image-drop">
                        <span class="image-preview" id="avatar-preview" aria-hidden="true" style="border-radius:50%">
                            @if ($testimonial->avatarUrl())
                                <img src="{{ $testimonial->avatarUrl() }}" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:50%">
                            @else
                                <span>{{ $testimonial->initials() ?: 'IMG' }}</span>
                            @endif
                        </span>
                        <div style="min-width:0">
                            <label class="label" for="avatar">Photo</label>
                            <input class="file-input" id="avatar" name="avatar" type="file" accept="image/jpeg,image/png,image/webp,image/avif" data-image-input="#avatar-preview">
                        </div>
                    </div>
                    <p class="help" style="margin-top:10px">Square crop works best. Resized to 400px and converted to WebP.</p>
                    @error('avatar')<span class="error">{{ $message }}</span>@enderror
                </div>
            </section>
        </aside>
    </div>
</form>
