@if ($testimonials->isNotEmpty())
    <div class="testi-grid">
        @foreach ($testimonials as $testimonial)
            <figure class="testi-card reveal">
                <blockquote class="testi-quote">“{{ $testimonial->quote }}”</blockquote>
                <figcaption class="testi-foot">
                    @if ($testimonial->avatarUrl())
                        <img class="testi-avatar" src="{{ $testimonial->avatarUrl() }}" alt="Portrait of {{ $testimonial->name }}" loading="lazy" width="88" height="88">
                    @else
                        <span class="testi-avatar" aria-hidden="true" style="display:grid;place-items:center;font-family:var(--font-mono);font-size:12px;color:var(--muted)">{{ $testimonial->initials() }}</span>
                    @endif
                    <span class="testi-who">
                        <strong>{{ $testimonial->name }}</strong><br>
                        <span class="role">{{ collect([$testimonial->role, $testimonial->company])->filter()->implode(', ') }}</span>
                    </span>
                </figcaption>
            </figure>
        @endforeach
    </div>
@endif
