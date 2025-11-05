@extends('layouts.app')

@section('title', $service->title)

@section('content')
<section class="py-5 bg-white">
    <div class="container">
        <div class="row gy-4">
            <div class="col-lg-8">
                <h1 class="section-title h2 mb-3">{{ $service->title }}</h1>
                <p class="text-secondary">{{ $service->short_description }}</p>
                <div class="mt-4">
                    {!! nl2br(e($service->description)) !!}
                </div>
            </div>
            <div class="col-lg-4">
                <div class="value-card p-4 bg-white">
                    <h2 class="h5 fw-semibold mb-3">Engagement highlights</h2>
                    <ul class="list-unstyled text-secondary mb-4">
                        <li class="mb-2"><i class="bi bi-clock-history text-primary me-2"></i>Weekly product syncs</li>
                        <li class="mb-2"><i class="bi bi-person-gear text-primary me-2"></i>Dedicated product team</li>
                        <li class="mb-2"><i class="bi bi-graph-up text-primary me-2"></i>Measurable outcomes</li>
                    </ul>
                    <a href="{{ route('contact.show') }}" class="btn btn-primary w-100">Discuss your project</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
