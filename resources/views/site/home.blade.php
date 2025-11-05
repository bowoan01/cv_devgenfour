@extends('layouts.app')

@section('title', 'Devgenfour — Software House')

@section('content')
<section class="hero bg-white">
    <div class="container">
        <div class="row align-items-center gy-4">
            <div class="col-lg-6">
                <h1 class="display-5 fw-bold mb-4">We build digital products that empower your business.</h1>
                <p class="lead text-secondary mb-4">From product discovery to launch, Devgenfour delivers custom software solutions with a focus on quality, collaboration, and innovation.</p>
                <a href="{{ route('contact.show') }}" class="btn btn-primary btn-lg">Let’s Work Together</a>
            </div>
            <div class="col-lg-6">
                <div class="contact-card p-4 bg-white">
                    <h2 class="h5 fw-semibold mb-3">Why teams choose Devgenfour</h2>
                    <ul class="list-unstyled mb-0 text-secondary">
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-primary me-2"></i>Full-stack team for web, mobile, and cloud</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-primary me-2"></i>UX-first approach with measurable outcomes</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-primary me-2"></i>Agile collaboration and transparent communication</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="section-title h3 mb-0">Featured Services</h2>
            <a href="{{ route('services.index') }}" class="btn btn-link">View all</a>
        </div>
        <div class="row g-4">
            @foreach($services as $service)
                <div class="col-md-6 col-lg-3">
                    <div class="value-card p-4 h-100 bg-white">
                        <div class="mb-3"><i class="bi bi-lightning-charge-fill fs-4 text-primary"></i></div>
                        <h3 class="h5">{{ $service->title }}</h3>
                        <p class="text-secondary">{{ $service->short_description }}</p>
                        <a href="{{ route('services.show', $service->slug) }}" class="stretched-link text-decoration-none">Learn more</a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-5 bg-white">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="section-title h3 mb-0">Recent Projects</h2>
            <a href="{{ route('portfolio.index') }}" class="btn btn-link">See portfolio</a>
        </div>
        <div class="row g-4">
            @foreach($projects as $project)
                <div class="col-md-6 col-lg-4">
                    <div class="portfolio-card bg-white h-100 shadow-sm rounded-4">
                        @if($project->cover_image)
                            <img src="{{ asset('storage/'.$project->cover_image) }}" alt="{{ $project->title }}" class="w-100">
                        @endif
                        <div class="p-4">
                            <span class="badge rounded-pill mb-2">{{ $project->category ?? 'Case Study' }}</span>
                            <h3 class="h5">{{ $project->title }}</h3>
                            <p class="text-secondary">{{ \Illuminate\Support\Str::limit($project->summary, 120) }}</p>
                            <a href="{{ route('portfolio.show', $project->slug) }}" class="stretched-link text-decoration-none">View project</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <h2 class="section-title h3 mb-4">Leadership</h2>
        <div class="row g-4">
            @foreach($team as $member)
                <div class="col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100">
                        @if($member->photo_path)
                            <img src="{{ asset('storage/'.$member->photo_path) }}" class="card-img-top" alt="{{ $member->name }}">
                        @endif
                        <div class="card-body">
                            <h3 class="h6 fw-semibold mb-1">{{ $member->name }}</h3>
                            <p class="text-primary small mb-2">{{ $member->role_title }}</p>
                            <p class="text-secondary small">{{ \Illuminate\Support\Str::limit($member->bio, 120) }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
