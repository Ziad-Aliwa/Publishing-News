@extends('layouts.app')

@section('title', __('Log in').' | Publishing News')
@section('page_class', 'auth-page')

@section('content')
    <div class="auth-layout">
        @include('auth._aside')
        <section class="auth-panel" aria-labelledby="auth-title">
            <div class="auth-panel-heading">
                <p class="eyebrow">{{ __('Your desk is waiting') }}</p>
                <h1 id="auth-title">{{ __('Welcome back.') }}</h1>
                <p>{{ __('Pick up where your curiosity left off.') }}</p>
            </div>
            <form class="auth-form" method="POST" action="{{ route('login') }}">
                @csrf
                <div class="field-group">
                    <label for="email">{{ __('Email address') }}</label>
                    <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="you@example.com" required autofocus autocomplete="username">
                    @error('email') <span class="field-error">{{ $message }}</span> @enderror
                </div>
                <div class="field-group">
                    <div class="field-label-row"><label for="password">{{ __('Password') }}</label><a href="{{ route('password.request') }}">{{ __('Forgot password?') }}</a></div>
                    <input id="password" name="password" type="password" class="form-control @error('password') is-invalid @enderror" placeholder="{{ __('Your password') }}" required autocomplete="current-password">
                    @error('password') <span class="field-error">{{ $message }}</span> @enderror
                </div>
                <label class="remember-option" for="remember">
                    <input id="remember" name="remember" type="checkbox" value="1">
                    <span>{{ __('Keep me signed in') }}</span>
                </label>
                <button class="button button-wide" type="submit">{{ __('Log in') }} <span aria-hidden="true">↗</span></button>
            </form>
            <p class="auth-switch">{{ __('New around here?') }} <a href="{{ route('register') }}">{{ __('Create an account') }}</a></p>
        </section>
    </div>
@endsection