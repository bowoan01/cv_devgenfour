@extends('layouts.app')

@section('title', 'Services')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-8">
                <h1 class="section-title h2 mb-3">Our Services</h1>
                <p class="lead text-secondary">From concept to launch, we provide end-to-end product development to accelerate your roadmap.</p>
            </div>
        </div>
        <div class="row g-4">
            @foreach($services as $service)
                <div class="col-md-6 col-lg-4">
                    <div class="value-card p-4 bg-white h-100">
                        <div class="mb-3"><i class="bi bi-app-indicator text-primary fs-4"></i></div>
                        <h2 class="h5">{{ $service->title }}</h2>
                        <p class="text-secondary">{{ $service->short_description }}</p>
                        <a href="{{ route('services.show', $service->slug) }}" class="stretched-link text-decoration-none">Discover more</a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
