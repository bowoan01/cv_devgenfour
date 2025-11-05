<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="/css/admin.css" rel="stylesheet">
    <title>@yield('title', 'Admin Panel')</title>
    @stack('styles')
</head>
<body class="d-flex bg-light">
    <aside class="admin-sidebar d-flex flex-column p-4">
        <div class="fs-4 fw-bold text-white mb-4">{{ config('app.name') }}</div>
        <nav class="nav flex-column gap-2">
            <a class="nav-link text-white" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
            <a class="nav-link text-white" href="{{ route('admin.services.index') }}"><i class="bi bi-layers me-2"></i>Services</a>
            <a class="nav-link text-white" href="{{ route('admin.projects.index') }}"><i class="bi bi-kanban me-2"></i>Projects</a>
            <a class="nav-link text-white" href="{{ route('admin.teams.index') }}"><i class="bi bi-people me-2"></i>Team</a>
            <a class="nav-link text-white" href="{{ route('admin.contacts.index') }}"><i class="bi bi-inbox me-2"></i>Contacts</a>
            <a class="nav-link text-white" href="{{ route('admin.profile.edit') }}"><i class="bi bi-person-circle me-2"></i>Profile</a>
            <form action="{{ route('logout') }}" method="POST" class="mt-auto">
                @csrf
                <button type="submit" class="btn btn-outline-light w-100"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
            </form>
        </nav>
    </aside>
    <div class="flex-grow-1 d-flex flex-column min-vh-100">
        <header class="admin-header d-flex align-items-center justify-content-between px-4 py-3 bg-white border-bottom">
            <div>
                <h1 class="h4 mb-0">@yield('page-title', 'Overview')</h1>
                <p class="text-muted small mb-0">@yield('page-subtitle')</p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="fw-semibold">{{ auth()->user()->name }}</span>
            </div>
        </header>
        <main class="p-4 flex-grow-1">
            @include('partials.flash')
            @yield('content')
        </main>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js" defer></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.3/Sortable.min.js" defer></script>
    <script src="/js/app.js" defer></script>
    @stack('scripts')
</body>
</html>
