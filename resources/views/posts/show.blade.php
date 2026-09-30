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

                <section id="comments" class="comments-section" aria-labelledby="comments-title">
                    <div class="comments-heading">
                        <div>
                            <span class="eyebrow">Around the table</span>
                            <h2 id="comments-title">Conversation <span>{{ $post->comments_count }}</span></h2>
                        </div>
                    </div>

                    @auth
                        @if (auth()->user()->hasVerifiedEmail())
                            <form class="comment-composer" method="POST" action="{{ route('posts.comments.store', $post->id) }}">
                                @csrf
                                <label class="visually-hidden" for="new-comment">Add your comment</label>
                                <textarea id="new-comment" name="body" rows="3" maxlength="5000" placeholder="Add something thoughtful to the conversation..." required>{{ old('body') }}</textarea>
                                @error('body') <span class="field-error">{{ $message }}</span> @enderror
                                <div class="composer-footer">
                                    <span>Keep it kind and on topic.</span>
                                    <button class="button button-small" type="submit">Add comment <span aria-hidden="true">↗</span></button>
                                </div>
                            </form>
                        @else
                            <div class="comment-signin-note">Verify your email to join the conversation. <a href="{{ route('verification.notice') }}">Verify email <span aria-hidden="true">↗</span></a></div>
                        @endif
                    @else
                        <div class="comment-signin-note">Have a thought to add? <a href="{{ route('login') }}">Log in to comment <span aria-hidden="true">↗</span></a></div>
                    @endauth

                    <div class="comment-list">
                        @forelse ($post->comments as $comment)
                            <article class="comment-item">
                                <div class="comment-avatar">{{ strtoupper(substr($comment->user->name, 0, 1)) }}</div>
                                <div class="comment-content">
                                    <div class="comment-meta"><strong>{{ $comment->user->name }}</strong><time datetime="{{ $comment->created_at->toIso8601String() }}">{{ $comment->created_at->diffForHumans() }}</time></div>
                                    <p class="comment-body">{{ $comment->body }}</p>
                                    <div class="comment-tools">
                                        @auth
                                            @if (auth()->user()->hasVerifiedEmail())
                                                <details class="reply-disclosure">
                                                    <summary class="reply-trigger"><span class="reply-icon" aria-hidden="true">↩</span> Reply</summary>
                                                    <form class="reply-composer" method="POST" action="{{ route('posts.comments.store', $post->id) }}">
                                                        @csrf
                                                        <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                                        <label class="visually-hidden" for="reply-{{ $comment->id }}">Reply to {{ $comment->user->name }}</label>
                                                        <textarea id="reply-{{ $comment->id }}" name="body" rows="2" maxlength="5000" placeholder="Write a reply..." required>{{ old('parent_id') == $comment->id ? old('body') : '' }}</textarea>
                                                        @error('parent_id') <span class="field-error">{{ $message }}</span> @enderror
                                                        <button class="button button-small" type="submit">Send reply</button>
                                                    </form>
                                                </details>
                                            @endif
                                            @if (auth()->id() === $comment->user_id || auth()->id() === $post->user_id)
                                                <form method="POST" action="{{ route('posts.comments.destroy', [$post->id, $comment->id]) }}" onsubmit="return confirm('Delete this comment and its replies?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="text-link text-link-danger" type="submit">Delete</button>
                                                </form>
                                            @endif
                                        @endauth
                                    </div>

                                    @if ($comment->replies->isNotEmpty())
                                        <div class="reply-list">
                                            @foreach ($comment->replies as $reply)
                                                <article class="comment-item reply-item">
                                                    <div class="comment-avatar">{{ strtoupper(substr($reply->user->name, 0, 1)) }}</div>
                                                    <div class="comment-content">
                                                        <div class="comment-meta"><strong>{{ $reply->user->name }}</strong><time datetime="{{ $reply->created_at->toIso8601String() }}">{{ $reply->created_at->diffForHumans() }}</time></div>
                                                        <p class="comment-body">{{ $reply->body }}</p>
                                                        @auth
                                                            @if (auth()->id() === $reply->user_id || auth()->id() === $post->user_id)
                                                                <form method="POST" action="{{ route('posts.comments.destroy', [$post->id, $reply->id]) }}" onsubmit="return confirm('Delete this reply?')">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button class="text-link text-link-danger" type="submit">Delete</button>
                                                                </form>
                                                            @endif
                                                        @endauth
                                                    </div>
                                                </article>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </article>
                        @empty
                            <p class="comments-empty">No comments yet. Start a thoughtful conversation.</p>
                        @endforelse
                    </div>
                </section>
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
