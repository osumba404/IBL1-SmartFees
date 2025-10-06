@extends('layouts.auth')

@section('title', 'Forgot Password')

@section('content')
<div class="card">
    <div class="card-header text-center">
        <h4>Reset Password</h4>
        <p class="text-muted">Enter your email to receive a password reset link</p>
    </div>
    <div class="card-body">
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('student.password.email') }}">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                       id="email" name="email" value="{{ old('email') }}" required autofocus>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Send Reset Link</button>
            </div>
        </form>
        
        <div class="text-center mt-3">
            <a href="{{ route('student.login') }}" class="text-decoration-none">Back to Login</a>
        </div>
    </div>
</div>
@endsection