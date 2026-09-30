@php
    $viewerReaction = $post->reactions->first()?->reaction;
    $canReact = auth()->check() && auth()->user()->hasVerifiedEmail();
    $reactionTarget = $canReact
        ? route('posts.reaction', $post->id)
        : (auth()->check() ? route('verification.notice') : route('login'));
    $accessMessage = auth()->check() ? __('Verify your email to') : __('Log in to');
    $reactionLabels = ['like' => __('Like'), 'dislike' => __('Dislike')];
@endphp

<div class="vote-shell">
    @if ($canReact)
        <form class="reaction-form interaction-votes" method="POST" action="{{ $reactionTarget }}" data-async-form="reaction">
            @csrf
            @foreach (['like' => $post->likes_count, 'dislike' => $post->dislikes_count] as $reaction => $count)
                <button class="vote-button reaction-{{ $reaction }} {{ $viewerReaction === $reaction ? 'is-selected' : '' }}" type="submit" name="reaction" value="{{ $reaction }}" aria-label="{{ __(':reaction, :count', ['reaction' => $reactionLabels[$reaction], 'count' => $count]) }}" aria-pressed="{{ $viewerReaction === $reaction ? 'true' : 'false' }}" data-tooltip="{{ $reactionLabels[$reaction] }}" data-reaction-label="{{ $reactionLabels[$reaction] }}" data-label-template="{{ __(':reaction, :count') }}">
                    @include('posts.partials.reaction-icon', ['type' => $reaction])
                    <span class="reaction-count">{{ $count }}</span>
                </button>
            @endforeach
        </form>
    @else
        <div class="interaction-votes">
            @foreach (['like' => $post->likes_count, 'dislike' => $post->dislikes_count] as $reaction => $count)
                <a class="vote-button reaction-{{ $reaction }}" href="{{ $reactionTarget }}" aria-label="{{ __(':message :reaction, :count', ['message' => $accessMessage, 'reaction' => $reactionLabels[$reaction], 'count' => $count]) }}" data-tooltip="{{ $reactionLabels[$reaction] }}">
                    @include('posts.partials.reaction-icon', ['type' => $reaction])
                    <span class="reaction-count">{{ $count }}</span>
                </a>
            @endforeach
        </div>
    @endif
</div>
