@php
    $eyebrow = $eyebrow ?? 'NEXT STEP';
    $heading = $heading ?? "Let's build the site that books your next patient.";
    $lead = $lead ?? 'Tell me about the clinic or the app. You will get a clear plan, a realistic timeline and a fixed quote.';
    $secondary = $secondary ?? true;
    $secondaryLabel = $secondaryLabel ?? 'See projects';
    $secondaryHref = $secondaryHref ?? route('projects.index');
@endphp

<section class="section cta-band" data-od-id="cta">
    <div class="cta-glow" aria-hidden="true"></div>
    <div class="container" style="text-align:center">
        <p class="eyebrow reveal" style="margin-bottom:var(--gap-md)">{{ $eyebrow }}</p>
        <h2 class="reveal" style="max-width:20ch;margin-inline:auto">{{ $heading }}</h2>
        <p class="lead reveal" style="margin:var(--gap-md) auto 0;max-width:52ch">{{ $lead }}</p>
        <div class="cta-actions reveal" style="justify-content:center">
            <a class="btn btn-primary btn-lg" href="{{ route('contact') }}" data-magnetic>Start a project</a>
            @if ($secondary)
                <a class="btn btn-secondary btn-lg js-nav" href="{{ $secondaryHref }}">{{ $secondaryLabel }}</a>
            @endif
        </div>
    </div>
</section>
