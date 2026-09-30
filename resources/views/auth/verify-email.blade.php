@extends('layouts.app')

@section('title', __('Verify your email').' | Publishing News')
@section('page_class', 'auth-page')

@section('content')
    <div class="auth-layout">
        @include('auth._aside')
        <section class="auth-panel" aria-labelledby="auth-title">
            <div class="auth-panel-heading">
                <p class="eyebrow">{{ __('One last step') }}</p>
                <h1 id="auth-title">{{ __('Check your inbox.') }}</h1>
                <p>{{ __('Follow the verification link we sent to') }} <strong>{{ auth()->user()->email }}</strong>. {{ __('Once you are verified, your desk is ready.') }}</p>
            </div>
            <form class="auth-form" method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button class="button button-wide" type="submit">{{ __('Resend verification link') }} <span aria-hidden="true">↗</span></button>
            </form>
            <div class="auth-logout-row">
                <span>{{ __('Wrong account?') }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="text-link" type="submit">{{ __('Log out') }}</button>
                </form>
            </div>
        </section>
    </div>
@endsection