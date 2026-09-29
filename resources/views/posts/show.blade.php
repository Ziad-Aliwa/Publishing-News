@extends('layouts.app')

@section('title', $post->title.' | Publishing News')

@section('content')
    <article class="article-page">
        <a class="back-link" href="{{ route('posts.index') }}"><span aria-hidden="true">←</span> Back to all stories</a>
        <div class="article-grid">
            <div class="article-main">
                <header class="article-header">
                    <p class="eyebrow"><span class="story-tag">FIELD NOTES</span> <span>Issue {{ str_pad((string) $post->id, 3, '0', STR_PAD_LEFT) }}</span></p>
                    <h1>{{ $post->title }}</h1>
                    <div class="article-byline">
                        <span class="byline-avatar">{{ strtoupper(substr($post->user?->name ?? 'G', 0, 1)) }}</span>
                        <span><strong>{{ $post->user?->name ?? 'Guest contributor' }}</strong><small>Published {{ $post->created_at->format('F j, Y') }}</small></span>
                    </div>
                </header>
                <div class="article-body">
                    <p>{{ $post->description }}</p>
                </div>
                @auth
                    @if (auth()->user()->hasVerifiedEmail() && $post->user_id === auth()->id())
                        <div class="article-actions">
                            <a class="button button-quiet" href="{{ route('posts.edit', $post->id) }}">Edit this story</a>
                            <form method="POST" action="{{ route('posts.destroy', $post->id) }}" onsubmit="return confirm('Delete this story?')">
                                @csrf
                                @method('DELETE')
                                <button class="button button-danger" type="submit">Delete story</button>
                            </form>
                        </div>
                    @endif
                @endauth
            </div>
            <aside class="article-aside">
                <span class="eyebrow">The contributor</span>
                <div class="contributor-mark">{{ strtoupper(substr($post->user?->name ?? 'G', 0, 1)) }}</div>
                <h2>{{ $post->user?->name ?? 'Guest contributor' }}</h2>
                <p>Part of a community making space for thoughtful stories and new perspectives.</p>
                <div class="aside-rule"></div>
                <span class="aside-date">{{ $post->created_at->format('M j, Y') }}</span>
            </aside>
        </div>
    </article>
@endsection
