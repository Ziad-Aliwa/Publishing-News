@php($isReply = $isReply ?? false)

<article class="comment-item {{ $isReply ? 'reply-item' : '' }}" data-comment-id="{{ $comment->id }}">
    <div class="comment-avatar">{{ strtoupper(substr($comment->user->name, 0, 1)) }}</div>
    <div class="comment-content">
        <div class="comment-meta">
            <strong>{{ $comment->user->name }}</strong>
            <time datetime="{{ $comment->created_at->toIso8601String() }}">{{ $comment->created_at->diffForHumans() }}</time>
        </div>
        <p class="comment-body">{{ $comment->body }}</p>

        @if (!$isReply)
            <div class="comment-tools">
                @auth
                    @if (auth()->user()->hasVerifiedEmail())
                        <details class="reply-disclosure">
                            <summary class="reply-trigger"><span class="reply-icon" aria-hidden="true">↩</span> {{ __('Reply') }}</summary>
                            <form class="reply-composer" method="POST" action="{{ route('posts.comments.store', $post->id) }}" data-async-form="comment">
                                @csrf
                                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                <label class="visually-hidden" for="reply-{{ $comment->id }}">{{ __('Reply to :name', ['name' => $comment->user->name]) }}</label>
                                <textarea id="reply-{{ $comment->id }}" name="body" rows="2" maxlength="5000" placeholder="{{ __('Write a reply...') }}" required></textarea>
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
                        @include('posts.partials.comment-item', ['post' => $post, 'comment' => $reply, 'isReply' => true])
                    @endforeach
                </div>
            @endif
        @else
            @auth
                @if (auth()->id() === $comment->user_id || auth()->id() === $post->user_id)
                    <form method="POST" action="{{ route('posts.comments.destroy', [$post->id, $comment->id]) }}" data-async-form="delete-comment" onsubmit="return confirm(@js(__('Delete this reply?')))">
                        @csrf
                        @method('DELETE')
                        <button class="text-link text-link-danger" type="submit">{{ __('Delete') }}</button>
                    </form>
                @endif
            @endauth
        @endif
    </div>
</article>
