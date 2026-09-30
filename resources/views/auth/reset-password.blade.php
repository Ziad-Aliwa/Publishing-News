@extends('layouts.app')

@section('title', __('Choose a new password').' | Publishing News')
@section('page_class', 'auth-page')

@section('content')
    <div class="auth-layout">
        @include('auth._aside')
        <section class="auth-panel" aria-labelledby="auth-title">
            <div class="auth-panel-heading">
                <p class="eyebrow">{{ __('A fresh start') }}</p>
                <h1 id="auth-title">{{ __('Choose a new password.') }}</h1>
                <p>{{ __('Set a new password for your account. Make it one only you know.') }}</p>
            </div>
            <form class="auth-form" method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <div class="field-group">
                    <label for="email">{{ __('Email address') }}</label>
                    <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $email) }}" placeholder="you@example.com" required autocomplete="email">
                    @error('email') <span class="field-error">{{ $message }}</span> @enderror
                </div>
                <div class="field-group">
                    <label for="password">{{ __('New password') }}</label>
                    <input id="password" name="password" type="password" class="form-control @error('password') is-invalid @enderror" placeholder="{{ __('At least 8 characters') }}" required autocomplete="new-password">
                    @error('password') <span class="field-error">{{ $message }}</span> @enderror
                </div>
                <div class="field-group">
                    <label for="password_confirmation">{{ __('Confirm new password') }}</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" placeholder="{{ __('Enter it once more') }}" required autocomplete="new-password">
                </div>
                <button class="button button-wide" type="submit">{{ __('Save new password') }} <span aria-hidden="true">↗</span></button>
            </form>
            <p class="auth-switch"><a href="{{ route('login') }}">{{ __('Return to log in') }}</a></p>
        </section>
    </div>
@endsection