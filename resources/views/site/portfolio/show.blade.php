@extends('layouts.app')

@section('title', $project->title)

@section('content')
<section class="py-5 bg-white">
    <div class="container">
        <div class="row gy-4">
            <div class="col-lg-8">
                <span class="badge rounded-pill bg-primary-subtle text-primary mb-3">{{ $project->category ?? 'Case Study' }}</span>
                <h1 class="section-title h2 mb-3">{{ $project->title }}</h1>
                <p class="text-secondary">{{ $project->summary }}</p>

                <h2 class="h5 mt-4">Challenge &amp; approach</h2>
                <div class="text-secondary">
                    {!! nl2br(e($project->results)) !!}
                </div>

                @if($project->tech_stack)
                    <h2 class="h6 mt-4">Tech stack</h2>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($project->tech_stack as $tech)
                            <span class="badge bg-light text-dark border">{{ $tech }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="col-lg-4">
                <div class="value-card p-4 bg-white">
                    <h2 class="h6 text-uppercase text-secondary">Project details</h2>
                    <ul class="list-unstyled text-secondary small mb-0">
                        <li class="mb-2"><strong>Client:</strong> {{ $project->client ?? 'Confidential' }}</li>
                        <li class="mb-2"><strong>Published:</strong> {{ $project->is_published ? 'Yes' : 'No' }}</li>
                        <li class="mb-2"><strong>Tags:</strong> {{ $project->tags->pluck('name')->implode(', ') ?: '—' }}</li>
                    </ul>
                    <a href="{{ route('contact.show') }}" class="btn btn-primary w-100 mt-3">Start your project</a>
                </div>
            </div>
        </div>

        @if($project->images->isNotEmpty())
            <div class="row g-4 mt-4">
                @foreach($project->images as $image)
                    <div class="col-md-6">
                        <figure class="rounded-4 overflow-hidden shadow-sm">
                            <img src="{{ asset('storage/'.$image->path) }}" alt="{{ $image->caption }}" class="w-100">
                            @if($image->caption)
                                <figcaption class="p-3 text-secondary small">{{ $image->caption }}</figcaption>
                            @endif
                        </figure>
                    </div>
                @endforeach
            </div>
        @endif

        @if($related->isNotEmpty())
            <div class="mt-5">
                <h2 class="section-title h4 mb-3">More stories</h2>
                <div class="row g-4">
                    @foreach($related as $item)
                        <div class="col-md-6 col-lg-4">
                            <div class="portfolio-card bg-white h-100 shadow-sm rounded-4">
                                @if($item->cover_image)
                                    <img src="{{ asset('storage/'.$item->cover_image) }}" alt="{{ $item->title }}" class="w-100">
                                @endif
                                <div class="p-4">
                                    <h3 class="h6">{{ $item->title }}</h3>
                                    <a href="{{ route('portfolio.show', $item->slug) }}" class="stretched-link text-decoration-none">Read case study</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
