<footer class="bg-dark text-white py-5 mt-auto">
    <div class="container">
        <div class="row gy-4">
            <div class="col-md-6">
                <h5 class="fw-bold">{{ config('app.name', 'Devgenfour') }}</h5>
                <p class="text-white-50 mb-0">We build digital products that empower your business.</p>
            </div>
            <div class="col-md-3">
                <h6 class="text-uppercase small">Contact</h6>
                <ul class="list-unstyled text-white-50 mb-0">
                    <li>{{ $globalSettings['contact_address'] ?? 'Remote-first' }}</li>
                    <li>{{ $globalSettings['contact_email'] ?? 'hello@example.com' }}</li>
                    <li>{{ $globalSettings['contact_phone'] ?? '+62-000-0000' }}</li>
                </ul>
            </div>
            <div class="col-md-3">
                <h6 class="text-uppercase small">Follow</h6>
                <div class="d-flex gap-3">
                    <a href="{{ $globalSettings['social_linkedin'] ?? '#' }}" class="text-white-50"><i class="bi bi-linkedin"></i></a>
                    <a href="{{ $globalSettings['social_github'] ?? '#' }}" class="text-white-50"><i class="bi bi-github"></i></a>
                    <a href="{{ $globalSettings['social_dribbble'] ?? '#' }}" class="text-white-50"><i class="bi bi-dribbble"></i></a>
                </div>
            </div>
        </div>
        <div class="border-top border-secondary mt-4 pt-3 d-flex justify-content-between text-white-50 small">
            <span>&copy; {{ now()->year }} {{ config('app.name') }}. All rights reserved.</span>
            <span>Crafted with Laravel &amp; Bootstrap.</span>
        </div>
    </div>
</footer>
