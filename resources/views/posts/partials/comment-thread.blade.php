<section class="comments-section" id="{{ $threadId ?? 'comments-'.$post->id }}" data-comments-thread data-post-id="{{ $post->id }}" aria-labelledby="comments-title-{{ $post->id }}">
    <div class="comments-heading">
        <div>
            <span class="eyebrow">{{ __('Around the table') }}</span>
            <h2 id="comments-title-{{ $post->id }}">{{ __('Conversation') }} <span data-comments-count>{{ $commentsCount }}</span></h2>
        </div>
    </div>

    @auth
        @if (auth()->user()->hasVerifiedEmail())
            <form class="comment-composer" method="POST" action="{{ route('posts.comments.store', $post->id) }}" data-async-form="comment">
                @csrf
                <label class="visually-hidden" for="new-comment-{{ $post->id }}">{{ __('Add your comment') }}</label>
                <textarea id="new-comment-{{ $post->id }}" name="body" rows="3" maxlength="5000" placeholder="{{ __('Add something thoughtful to the conversation...') }}" required></textarea>
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
        @forelse ($comments as $comment)
            @include('posts.partials.comment-item', ['post' => $post, 'comment' => $comment, 'isReply' => false])
        @empty
            <p class="comments-empty">{{ __('No comments yet. Start a thoughtful conversation.') }}</p>
        @endforelse
    </div>
</section>
