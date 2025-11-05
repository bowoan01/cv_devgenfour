@extends('layouts.app')

@section('title', 'Portfolio')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h1 class="section-title h2 mb-1">Portfolio</h1>
                <p class="text-secondary mb-0">Selected work spanning web, mobile, and enterprise platforms.</p>
            </div>
            <form class="d-flex gap-2" method="GET">
                <select name="category" class="form-select" onchange="this.form.submit()">
                    <option value="">All categories</option>
                    @foreach($categories as $item)
                        <option value="{{ $item }}" @selected($category === $item)>{{ $item }}</option>
                    @endforeach
                </select>
                @if($category)
                    <a href="{{ route('portfolio.index') }}" class="btn btn-outline-secondary">Reset</a>
                @endif
            </form>
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
                            <h2 class="h5">{{ $project->title }}</h2>
                            <p class="text-secondary">{{ \Illuminate\Support\Str::limit($project->summary, 140) }}</p>
                            <a href="{{ route('portfolio.show', $project->slug) }}" class="stretched-link text-decoration-none">View case study</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-4">
            {{ $projects->withQueryString()->links() }}
        </div>
    </div>
</section>
@endsection
