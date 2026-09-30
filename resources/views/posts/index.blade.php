@extends('layouts.app')

@section('title', __('Newsroom').' | Publishing News')

@section('content')
    <section class="newsroom-intro" aria-labelledby="newsroom-title">
        <div class="intro-copy">
            <p class="eyebrow"><span class="eyebrow-dot"></span> {{ __('A considered corner of the internet') }}</p>
            <h1 id="newsroom-title">{{ __('Good stories') }}<br><em>{{ __('make room') }}</em> {{ __('for thought.') }}</h1>
            <p class="intro-description">{{ __('A shared newsroom for curious minds, careful reporting, and ideas worth passing on.') }}</p>
            @auth
                @if (auth()->user()->hasVerifiedEmail())
                    <a class="button" href="{{ route('posts.create') }}">{{ __('Start a story') }} <span aria-hidden="true">↗</span></a>
                @else
                    <a class="button button-quiet" href="{{ route('verification.notice') }}">{{ __('Verify your email to publish') }}</a>
                @endif
            @else
                <a class="button" href="{{ route('register') }}">{{ __('Join the conversation') }} <span aria-hidden="true">↗</span></a>
            @endauth
        </div>
        <figure class="intro-visual">
            <img src="https://images.unsplash.com/photo-1504711434969-e33886168f5c?auto=format&amp;fit=crop&amp;w=1400&amp;q=85" alt="{{ __('A reader looking through a newspaper') }}" fetchpriority="high">
            <figcaption><span>{{ __('THE DAILY EDITION') }}</span><span>{{ __('Ideas in good company') }}</span></figcaption>
        </figure>
        <div class="intro-index" aria-hidden="true">01 <span>—</span> {{ __('OPEN TO EVERY VOICE') }}</div>
    </section>

    <section class="feed-section" aria-labelledby="feed-title">
        <div class="section-heading">
            <div>
                <p class="eyebrow">{{ __('Fresh from the desk') }}</p>
                <h2 id="feed-title">{{ __('The latest stories') }}</h2>
            </div>
            <span class="story-count">{{ $posts->count() }} {{ $posts->count() === 1 ? __('story') : __('stories_plural') }}</span>
        </div>

        <div class="feed-layout">
            <div class="story-list">
                @forelse ($posts as $post)
                    <article class="story-row">
                        <div class="story-number">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</div>
                        <div class="story-copy">
                            <div class="story-meta">
                                <span class="story-tag">{{ __('FIELD NOTES') }}</span>
                                <span>{{ $post->user?->name ?? __('Guest contributor') }}</span>
                                <span aria-hidden="true">·</span>
                                <time datetime="{{ $post->created_at->toDateString() }}">{{ $post->created_at->translatedFormat('M j, Y') }}</time>
                            </div>
                            <h3><a href="{{ route('posts.show', $post->id) }}">{{ $post->title }}</a></h3>
                            <p class="story-excerpt">{{ \Illuminate\Support\Str::limit($post->description, 180) }}</p>
                            <div class="feed-interactions" aria-label="{{ __('Story interactions') }}">
                                @include('posts.partials.reaction-control', ['post' => $post])
                                <button class="feed-comments-link" type="button" data-comments-toggle data-comments-url="{{ route('posts.comments.index', $post->id) }}" data-post-title="{{ $post->title }}" data-comments-label="{{ __('Open :count comments on :title') }}" data-tooltip="{{ __('Comments') }}" aria-label="{{ __('Open :count comments on :title', ['count' => $post->comments_count, 'title' => $post->title]) }}" aria-expanded="false" aria-controls="post-comments-{{ $post->id }}">
                                    <svg class="comment-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" aria-hidden="true">
                                        <path d="M8 9h8M8 13h6"></path>
                                        <path d="M18 4a3 3 0 0 1 3 3v8a3 3 0 0 1-3 3h-5l-5 3v-3H6a3 3 0 0 1-3-3V7a3 3 0 0 1 3-3h12z"></path>
                                    </svg>
                                    <span class="comment-count" data-post-comment-count="{{ $post->id }}">{{ $post->comments_count }}</span>
                                </button>
                            </div>
                            <div class="feed-comments-panel" id="post-comments-{{ $post->id }}" hidden></div>
                            <div class="story-actions">
                                <a class="text-link" href="{{ route('posts.show', $post->id) }}">{{ __('Read story') }} <span aria-hidden="true">↗</span></a>
                                @auth
                                    @if (auth()->user()->hasVerifiedEmail() && $post->user_id === auth()->id())
                                        <a class="text-link text-link-muted" href="{{ route('posts.edit', $post->id) }}">{{ __('Edit') }}</a>
                                        <form method="POST" action="{{ route('posts.destroy', $post->id) }}" onsubmit="return confirm(@js(__('Delete this story?')))">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-link text-link-danger" type="submit">{{ __('Delete') }}</button>
                                        </form>
                                    @endif
                                @endauth
                            </div>
                        </div>
                        <a class="story-arrow" href="{{ route('posts.show', $post->id) }}" aria-label="{{ __('Read :title', ['title' => $post->title]) }}">↗</a>
                    </article>
                @empty
                    <div class="empty-state">
                        <span class="empty-mark">✳</span>
                        <h3>{{ __('The page is yours to begin.') }}</h3>
                        <p>{{ __('There are no stories yet. A good first one can start with you.') }}</p>
                        @auth
                            @if (auth()->user()->hasVerifiedEmail())
                                <a class="button" href="{{ route('posts.create') }}">{{ __('Write the first story') }}</a>
                            @endif
                        @else
                            <a class="text-link" href="{{ route('register') }}">{{ __('Create an account to publish') }}</a>
                        @endauth
                    </div>
                @endforelse
            </div>

            <aside class="desk-note">
                <div class="desk-note-top"><span class="eyebrow">{{ __('A note from the desk') }}</span><span class="desk-spark" aria-hidden="true">✳</span></div>
                <h3>{{ __('Every perspective adds a little more light.') }}</h3>
                <p>{{ __('Write what you know. Share what you notice. Let the conversation grow from there.') }}</p>
                <div class="desk-note-bottom">
                    <span>{{ now()->translatedFormat('l, F j') }}</span>
                    @guest
                        <a class="text-link" href="{{ route('register') }}">{{ __('Become a contributor') }} <span aria-hidden="true">↗</span></a>
                    @else
                        @if (auth()->user()->hasVerifiedEmail())
                            <a class="text-link" href="{{ route('posts.create') }}">{{ __('Write a story') }} <span aria-hidden="true">↗</span></a>
                        @endif
                    @endguest
                </div>
            </aside>
        </div>
    </section>
@endsection