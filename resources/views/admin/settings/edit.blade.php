@extends('layouts.admin')

@section('title', 'Settings')

@section('content')
    <div class="page-head">
        <div>
            <p class="eyebrow">System</p>
            <h1>Site settings.</h1>
            <p class="sub">Identity, contact details, social links and SEO defaults. These values feed the navigation, footer, metadata and contact page.</p>
        </div>
        <div class="head-actions">
            <a class="abtn abtn-ghost" href="{{ route('home') }}" target="_blank" rel="noopener">View site ↗</a>
            <button class="abtn" type="submit" form="settings-form">Save settings</button>
        </div>
    </div>

    <form id="settings-form" method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" data-lock-submit>
        @csrf
        @method('PUT')

        <div class="form-layout">
            <div class="form-main">
                <section class="card">
                    <div class="card-head"><h2>Identity</h2></div>
                    <div class="card-body">
                        <div class="form-grid">
                            <div class="field">
                                <label class="label" for="site_title">Site title</label>
                                <input class="input @error('site_title') is-invalid @enderror" id="site_title" name="site_title" type="text"
                                       value="{{ old('site_title', $settings->site_title) }}" required maxlength="191">
                                @error('site_title')<span class="error">{{ $message }}</span>@enderror
                            </div>

                            <div class="field">
                                <label class="label" for="availability_label">Availability <span class="opt">Optional</span></label>
                                <input class="input" id="availability_label" name="availability_label" type="text"
                                       value="{{ old('availability_label', $settings->availability_label) }}" maxlength="80" placeholder="Open for Q2 2026 projects">
                            </div>

                            <div class="field field--full">
                                <label class="label" for="tagline">Tagline</label>
                                <input class="input" id="tagline" name="tagline" type="text" value="{{ old('tagline', $settings->tagline) }}" maxlength="255"
                                       placeholder="Premium med spa websites & the Laravel systems behind them.">
                                <span class="help">Used in the footer and as the default social description.</span>
                            </div>

                            <div class="field">
                                <label class="label" for="location">Location <span class="opt">Optional</span></label>
                                <input class="input" id="location" name="location" type="text" value="{{ old('location', $settings->location) }}" maxlength="120" placeholder="Dhaka, Bangladesh">
                            </div>

                            <div class="field">
                                <label class="label" for="call_booking_url">Booking URL <span class="opt">Optional</span></label>
                                <input class="input @error('call_booking_url') is-invalid @enderror" id="call_booking_url" name="call_booking_url" type="url"
                                       value="{{ old('call_booking_url', $settings->call_booking_url) }}" maxlength="255" placeholder="https://cal.com/…">
                                <span class="help">Only used if you later add a scheduler. Blank is correct for an inquiry-first site.</span>
                                @error('call_booking_url')<span class="error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                </section>

                <section class="card">
                    <div class="card-head"><h2>Contact</h2></div>
                    <div class="card-body">
                        <div class="form-grid">
                            <div class="field">
                                <label class="label" for="email">Email</label>
                                <input class="input @error('email') is-invalid @enderror" id="email" name="email" type="email"
                                       value="{{ old('email', $settings->email) }}" required maxlength="191">
                                @error('email')<span class="error">{{ $message }}</span>@enderror
                            </div>

                            <div class="field">
                                <label class="label" for="phone">Phone <span class="opt">Optional</span></label>
                                <input class="input" id="phone" name="phone" type="tel" value="{{ old('phone', $settings->phone) }}" maxlength="32" placeholder="+880 1739 387922">
                            </div>
                        </div>
                    </div>
                </section>

                <section class="card">
                    <div class="card-head">
                        <div>
                            <h2>Social links</h2>
                            <p>Rendered in the footer, in this order.</p>
                        </div>
                    </div>
                    <div class="card-body">
                        @include('admin.partials.repeater', [
                            'name' => 'social_links',
                            'label' => 'Links',
                            'type' => 'pair',
                            'fields' => ['label', 'url'],
                            'values' => $settings->social_links ?? [],
                            'placeholders' => ['LinkedIn', 'https://linkedin.com/in/…'],
                        ])
                    </div>
                </section>

                <section class="card">
                    <div class="card-head"><h2>SEO defaults</h2></div>
                    <div class="card-body">
                        <div class="stack">
                            <div class="field">
                                <label class="label" for="seo_title">Default title</label>
                                <input class="input" id="seo_title" name="seo_title" type="text" value="{{ old('seo_title', $settings->seo_title) }}" maxlength="255"
                                       placeholder="Md Naime — UI/UX Designer & Web Developer, Dhaka">
                                <span class="help">Pages with their own title still use their own.</span>
                            </div>

                            <div class="field">
                                <label class="label" for="seo_description">Default description</label>
                                <textarea class="textarea" id="seo_description" name="seo_description" rows="3" maxlength="255"
                                          placeholder="Premium med spa websites and the Laravel systems behind them.">{{ old('seo_description', $settings->seo_description) }}</textarea>
                                @error('seo_description')<span class="error">{{ $message }}</span>@enderror
                            </div>

                            <div class="field">
                                <label class="label" for="og_image">Social share image <span class="opt">Optional</span></label>
                                <div class="image-drop">
                                    <span class="image-preview" id="og-preview" aria-hidden="true">
                                        @if ($settings->ogImageUrl())
                                            <img src="{{ $settings->ogImageUrl() }}" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:7px">
                                        @else
                                            <span>1200×630</span>
                                        @endif
                                    </span>
                                    <input class="file-input" id="og_image" name="og_image" type="file" accept="image/jpeg,image/png,image/webp" data-image-input="#og-preview">
                                </div>
                                <span class="help">Use a 1200×630 JPEG or PNG. Stored as uploaded for social-crawler compatibility.</span>
                                @error('og_image')<span class="error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <aside class="form-side">
                <section class="card">
                    <div class="card-head">
                        <div>
                            <h2>Hero photo</h2>
                            <p>Shown in the homepage hero figure.</p>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="field">
                            <label class="label" for="hero_image">Homepage hero photo <span class="opt">Optional</span></label>
                            <div class="image-drop">
                                <span class="image-preview" id="hero-preview" aria-hidden="true" style="aspect-ratio:3/4;height:auto;max-height:160px">
                                    @if ($settings->heroImageUrl())
                                        <img src="{{ $settings->heroImageUrl() }}" alt="Current hero image" style="width:100%;height:100%;object-fit:cover;border-radius:7px">
                                    @else
                                        <span>3:4</span>
                                    @endif
                                </span>
                                <input class="file-input" id="hero_image" name="hero_image" type="file" accept="image/jpeg,image/png,image/webp,image/avif" data-image-input="#hero-preview">
                            </div>
                            <span class="help">Converted to WebP automatically. Falls back to About portrait if not set.</span>
                            @error('hero_image')<span class="error">{{ $message }}</span>@enderror
                        </div>

                        <div style="margin-top:14px;padding-top:12px;border-top:1px solid var(--border)">
                            <p class="small faint" style="margin-bottom:6px">About section portrait</p>
                            <a class="link small" href="{{ route('admin.about.edit') }}" style="display:inline-flex;align-items:center;gap:4px">
                                Edit About page portrait &rarr;
                            </a>
                        </div>
                    </div>
                </section>

                <section class="card">
                    <div class="card-head"><h2>Brand assets</h2></div>
                    <div class="card-body">
                        <div class="field">
                            <label class="label" for="logo">Logo <span class="opt">Optional</span></label>
                            <div class="image-drop">
                                <span class="image-preview" id="logo-preview" aria-hidden="true">
                                    @if ($settings->logoUrl())
                                        <img src="{{ $settings->logoUrl() }}" alt="" style="width:100%;height:100%;object-fit:contain;border-radius:7px;padding:6px">
                                    @else
                                        <span>LOGO</span>
                                    @endif
                                </span>
                                <input class="file-input" id="logo" name="logo" type="file" accept="image/jpeg,image/png,image/webp" data-image-input="#logo-preview">
                            </div>
                            <span class="help">Replaces the text wordmark in the navigation when set.</span>
                            @error('logo')<span class="error">{{ $message }}</span>@enderror
                        </div>

                        <div class="field" style="margin-top:18px">
                            <label class="label" for="favicon">Favicon <span class="opt">PNG / ICO</span></label>
                            <div class="image-drop">
                                <span class="image-preview" id="favicon-preview" aria-hidden="true" style="width:52px;height:52px">
                                    @if ($settings->faviconUrl())
                                        <img src="{{ $settings->faviconUrl() }}" alt="" style="width:100%;height:100%;object-fit:contain">
                                    @else
                                        <span>ICO</span>
                                    @endif
                                </span>
                                <input class="file-input" id="favicon" name="favicon" type="file" accept="image/png,image/x-icon,image/webp" data-image-input="#favicon-preview">
                            </div>
                            <span class="help">Square, at least 64px. SVG is rejected for security.</span>
                            @error('favicon')<span class="error">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </section>

                <section class="card">
                    <div class="card-body">
                        <button class="abtn abtn-primary abtn-block" type="submit">Save settings</button>
                        <p class="help" style="margin-top:12px">Saving clears the public cache so changes appear immediately.</p>
                    </div>
                </section>
            </aside>
        </div>
    </form>
@endsection
