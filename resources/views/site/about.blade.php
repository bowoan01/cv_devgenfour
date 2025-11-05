@extends('layouts.app')

@section('title', 'About Devgenfour')

@section('content')
<section class="py-5 bg-white">
    <div class="container">
        <div class="row align-items-center gy-4">
            <div class="col-lg-6">
                <h1 class="section-title h2 mb-4">A product team built for growth</h1>
                <p class="lead text-secondary">Devgenfour is a remote-first software house helping companies transform bold ideas into digital products. We partner with founders and product teams to design, build, and ship experiences that delight users.</p>
            </div>
            <div class="col-lg-5 offset-lg-1">
                <div class="value-card p-4 bg-white h-100">
                    <h2 class="h5 fw-semibold mb-3">Core values</h2>
                    <ul class="list-unstyled text-secondary mb-0">
                        <li class="mb-2"><i class="bi bi-gem text-primary me-2"></i>Quality first</li>
                        <li class="mb-2"><i class="bi bi-people text-primary me-2"></i>Collaborative spirit</li>
                        <li class="mb-2"><i class="bi bi-lightbulb text-primary me-2"></i>Continuous innovation</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <h2 class="section-title h3 mb-4">Leadership</h2>
        <div class="row g-4">
            @foreach($team as $member)
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        @if($member->photo_path)
                            <img src="{{ asset('storage/'.$member->photo_path) }}" class="card-img-top" alt="{{ $member->name }}">
                        @endif
                        <div class="card-body">
                            <h3 class="h6 fw-semibold">{{ $member->name }}</h3>
                            <p class="text-primary small mb-2">{{ $member->role_title }}</p>
                            <p class="text-secondary small">{{ \Illuminate\Support\Str::limit($member->bio, 160) }}</p>
                            @if($member->social_links)
                                <div class="d-flex gap-2">
                                    @foreach($member->social_links as $link)
                                        <a href="{{ $link['url'] }}" class="btn btn-sm btn-outline-primary" target="_blank" rel="noopener">{{ $link['label'] ?? 'Link' }}</a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-5 bg-white">
    <div class="container">
        <h2 class="section-title h3 mb-4">Timeline</h2>
        <div class="timeline">
            @forelse($timeline as $event)
                <div class="mb-4">
                    <h3 class="h6 fw-semibold mb-1">{{ $event['title'] ?? 'Milestone' }}</h3>
                    <p class="text-primary small mb-1">{{ $event['year'] ?? '' }}</p>
                    <p class="text-secondary mb-0">{{ $event['description'] ?? '' }}</p>
                </div>
            @empty
                <p class="text-secondary">Timeline content will be available soon.</p>
            @endforelse
        </div>
    </div>
</section>
@endsection
