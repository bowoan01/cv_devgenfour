@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Overview of content and engagement metrics')

@section('content')
<div class="row g-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h2 class="h6 text-secondary">Services</h2>
                <p class="display-6 fw-bold mb-0">{{ $stats['services'] }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h2 class="h6 text-secondary">Projects</h2>
                <p class="display-6 fw-bold mb-0">{{ $stats['projects'] }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h2 class="h6 text-secondary">Team</h2>
                <p class="display-6 fw-bold mb-0">{{ $stats['team'] }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h2 class="h6 text-secondary">New messages</h2>
                <p class="display-6 fw-bold mb-0">{{ $stats['contacts'] }}</p>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-1">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h2 class="h6 mb-0">Recent contacts</h2>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    @forelse($recentContacts as $contact)
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <div>
                                <strong>{{ $contact->name }}</strong>
                                <p class="mb-0 text-secondary small">{{ \Illuminate\Support\Str::limit($contact->message, 80) }}</p>
                            </div>
                            <span class="badge bg-{{ $contact->status === 'new' ? 'warning text-dark' : 'success' }}">{{ ucfirst($contact->status) }}</span>
                        </li>
                    @empty
                        <li class="list-group-item text-secondary">No contact messages yet.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h2 class="h6 mb-0">Recent projects</h2>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    @forelse($recentProjects as $project)
                        <li class="list-group-item px-0">
                            <strong>{{ $project->title }}</strong>
                            <p class="mb-0 text-secondary small">{{ $project->category ?? '—' }} • {{ $project->created_at->format('M d, Y') }}</p>
                        </li>
                    @empty
                        <li class="list-group-item text-secondary">No projects recorded yet.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
