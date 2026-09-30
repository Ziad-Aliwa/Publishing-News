@extends('layouts.app')

@section('title', __('Join the newsroom').' | Publishing News')
@section('page_class', 'auth-page')

@section('content')
    <div class="auth-layout">
        @include('auth._aside')
        <section class="auth-panel" aria-labelledby="auth-title">
            <div class="auth-panel-heading">
                <p class="eyebrow">{{ __('A seat at the table') }}</p>
                <h1 id="auth-title">{{ __('Join the newsroom.') }}</h1>
                <p>{{ __('Bring your point of view. We\'ll save you a space.') }}</p>
            </div>
            <form class="auth-form" method="POST" action="{{ route('register') }}">
                @csrf
                <div class="field-group">
                    <label for="name">{{ __('Your name') }}</label>
                    <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="{{ __('How should we address you?') }}" required autofocus autocomplete="name">
                    @error('name') <span class="field-error">{{ $message }}</span> @enderror
                </div>
                <div class="field-group">
                    <label for="email">{{ __('Email address') }}</label>
                    <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="you@example.com" required autocomplete="username">
                    @error('email') <span class="field-error">{{ $message }}</span> @enderror
                </div>
                <div class="field-group">
                    <label for="password">{{ __('Password') }}</label>
                    <input id="password" name="password" type="password" class="form-control @error('password') is-invalid @enderror" placeholder="{{ __('At least 8 characters') }}" required autocomplete="new-password">
                    @error('password') <span class="field-error">{{ $message }}</span> @enderror
                </div>
                <div class="field-group">
                    <label for="password_confirmation">{{ __('Confirm password') }}</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" placeholder="{{ __('Enter it once more') }}" required autocomplete="new-password">
                </div>
                <button class="button button-wide" type="submit">{{ __('Create my account') }} <span aria-hidden="true">↗</span></button>
            </form>
            <p class="auth-switch">{{ __('Already a member?') }} <a href="{{ route('login') }}">{{ __('Log in') }}</a></p>
        </section>
    </div>
@endsection