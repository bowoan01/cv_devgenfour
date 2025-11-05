@extends('layouts.app')

@section('title', 'Contact Devgenfour')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row gy-4 align-items-start">
            <div class="col-lg-5">
                <h1 class="section-title h2 mb-3">Let’s build together</h1>
                <p class="text-secondary">Share your goals and we’ll schedule a strategy session to explore the right solution.</p>
                <div class="mt-4">
                    <h2 class="h6 text-uppercase text-secondary">Contact details</h2>
                    <ul class="list-unstyled text-secondary">
                        <li class="mb-2"><i class="bi bi-geo-alt text-primary me-2"></i>{{ $office['address'] }}</li>
                        <li class="mb-2"><i class="bi bi-envelope text-primary me-2"></i>{{ $office['email'] }}</li>
                        <li class="mb-2"><i class="bi bi-telephone text-primary me-2"></i>{{ $office['phone'] }}</li>
                    </ul>
                    <iframe class="rounded-4 shadow-sm w-100" height="280" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3958.646852374397!2d112.732!3d-7.1709" allowfullscreen loading="lazy"></iframe>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="contact-card p-4 bg-white">
                    <form action="{{ route('contact.submit') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Company</label>
                                <input type="text" name="company" class="form-control" value="{{ old('company') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Project details</label>
                                <textarea name="message" rows="5" class="form-control" required>{{ old('message') }}</textarea>
                            </div>
                        </div>
                        <div class="mt-4 d-flex justify-content-between align-items-center">
                            <span class="text-secondary small">Protected by rate limiting and spam filters.</span>
                            <button type="submit" class="btn btn-primary">Send message</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
