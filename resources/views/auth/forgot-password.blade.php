@extends('layouts.app')

@section('title', 'Reset your password | Publishing News')
@section('page_class', 'auth-page')

@section('content')
    <div class="auth-layout">
        @include('auth._aside')
        <section class="auth-panel" aria-labelledby="auth-title">
            <div class="auth-panel-heading">
                <p class="eyebrow">A small reset</p>
                <h1 id="auth-title">Find your way back.</h1>
                <p>Share the email on your account and we'll send a link to choose a new password.</p>
            </div>
            <form class="auth-form" method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="field-group">
                    <label for="email">Email address</label>
                    <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="you@example.com" required autofocus autocomplete="email">
                    @error('email') <span class="field-error">{{ $message }}</span> @enderror
                </div>
                <button class="button button-wide" type="submit">Send reset link <span aria-hidden="true">↗</span></button>
            </form>
            <p class="auth-switch"><a href="{{ route('login') }}">Return to log in</a></p>
        </section>
    </div>
@endsection