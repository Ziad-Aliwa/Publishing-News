@extends('layouts.app')

@section('title', 'Forgot password')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <h1 class="h3 mb-3">Reset your password</h1>
            <p>Enter your email address and we will send you a password reset link.</p>
            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus autocomplete="email">
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <button class="btn btn-primary" type="submit">Send reset link</button>
            </form>
        </div>
    </div>
@endsection