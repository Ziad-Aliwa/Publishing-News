@extends('layouts.app')

@section('title', 'Log in | Publishing News')
@section('page_class', 'auth-page')

@section('content')
    <div class="auth-layout">
        @include('auth._aside')
        <section class="auth-panel" aria-labelledby="auth-title">
            <div class="auth-panel-heading">
                <p class="eyebrow">Your desk is waiting</p>
                <h1 id="auth-title">Welcome back.</h1>
                <p>Pick up where your curiosity left off.</p>
            </div>
            <form class="auth-form" method="POST" action="{{ route('login') }}">
                @csrf
                <div class="field-group">
                    <label for="email">Email address</label>
                    <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="you@example.com" required autofocus autocomplete="username">
                    @error('email') <span class="field-error">{{ $message }}</span> @enderror
                </div>
                <div class="field-group">
                    <div class="field-label-row"><label for="password">Password</label><a href="{{ route('password.request') }}">Forgot password?</a></div>
                    <input id="password" name="password" type="password" class="form-control @error('password') is-invalid @enderror" placeholder="Your password" required autocomplete="current-password">
                    @error('password') <span class="field-error">{{ $message }}</span> @enderror
                </div>
                <label class="remember-option" for="remember">
                    <input id="remember" name="remember" type="checkbox" value="1">
                    <span>Keep me signed in</span>
                </label>
                <button class="button button-wide" type="submit">Log in <span aria-hidden="true">↗</span></button>
            </form>
            <p class="auth-switch">New around here? <a href="{{ route('register') }}">Create an account</a></p>
        </section>
    </div>
@endsection