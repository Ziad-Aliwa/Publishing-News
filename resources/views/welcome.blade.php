@extends('layouts.app')

@section('title', 'Publishing News | '.__('A thoughtful newsroom'))
@section('page_class', 'welcome-page')

@section('content')
    <section class="welcome-hero" aria-labelledby="welcome-title">
        <div class="welcome-copy">
            <p class="eyebrow"><span class="eyebrow-dot"></span> {{ __('An open space for good stories') }}</p>
            <h1 id="welcome-title">{{ __('Make room for') }}<br><em>{{ __('what matters.') }}</em></h1>
            <p class="welcome-description">{{ __('Publishing News is a shared newsroom for curious minds, thoughtful reporting, and ideas worth passing on.') }}</p>
            <div class="welcome-actions">
                <a class="button" href="{{ route('posts.index') }}">{{ __('Explore the newsroom') }} <span aria-hidden="true">↗</span></a>
                @guest
                    <a class="text-link" href="{{ route('register') }}">{{ __('Find your voice') }}</a>
                @endguest
            </div>
            <div class="welcome-edition"><span class="edition-indicator"></span><span>{{ __('OPEN EDITION') }}</span><span class="edition-divider"></span><span>{{ now()->translatedFormat('F Y') }}</span></div>
        </div>
        <figure class="welcome-visual">
            <img src="https://images.unsplash.com/photo-1504711434969-e33886168f5c?auto=format&amp;fit=crop&amp;w=1600&amp;q=85" alt="{{ __('A reader taking a moment with the day\'s news') }}" fetchpriority="high">
            <figcaption><span>{{ __('READ CLOSELY') }}</span><span>{{ __('Leave space for a new perspective.') }}</span></figcaption>
            <span class="visual-index" aria-hidden="true">PN / 01</span>
        </figure>
    </section>

    <section class="welcome-footnote" aria-label="{{ __('About the newsroom') }}">
        <p>{{ __('Independent voices, carefully published.') }}</p>
        <a class="text-link" href="{{ route('posts.index') }}">{{ __('Step inside the newsroom') }} <span aria-hidden="true">→</span></a>
    </section>
@endsection