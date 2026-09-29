@extends('layouts.app')

@section('title', 'Log in')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <h1 class="h3 mb-4">Log in</h1>
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus autocomplete="username">
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input id="password" name="password" type="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="current-password">
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="form-check mb-3">
                    <input id="remember" name="remember" type="checkbox" class="form-check-input" value="1">
                    <label for="remember" class="form-check-label">Remember me</label>
                </div>
                <button class="btn btn-primary" type="submit">Log in</button>
                <a class="btn btn-link" href="{{ route('password.request') }}">Forgot password?</a>
            </form>
        </div>
    </div>
@endsection