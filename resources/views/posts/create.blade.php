@extends('layouts.app')

@section('title', __('Write a story').' | Publishing News')

@section('content')
    <section class="editor-page">
        <a class="back-link" href="{{ route('posts.index') }}"><span aria-hidden="true">←</span> {{ __('Back to the newsroom') }}</a>
        <div class="editor-heading">
            <p class="eyebrow"><span class="eyebrow-dot"></span> {{ __('New contribution') }}</p>
            <h1>{{ __('Give your idea') }}<br><em>{{ __('a place to land.') }}</em></h1>
            <p>{{ __('A clear title and a few considered lines are a good place to begin.') }}</p>
        </div>
        <form class="editor-form" method="POST" action="{{ route('posts.store') }}">
            @csrf
            <div class="field-group">
                <label for="title">{{ __('Story title') }}</label>
                <input id="title" name="title" type="text" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="{{ __('A title that invites someone in') }}" required minlength="3" autofocus>
                @error('title') <span class="field-error">{{ $message }}</span> @enderror
            </div>
            <div class="field-group">
                <label for="description">{{ __('Your story') }}</label>
                <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" rows="10" placeholder="{{ __('Start with what matters most...') }}" required minlength="10">{{ old('description') }}</textarea>
                @error('description') <span class="field-error">{{ $message }}</span> @enderror
                <span class="field-hint">{{ __('Write in your own voice. You can always come back and refine it.') }}</span>
            </div>
            <div class="form-actions">
                <button class="button" type="submit">{{ __('Publish story') }} <span aria-hidden="true">↗</span></button>
                <a class="text-link text-link-muted" href="{{ route('posts.index') }}">{{ __('Cancel') }}</a>
            </div>
        </form>
    </section>
@endsection