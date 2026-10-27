@extends('layouts.admin')

@section('title', 'About page')

@section('content')
    <div class="page-head">
        <div>
            <p class="eyebrow">Content</p>
            <h1>The About page.</h1>
            <p class="sub">Edit the story, principles and toolkit — and switch whole sections on or off without touching code.</p>
        </div>
        <div class="head-actions">
            <a class="abtn abtn-ghost" href="{{ route('about') }}" target="_blank" rel="noopener">View page ↗</a>
            <button class="abtn" type="submit" form="about-form">Save about page</button>
        </div>
    </div>

    <form id="about-form" method="POST" action="{{ route('admin.about.update') }}" enctype="multipart/form-data" data-lock-submit>
        @csrf
        @method('PUT')

        <div class="form-layout">
            <div class="form-main">
                <section class="card">
                    <div class="card-head">
                        <div>
                            <h2>Hero</h2>
                            <p>The first screen. Keep the headline to one strong sentence.</p>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-grid">
                            <div class="field field--full">
                                <label class="label" for="eyebrow">Eyebrow</label>
                                <input class="input" id="eyebrow" name="eyebrow" type="text" value="{{ old('eyebrow', $page->eyebrow) }}" required maxlength="80">
                                @error('eyebrow')<span class="error">{{ $message }}</span>@enderror
                            </div>

                            <div class="field field--full">
                                <label class="label" for="headline">Headline</label>
                                <input class="input @error('headline') is-invalid @enderror" id="headline" name="headline" type="text"
                                       value="{{ old('headline', $page->headline) }}" required maxlength="191">
                                @error('headline')<span class="error">{{ $message }}</span>@enderror
                            </div>

                            <div class="field field--full">
                                <label class="label" for="intro">Intro <span class="opt">Max 500 characters</span></label>
                                <textarea class="textarea" id="intro" name="intro" rows="3" maxlength="500">{{ old('intro', $page->intro) }}</textarea>
                                @error('intro')<span class="error">{{ $message }}</span>@enderror
                            </div>

                            <div class="field">
                                <label class="label" for="availability_label">Availability note <span class="opt">Optional</span></label>
                                <input class="input" id="availability_label" name="availability_label" type="text"
                                       value="{{ old('availability_label', $page->availability_label) }}" maxlength="80" placeholder="Available for new projects">
                            </div>

                            <div class="field">
                                <label class="label" for="portrait">Portrait <span class="opt">Optional</span></label>
                                <div class="image-drop">
                                    <span class="image-preview" id="portrait-preview" aria-hidden="true">
                                        @if ($page->portraitUrl())
                                            <img src="{{ $page->portraitUrl() }}" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:7px">
                                        @else
                                            <span>3:4</span>
                                        @endif
                                    </span>
                                    <input class="file-input" id="portrait" name="portrait" type="file" accept="image/jpeg,image/png,image/webp,image/avif" data-image-input="#portrait-preview">
                                </div>
                                @error('portrait')<span class="error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                </section>

                <section class="card">
                    <div class="card-head">
                        <div>
                            <h2>Story</h2>
                            <p>Who you are and how you work. Two or three short paragraphs is enough.</p>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="stack">
                            <div class="field">
                                <label class="label" for="story_heading">Story heading</label>
                                <input class="input" id="story_heading" name="story_heading" type="text"
                                       value="{{ old('story_heading', $page->story_heading) }}" maxlength="191">
                            </div>

                            @include('admin.partials.editor', ['name' => 'story', 'label' => 'Story', 'value' => $page->story, 'optional' => true])
                        </div>
                    </div>
                </section>

                <section class="card">
                    <div class="card-head">
                        <div>
                            <h2>Principles</h2>
                            <p>Three short beliefs that explain how you work.</p>
                        </div>
                    </div>
                    <div class="card-body">
                        @include('admin.partials.repeater', [
                            'name' => 'principles',
                            'label' => 'Principles',
                            'type' => 'pair',
                            'fields' => ['title', 'body'],
                            'values' => $page->principles ?? [],
                            'placeholders' => ['Clarity before decoration', 'One sentence on why it matters.'],
                        ])
                    </div>
                </section>

                <section class="card">
                    <div class="card-head">
                        <div>
                            <h2>Toolkit</h2>
                            <p>The grid on the About page. Keep it to tools you can defend in a call.</p>
                        </div>
                    </div>
                    <div class="card-body">
                        @include('admin.partials.repeater', [
                            'name' => 'toolkit',
                            'label' => 'Tools',
                            'type' => 'pair',
                            'fields' => ['name', 'role'],
                            'values' => $page->toolkit ?? [],
                            'placeholders' => ['Figma', 'UI/UX design'],
                        ])
                    </div>
                </section>

                <section class="card">
                    <div class="card-head"><h2>SEO</h2></div>
                    <div class="card-body">
                        <div class="stack">
                            <div class="field">
                                <label class="label" for="seo_title">SEO title <span class="opt">Optional</span></label>
                                <input class="input" id="seo_title" name="seo_title" type="text" value="{{ old('seo_title', $page->seo_title) }}" maxlength="255">
                            </div>
                            <div class="field">
                                <label class="label" for="seo_description">SEO description <span class="opt">Optional</span></label>
                                <textarea class="textarea" id="seo_description" name="seo_description" rows="3" maxlength="500">{{ old('seo_description', $page->seo_description) }}</textarea>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <aside class="form-side">
                <section class="card">
                    <div class="card-head">
                        <div>
                            <h2>Visible sections</h2>
                            <p>Switch sections off without deleting content.</p>
                        </div>
                    </div>
                    <div class="card-body">
                        @php
                            $switches = [
                                'show_story' => ['Story', 'The story and portrait block.'],
                                'show_principles' => ['Principles', 'How you work.'],
                                'show_toolkit' => ['Toolkit', 'Tools and platforms.'],
                                'show_testimonials' => ['Testimonials', 'Published client quotes.'],
                                'show_cta' => ['Closing CTA', 'Start-a-project band at the end.'],
                            ];
                        @endphp

                        @foreach ($switches as $field => $copy)
                            <div class="publish-state" @if ($loop->last) style="border-bottom:0;margin-bottom:0;padding-bottom:4px" @endif>
                                <div>
                                    <strong>{{ $copy[0] }}</strong>
                                    <span>{{ $copy[1] }}</span>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" name="{{ $field }}" value="1" @checked(old($field, $page->{$field} ?? true))>
                                    <span class="switch-track" aria-hidden="true"></span>
                                    <span class="sr-only">Show {{ $copy[0] }}</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </section>

                <section class="card">
                    <div class="card-body">
                        <button class="abtn abtn-primary abtn-block" type="submit">Save about page</button>
                        <p class="help" style="margin-top:12px">The About page cache clears on save.</p>
                        @if ($page->exists)
                            <dl class="meta-list">
                                <div class="meta-row"><dt>Updated</dt><dd>{{ $page->updated_at?->diffForHumans() }}</dd></div>
                                <div class="meta-row"><dt>Toolkit</dt><dd>{{ count($page->toolkit ?? []) }} items</dd></div>
                            </dl>
                        @endif
                    </div>
                </section>
            </aside>
        </div>
    </form>
@endsection
