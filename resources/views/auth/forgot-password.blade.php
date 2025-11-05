@extends('layouts.app')

@section('title', 'Forgot password')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h1 class="h4 mb-3">Reset your password</h1>
                        <p class="text-secondary small">Enter your email address and we will send you a password reset link.</p>
                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
                                @error('email')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Send reset link</button>
                        </form>
                        <p class="text-secondary small mt-3"><a href="{{ route('login') }}">Back to login</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
