@extends('layouts.public')

@section('content')
    <section class="section page-hero" data-od-id="page-hero">
        <div class="container">
            <p class="eyebrow reveal">CONTACT</p>
            <h1 class="split" data-split style="margin-top:var(--gap-lg)">Tell me about the project.</h1>
            <p class="lead reveal" style="margin-top:var(--gap-lg);max-width:54ch">Share a few details and I will come back with a clear scope, a timeline and a fixed quote.</p>
        </div>
    </section>

    <section class="section" id="start" style="padding-top:0" data-od-id="inquiry">
        <div class="container grid-lead">
            <div>
                @php $sent = session('inquiry_sent'); @endphp

                <form class="stack" data-inquiry-form method="POST" action="{{ route('contact.store') }}" aria-label="Project inquiry form" @if ($sent) hidden @endif>
                    @csrf

                    <div class="form-status {{ $errors->any() ? 'is-on' : '' }}" data-form-status role="alert">
                        @if ($errors->any())
                            Please check the highlighted fields and try again.
                        @endif
                    </div>

                    <div class="form-grid">
                        <div class="field @error('name') has-error @enderror">
                            <label for="name">Your name</label>
                            <input class="input" id="name" name="name" type="text" autocomplete="name" placeholder="Jordan Reyes"
                                   value="{{ old('name') }}" required aria-describedby="name-err">
                            <span class="err" id="name-err">{{ $errors->first('name') }}</span>
                        </div>

                        <div class="field @error('email') has-error @enderror">
                            <label for="email">Email</label>
                            <input class="input" id="email" name="email" type="email" autocomplete="email" placeholder="you@clinic.com"
                                   value="{{ old('email') }}" required aria-describedby="email-err">
                            <span class="err" id="email-err">{{ $errors->first('email') }}</span>
                        </div>

                        <div class="field @error('business_type') has-error @enderror">
                            <label for="business_type">Business type</label>
                            <select class="select" id="business_type" name="business_type" aria-describedby="business_type-err">
                                <option value="">Select one</option>
                                @foreach (\App\Http\Requests\Site\InquiryRequest::BUSINESS_TYPES as $type)
                                    <option value="{{ $type }}" @selected(old('business_type') === $type)>{{ $type }}</option>
                                @endforeach
                            </select>
                            <span class="err" id="business_type-err">{{ $errors->first('business_type') }}</span>
                        </div>

                        <div class="field @error('service') has-error @enderror">
                            <label for="service">Service interested in</label>
                            <select class="select" id="service" name="service" aria-describedby="service-err">
                                <option value="">Select one</option>
                                @foreach ($services as $service)
                                    <option value="{{ $service->title }}" @selected(old('service') === $service->title)>{{ $service->title }}</option>
                                @endforeach
                                <option value="Not sure yet" @selected(old('service') === 'Not sure yet')>Not sure yet</option>
                            </select>
                            <span class="err" id="service-err">{{ $errors->first('service') }}</span>
                        </div>

                        <div class="field field--full @error('budget') has-error @enderror">
                            <label for="budget">Budget range</label>
                            <select class="select" id="budget" name="budget" aria-describedby="budget-err">
                                <option value="">Select a range</option>
                                @foreach (\App\Http\Requests\Site\InquiryRequest::BUDGETS as $budget)
                                    <option value="{{ $budget }}" @selected(old('budget') === $budget)>{{ $budget }}</option>
                                @endforeach
                            </select>
                            <span class="err" id="budget-err">{{ $errors->first('budget') }}</span>
                        </div>

                        <div class="field field--full @error('message') has-error @enderror">
                            <label for="message">Project details</label>
                            <textarea class="textarea" id="message" name="message" placeholder="A sentence or two about the clinic or app, the timeline and what success looks like."
                                      required aria-describedby="message-err">{{ old('message') }}</textarea>
                            <span class="err" id="message-err">{{ $errors->first('message') }}</span>
                            <span class="hint">No newsletters, no spam — this goes straight to my inbox.</span>
                        </div>
                    </div>

                    <div class="field" aria-hidden="true" style="position:absolute;left:-9999px;width:1px;height:1px;overflow:hidden">
                        <label for="website">Leave this field empty</label>
                        <input id="website" name="website" type="text" tabindex="-1" autocomplete="off">
                    </div>

                    <button class="btn btn-primary btn-lg" type="submit" data-magnetic>Send inquiry</button>
                </form>

                <div class="form-success {{ $sent ? 'is-on' : '' }}" data-inquiry-success aria-live="polite" @if (! $sent) hidden @endif>
                    <span class="success-mark" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 12.5l5 5L20 6.5"/></svg>
                    </span>
                    <h2>Inquiry received.</h2>
                    <p class="lead" style="margin-inline:auto">Thanks — I have your details and will reply within one business day with next steps and a couple of times to talk.</p>
                    <p class="meta" data-success-summary style="border:1px solid var(--border);border-radius:var(--radius);padding:10px 16px">{{ $sent ?: 'Project inquiry' }}</p>
                    <button class="btn btn-secondary" type="button" data-form-again>Send another inquiry</button>
                </div>
            </div>

            <aside class="stack" aria-label="Contact details">
                <div class="fact-list">
                    <div class="fact"><span class="k">Email</span><span class="v"><a href="mailto:{{ $settings?->email }}">{{ $settings?->email }}</a></span></div>
                    @if ($settings?->phone)
                        <div class="fact"><span class="k">Phone</span><span class="v"><a href="{{ $settings->telHref() }}">{{ $settings->phone }}</a></span></div>
                    @endif
                    <div class="fact"><span class="k">Based in</span><span class="v">{{ $settings?->location ?: 'Dhaka, Bangladesh' }}</span></div>
                    <div class="fact"><span class="k">Timezone</span><span class="v">GMT+6 · Dhaka</span></div>
                    <div class="fact"><span class="k">Response time</span><span class="v">Within 1 business day</span></div>
                </div>

                <div class="card" style="padding:var(--gap-lg);border:1px solid var(--border);border-radius:var(--radius-lg);background:var(--surface)">
                    <p class="kicker">WHAT HAPPENS NEXT</p>
                    <ol class="stack" style="margin-top:var(--gap-md)">
                        <li class="meta" style="color:var(--fg)">01 — I read your inquiry and reply with questions or a call link.</li>
                        <li class="meta" style="color:var(--fg)">02 — We talk for 30 minutes; I scope the work and the timeline.</li>
                        <li class="meta" style="color:var(--fg)">03 — You get a fixed quote and a start date. No pressure.</li>
                    </ol>
                </div>

                <p class="meta muted">Not ready to talk yet? <a class="link-underline js-nav" href="{{ route('projects.index') }}">Look through the projects</a>.</p>
            </aside>
        </div>
    </section>
@endsection
