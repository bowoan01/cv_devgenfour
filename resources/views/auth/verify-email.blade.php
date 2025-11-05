@extends('layouts.app')

@section('title', 'Verify email')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 text-center">
                        <h1 class="h4 mb-3">Verify your email</h1>
                        <p class="text-secondary">We have sent a verification link to <strong>{{ auth()->user()->email }}</strong>. Click the link to activate your account.</p>
                        @if (session('status') == 'verification-link-sent')
                            <div class="alert alert-success" role="alert">
                                A new verification link has been sent to your email address.
                            </div>
                        @endif
                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100">Resend verification email</button>
                        </form>
                        <form method="POST" action="{{ route('logout') }}" class="mt-3">
                            @csrf
                            <button type="submit" class="btn btn-link text-decoration-none">Log out</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
