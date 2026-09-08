@extends('layouts.marketing')

@section('title', 'Frequently Asked Questions')
@section('description', 'Answers to common questions about Pro Builder CRM - setup, security, team access, pricing and more.')

@push('head')
@include('partials.json-ld', ['schemas' => [\App\Support\Seo::faqSchema($faqs->toArray())]])
@endpush

@section('content')

<section class="section-hero">
    <div class="container" style="max-width: 720px;">
        <p class="eyebrow">FAQ</p>
        <h1>Frequently Asked Questions</h1>
    </div>
</section>

<section class="section">
    <div class="container" style="max-width: 760px;" x-data="{ open: 0, visibleCount: 6 }">
        <div style="display: flex; flex-direction: column; gap: 12px;">
            @foreach ($faqs as $index => $faq)
                <div class="card faq-item" x-show="{{ $index }} < visibleCount">
                    <button type="button" class="faq-question" @click="open = open === {{ $index }} ? -1 : {{ $index }}">
                        {{ $faq->question }}
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                             class="faq-chevron" :class="{ open: open === {{ $index }} }">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="faq-answer" x-show="open === {{ $index }}" x-cloak>{{ $faq->answer }}</div>
                </div>
            @endforeach
        </div>

        @if ($faqs->count() > 6)
            <div style="text-align: center; margin-top: var(--space-lg);" x-show="visibleCount < {{ $faqs->count() }}">
                <button type="button" class="btn btn-secondary" @click="visibleCount += 6">Load More</button>
            </div>
        @endif
    </div>
</section>

@include('partials.dashboard-tabs-showcase')

@include('partials.blog-preview')

@include('partials.testimonials')

@endsection
