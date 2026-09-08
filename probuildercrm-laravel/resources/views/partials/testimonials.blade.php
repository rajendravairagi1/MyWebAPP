@php
    $__testimonials = \App\Models\Testimonial::orderBy('sort_order')->get();
    $__testimonialTag = $testimonialTag ?? 'Trusted by builders';
    $__testimonialHeading = $testimonialHeading ?? 'What builders say';
@endphp
@if ($__testimonials->isNotEmpty())
    <section class="section">
        <div class="container">
            <div style="text-align: center; max-width: 640px; margin: 0 auto var(--space-xl);">
                <span class="tag">{{ $__testimonialTag }}</span>
                <h2 style="margin-top: 12px;">{{ $__testimonialHeading }}</h2>
            </div>

            <div class="testimonial-marquee">
                <div class="testimonial-track">
                    @foreach ($__testimonials->concat($__testimonials) as $t)
                        <div class="card testimonial-card">
                            <div class="testimonial-stars">
                                @for ($s = 0; $s < $t->rating; $s++)@include('partials.icon', ['name' => 'star', 'size' => 15])@endfor
                            </div>
                            <p class="testimonial-quote">&ldquo;{{ $t->quote }}&rdquo;</p>
                            <div class="testimonial-author">
                                <span class="testimonial-avatar">{{ substr($t->author_role, 0, 1) }}</span>
                                <div>
                                    <div class="testimonial-name">{{ $t->author_role }}</div>
                                    @if ($t->author_city)
                                        <div class="testimonial-role">{{ $t->author_city }}, India</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endif
