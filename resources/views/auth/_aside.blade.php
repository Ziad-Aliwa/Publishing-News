<aside class="auth-aside">
    <a class="auth-aside-brand" href="{{ route('posts.index') }}">Publishing News <span>·</span> {{ __('Writers\' room') }}</a>
    <div class="auth-aside-copy">
        <p class="eyebrow"><span class="eyebrow-dot"></span> {{ __('Make space for a good story') }}</p>
        <h2>{{ __('Curiosity is') }}<br>{{ __('where it') }} <em>{{ __('begins.') }}</em></h2>
        <p>{{ __('A quieter place to share what you see, learn from other voices, and leave the conversation a little richer.') }}</p>
    </div>
    <figure class="auth-photo">
        <img src="https://images.unsplash.com/photo-1504711434969-e33886168f5c?auto=format&amp;fit=crop&amp;w=1000&amp;q=85" alt="{{ __('A reader taking time with the day\'s news') }}" loading="eager">
        <figcaption>{{ __('Read closely. Write honestly.') }}</figcaption>
    </figure>
    <span class="auth-aside-footer">{{ __('INDEPENDENT VOICES, CAREFULLY PUBLISHED') }}</span>
</aside>