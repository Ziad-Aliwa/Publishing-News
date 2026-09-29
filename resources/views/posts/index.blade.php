@extends('layouts.app')

@section('title', 'Newsroom | Publishing News')

@section('content')
    <section class="newsroom-intro" aria-labelledby="newsroom-title">
        <div class="intro-copy">
            <p class="eyebrow"><span class="eyebrow-dot"></span> A considered corner of the internet</p>
            <h1 id="newsroom-title">Good stories<br><em>make room</em> for thought.</h1>
            <p class="intro-description">A shared newsroom for curious minds, careful reporting, and ideas worth passing on.</p>
            @auth
                @if (auth()->user()->hasVerifiedEmail())
                    <a class="button" href="{{ route('posts.create') }}">Start a story <span aria-hidden="true">↗</span></a>
                @else
                    <a class="button button-quiet" href="{{ route('verification.notice') }}">Verify your email to publish</a>
                @endif
            @else
                <a class="button" href="{{ route('register') }}">Join the conversation <span aria-hidden="true">↗</span></a>
            @endauth
        </div>
        <figure class="intro-visual">
            <img src="https://images.unsplash.com/photo-1504711434969-e33886168f5c?auto=format&amp;fit=crop&amp;w=1400&amp;q=85" alt="A reader looking through a newspaper" fetchpriority="high">
            <figcaption><span>THE DAILY EDITION</span><span>Ideas in good company</span></figcaption>
        </figure>
        <div class="intro-index" aria-hidden="true">01 <span>—</span> OPEN TO EVERY VOICE</div>
    </section>

    <section class="feed-section" aria-labelledby="feed-title">
        <div class="section-heading">
            <div>
                <p class="eyebrow">Fresh from the desk</p>
                <h2 id="feed-title">The latest stories</h2>
            </div>
            <span class="story-count">{{ $posts->count() }} {{ \Illuminate\Support\Str::plural('story', $posts->count()) }}</span>
        </div>

        <div class="feed-layout">
            <div class="story-list">
                @forelse ($posts as $post)
                    <article class="story-row">
                        <div class="story-number">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</div>
                        <div class="story-copy">
                            <div class="story-meta">
                                <span class="story-tag">FIELD NOTES</span>
                                <span>{{ $post->user?->name ?? 'Guest contributor' }}</span>
                                <span aria-hidden="true">·</span>
                                <time datetime="{{ $post->created_at->toDateString() }}">{{ $post->created_at->format('M j, Y') }}</time>
                            </div>
                            <h3><a href="{{ route('posts.show', $post->id) }}">{{ $post->title }}</a></h3>
                            <p class="story-excerpt">{{ \Illuminate\Support\Str::limit($post->description, 180) }}</p>
                            <div class="story-actions">
                                <a class="text-link" href="{{ route('posts.show', $post->id) }}">Read story <span aria-hidden="true">↗</span></a>
                                @auth
                                    @if (auth()->user()->hasVerifiedEmail() && $post->user_id === auth()->id())
                                        <a class="text-link text-link-muted" href="{{ route('posts.edit', $post->id) }}">Edit</a>
                                        <form method="POST" action="{{ route('posts.destroy', $post->id) }}" onsubmit="return confirm('Delete this story?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-link text-link-danger" type="submit">Delete</button>
                                        </form>
                                    @endif
                                @endauth
                            </div>
                        </div>
                        <a class="story-arrow" href="{{ route('posts.show', $post->id) }}" aria-label="Read {{ $post->title }}">↗</a>
                    </article>
                @empty
                    <div class="empty-state">
                        <span class="empty-mark">✳</span>
                        <h3>The page is yours to begin.</h3>
                        <p>There are no stories yet. A good first one can start with you.</p>
                        @auth
                            @if (auth()->user()->hasVerifiedEmail())
                                <a class="button" href="{{ route('posts.create') }}">Write the first story</a>
                            @endif
                        @else
                            <a class="text-link" href="{{ route('register') }}">Create an account to publish</a>
                        @endauth
                    </div>
                @endforelse
            </div>

            <aside class="desk-note">
                <div class="desk-note-top"><span class="eyebrow">A note from the desk</span><span class="desk-spark" aria-hidden="true">✳</span></div>
                <h3>Every perspective adds a little more light.</h3>
                <p>Write what you know. Share what you notice. Let the conversation grow from there.</p>
                <div class="desk-note-bottom">
                    <span>{{ now()->format('l, F j') }}</span>
                    @guest
                        <a class="text-link" href="{{ route('register') }}">Become a contributor <span aria-hidden="true">↗</span></a>
                    @else
                        @if (auth()->user()->hasVerifiedEmail())
                            <a class="text-link" href="{{ route('posts.create') }}">Write a story <span aria-hidden="true">↗</span></a>
                        @endif
                    @endguest
                </div>
            </aside>
        </div>
    </section>
@endsection