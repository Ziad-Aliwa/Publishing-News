@extends('layouts.app')

@section('title', $post->title.' | '.__('Publishing News'))

@section('content')
    <article class="article-page">
        <a class="back-link" href="{{ route('posts.index') }}"><span aria-hidden="true">←</span> {{ __('Back to all stories') }}</a>
        <div class="article-grid">
            <div class="article-main">
                <header class="article-header">
                    <p class="eyebrow"><span class="story-tag">{{ __('FIELD NOTES') }}</span> <span>{{ __('Issue :id', ['id' => str_pad((string) $post->id, 3, '0', STR_PAD_LEFT)]) }}</span></p>
                    <h1>{{ $post->title }}</h1>
                    <div class="article-byline">
                        <span class="byline-avatar">{{ strtoupper(substr($post->user?->name ?? 'G', 0, 1)) }}</span>
                        <span><strong>{{ $post->user?->name ?? __('Guest contributor') }}</strong><small>{{ __('Published :date', ['date' => $post->created_at->translatedFormat('F j, Y')]) }}</small></span>
                    </div>
                </header>
                <div class="article-body">
                    <p>{{ $post->description }}</p>
                </div>
                <div class="article-reactions" aria-label="{{ __('React to this story') }}">
                    @include('posts.partials.reaction-control', ['post' => $post])
                </div>
                @auth
                    @if (auth()->user()->hasVerifiedEmail() && $post->user_id === auth()->id())
                        <div class="article-actions">
                            <a class="button button-quiet" href="{{ route('posts.edit', $post->id) }}">{{ __('Edit this story') }}</a>
                            <form method="POST" action="{{ route('posts.destroy', $post->id) }}" onsubmit="return confirm(@js(__('Delete this story?')))">
                                @csrf
                                @method('DELETE')
                                <button class="button button-danger" type="submit">{{ __('Delete story') }}</button>
                            </form>
                        </div>
                    @endif
                @endauth

                <section id="comments" class="comments-section" data-comments-thread data-post-id="{{ $post->id }}" aria-labelledby="comments-title">
                    <div class="comments-heading">
                        <div>
                            <span class="eyebrow">{{ __('Around the table') }}</span>
                            <h2 id="comments-title">{{ __('Conversation') }} <span data-comments-count>{{ $post->comments_count }}</span></h2>
                        </div>
                    </div>

                    @auth
                        @if (auth()->user()->hasVerifiedEmail())
                            <form class="comment-composer" method="POST" action="{{ route('posts.comments.store', $post->id) }}" data-async-form="comment">
                                @csrf
                                <label class="visually-hidden" for="new-comment">{{ __('Add your comment') }}</label>
                                <textarea id="new-comment" name="body" rows="3" maxlength="5000" placeholder="{{ __('Add something thoughtful to the conversation...') }}" required>{{ old('body') }}</textarea>
                                @error('body') <span class="field-error">{{ $message }}</span> @enderror
                                <span class="async-feedback" aria-live="polite"></span>
                                <div class="composer-footer">
                                    <span>{{ __('Keep it kind and on topic.') }}</span>
                                    <button class="button button-small" type="submit">{{ __('Add comment') }} <span aria-hidden="true">↗</span></button>
                                </div>
                            </form>
                        @else
                            <div class="comment-signin-note">{{ __('Verify your email to join the conversation.') }} <a href="{{ route('verification.notice') }}">{{ __('Verify email') }} <span aria-hidden="true">↗</span></a></div>
                        @endif
                    @else
                        <div class="comment-signin-note">{{ __('Have a thought to add?') }} <a href="{{ route('login') }}">{{ __('Log in to comment') }} <span aria-hidden="true">↗</span></a></div>
                    @endauth

                    <div class="comment-list" data-comment-list>
                        @forelse ($post->comments as $comment)
                            <article class="comment-item" data-comment-id="{{ $comment->id }}">
                                <div class="comment-avatar">{{ strtoupper(substr($comment->user->name, 0, 1)) }}</div>
                                <div class="comment-content">
                                    <div class="comment-meta"><strong>{{ $comment->user->name }}</strong><time datetime="{{ $comment->created_at->toIso8601String() }}">{{ $comment->created_at->diffForHumans() }}</time></div>
                                    <p class="comment-body">{{ $comment->body }}</p>
                                    <div class="comment-tools">
                                        @auth
                                            @if (auth()->user()->hasVerifiedEmail())
                                                <details class="reply-disclosure">
                                                    <summary class="reply-trigger"><span class="reply-icon" aria-hidden="true">↩</span> {{ __('Reply') }}</summary>
                                                    <form class="reply-composer" method="POST" action="{{ route('posts.comments.store', $post->id) }}" data-async-form="comment">
                                                        @csrf
                                                        <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                                        <label class="visually-hidden" for="reply-{{ $comment->id }}">{{ __('Reply to :name', ['name' => $comment->user->name]) }}</label>
                                                        <textarea id="reply-{{ $comment->id }}" name="body" rows="2" maxlength="5000" placeholder="{{ __('Write a reply...') }}" required>{{ old('parent_id') == $comment->id ? old('body') : '' }}</textarea>
                                                        @error('parent_id') <span class="field-error">{{ $message }}</span> @enderror
                                                        <span class="async-feedback" aria-live="polite"></span>
                                                        <button class="button button-small" type="submit">{{ __('Send reply') }}</button>
                                                    </form>
                                                </details>
                                            @endif
                                            @if (auth()->id() === $comment->user_id || auth()->id() === $post->user_id)
                                                <form method="POST" action="{{ route('posts.comments.destroy', [$post->id, $comment->id]) }}" data-async-form="delete-comment" onsubmit="return confirm(@js(__('Delete this comment and its replies?')))">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="text-link text-link-danger" type="submit">{{ __('Delete') }}</button>
                                                </form>
                                            @endif
                                        @endauth
                                    </div>

                                    @if ($comment->replies->isNotEmpty())
                                        <div class="reply-list">
                                            @foreach ($comment->replies as $reply)
                                                <article class="comment-item reply-item" data-comment-id="{{ $reply->id }}">
                                                    <div class="comment-avatar">{{ strtoupper(substr($reply->user->name, 0, 1)) }}</div>
                                                    <div class="comment-content">
                                                        <div class="comment-meta"><strong>{{ $reply->user->name }}</strong><time datetime="{{ $reply->created_at->toIso8601String() }}">{{ $reply->created_at->diffForHumans() }}</time></div>
                                                        <p class="comment-body">{{ $reply->body }}</p>
                                                        @auth
                                                            @if (auth()->id() === $reply->user_id || auth()->id() === $post->user_id)
                                                                <form method="POST" action="{{ route('posts.comments.destroy', [$post->id, $reply->id]) }}" data-async-form="delete-comment" onsubmit="return confirm(@js(__('Delete this reply?')))">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button class="text-link text-link-danger" type="submit">{{ __('Delete') }}</button>
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
                            <p class="comments-empty">{{ __('No comments yet. Start a thoughtful conversation.') }}</p>
                        @endforelse
                    </div>
                </section>
            </div>
            <aside class="article-aside">
                <span class="eyebrow">{{ __('The contributor') }}</span>
                <div class="contributor-mark">{{ strtoupper(substr($post->user?->name ?? 'G', 0, 1)) }}</div>
                <h2>{{ $post->user?->name ?? __('Guest contributor') }}</h2>
                <p>{{ __('Part of a community making space for thoughtful stories and new perspectives.') }}</p>
                <div class="aside-rule"></div>
                <span class="aside-date">{{ $post->created_at->translatedFormat('M j, Y') }}</span>
            </aside>
        </div>
    </article>
@endsection
