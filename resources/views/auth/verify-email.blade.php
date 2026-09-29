@extends('layouts.app')

@section('title', 'Verify your email | Publishing News')
@section('page_class', 'auth-page')

@section('content')
    <div class="auth-layout">
        @include('auth._aside')
        <section class="auth-panel" aria-labelledby="auth-title">
            <div class="auth-panel-heading">
                <p class="eyebrow">One last step</p>
                <h1 id="auth-title">Check your inbox.</h1>
                <p>Follow the verification link we sent to <strong>{{ auth()->user()->email }}</strong>. Once you're verified, your desk is ready.</p>
            </div>
            <form class="auth-form" method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button class="button button-wide" type="submit">Resend verification link <span aria-hidden="true">↗</span></button>
            </form>
            <div class="auth-logout-row">
                <span>Wrong account?</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="text-link" type="submit">Log out</button>
                </form>
            </div>
        </section>
    </div>
@endsection