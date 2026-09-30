@extends('layouts.app')

@section('title', __('Edit story').' | Publishing News')

@section('content')
    <section class="editor-page">
        <a class="back-link" href="{{ route('posts.show', $post->id) }}"><span aria-hidden="true">←</span> {{ __('Back to your story') }}</a>
        <div class="editor-heading">
            <p class="eyebrow"><span class="eyebrow-dot"></span> {{ __('Refine your contribution') }}</p>
            <h1>{{ __('Every story can') }}<br><em>{{ __('find its shape.') }}</em></h1>
            <p>{{ __('Make your changes below. Your voice stays yours.') }}</p>
        </div>
        <form class="editor-form" method="POST" action="{{ route('posts.update', $post->id) }}">
            @csrf
            @method('PUT')
            <div class="field-group">
                <label for="title">{{ __('Story title') }}</label>
                <input id="title" name="title" type="text" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $post->title) }}" required minlength="3" autofocus>
                @error('title') <span class="field-error">{{ $message }}</span> @enderror
            </div>
            <div class="field-group">
                <label for="description">{{ __('Your story') }}</label>
                <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" rows="10" required minlength="10">{{ old('description', $post->description) }}</textarea>
                @error('description') <span class="field-error">{{ $message }}</span> @enderror
                <span class="field-hint">{{ __('Last published :date.', ['date' => $post->updated_at->translatedFormat('F j, Y')]) }}</span>
            </div>
            <div class="form-actions">
                <button class="button" type="submit">{{ __('Save changes') }} <span aria-hidden="true">↗</span></button>
                <a class="text-link text-link-muted" href="{{ route('posts.show', $post->id) }}">{{ __('Cancel') }}</a>
            </div>
        </form>
    </section>
@endsection