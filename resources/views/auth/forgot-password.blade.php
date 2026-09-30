@extends('layouts.app')

@section('title', __('Reset your password').' | Publishing News')
@section('page_class', 'auth-page')

@section('content')
    <div class="auth-layout">
        @include('auth._aside')
        <section class="auth-panel" aria-labelledby="auth-title">
            <div class="auth-panel-heading">
                <p class="eyebrow">{{ __('A small reset') }}</p>
                <h1 id="auth-title">{{ __('Find your way back.') }}</h1>
                <p>{{ __('Share the email on your account and we will send a link to choose a new password.') }}</p>
            </div>
            <form class="auth-form" method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="field-group">
                    <label for="email">{{ __('Email address') }}</label>
                    <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="you@example.com" required autofocus autocomplete="email">
                    @error('email') <span class="field-error">{{ $message }}</span> @enderror
                </div>
                <button class="button button-wide" type="submit">{{ __('Send reset link') }} <span aria-hidden="true">↗</span></button>
            </form>
            <p class="auth-switch"><a href="{{ route('login') }}">{{ __('Return to log in') }}</a></p>
        </section>
    </div>
@endsection