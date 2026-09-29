@extends('layouts.app')

@section('title', 'Verify email')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <h1 class="h3 mb-3">Verify your email address</h1>
            <p>Use the verification link sent to your email address before publishing or managing posts.</p>
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button class="btn btn-primary" type="submit">Resend verification email</button>
            </form>
        </div>
    </div>
@endsection