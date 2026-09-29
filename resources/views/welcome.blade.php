@extends('layouts.app')

@section('title', 'Publishing News | A thoughtful newsroom')
@section('page_class', 'welcome-page')

@section('content')
    <section class="welcome-hero" aria-labelledby="welcome-title">
        <div class="welcome-copy">
            <p class="eyebrow"><span class="eyebrow-dot"></span> An open space for good stories</p>
            <h1 id="welcome-title">Make room for<br><em>what matters.</em></h1>
            <p class="welcome-description">Publishing News is a shared newsroom for curious minds, thoughtful reporting, and ideas worth passing on.</p>
            <div class="welcome-actions">
                <a class="button" href="{{ route('posts.index') }}">Explore the newsroom <span aria-hidden="true">↗</span></a>
                @guest
                    <a class="text-link" href="{{ route('register') }}">Find your voice</a>
                @endguest
            </div>
            <div class="welcome-edition"><span class="edition-indicator"></span><span>OPEN EDITION</span><span class="edition-divider"></span><span>{{ now()->format('F Y') }}</span></div>
        </div>
        <figure class="welcome-visual">
            <img src="https://images.unsplash.com/photo-1504711434969-e33886168f5c?auto=format&amp;fit=crop&amp;w=1600&amp;q=85" alt="A reader taking a moment with the day's news" fetchpriority="high">
            <figcaption><span>READ CLOSELY</span><span>Leave space for a new perspective.</span></figcaption>
            <span class="visual-index" aria-hidden="true">PN / 01</span>
        </figure>
    </section>

    <section class="welcome-footnote" aria-label="About the newsroom">
        <p>Independent voices, carefully published.</p>
        <a class="text-link" href="{{ route('posts.index') }}">Step inside the newsroom <span aria-hidden="true">→</span></a>
    </section>
@endsection