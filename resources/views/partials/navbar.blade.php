<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container py-2">
        <a class="navbar-brand fw-bold text-primary" href="{{ route('home') }}">{{ config('app.name', 'Devgenfour') }}</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('about') }}">About</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('services.index') }}">Services</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('portfolio.index') }}">Portfolio</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('contact.show') }}">Contact</a></li>
            </ul>
            <div class="d-flex ms-lg-3 gap-2">
                @auth
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary btn-sm">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-sm">Login</a>
                @endauth
            </div>
        </div>
    </div>
</nav>
