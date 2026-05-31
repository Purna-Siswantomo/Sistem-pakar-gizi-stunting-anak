<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'NutriScreen-ES')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light text-dark">
    <header class="border-bottom bg-white">
        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="navbar-brand fw-semibold text-success">
                    NutriScreen-ES
                </a>

                <div class="navbar-nav flex-row flex-wrap gap-2 ms-lg-auto align-items-center">
                    @auth
                        <a href="{{ route('dashboard') }}" class="nav-link px-3 rounded {{ request()->routeIs('dashboard') ? 'bg-success-subtle text-success fw-semibold' : '' }}">Dashboard</a>
                        <a href="{{ route('children.index') }}" class="nav-link px-3 rounded {{ request()->routeIs('children.*') ? 'bg-success-subtle text-success fw-semibold' : '' }}">Data Anak</a>
                        <a href="{{ route('screenings.index') }}" class="nav-link px-3 rounded {{ request()->routeIs('screenings.index') ? 'bg-success-subtle text-success fw-semibold' : '' }}">Riwayat</a>
                        @if (auth()->user()->role === 'admin')
                            <a href="{{ route('knowledge-base.rules') }}" class="nav-link px-3 rounded {{ request()->routeIs('knowledge-base.rules*') ? 'bg-success-subtle text-success fw-semibold' : '' }}">Knowledge Base</a>
                        @endif
                        <a href="{{ route('about') }}" class="nav-link px-3 rounded {{ request()->routeIs('about') ? 'bg-success-subtle text-success fw-semibold' : '' }}">Tentang</a>
                        <span class="text-secondary small px-2">{{ auth()->user()->name }} ({{ auth()->user()->role }})</span>
                        <form method="POST" action="{{ route('logout') }}" class="mb-0">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-secondary">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-sm btn-outline-success">Login</a>
                        <a href="{{ route('register') }}" class="btn btn-sm btn-success">Register</a>
                    @endauth
                </div>
            </div>
        </nav>
    </header>

    <main class="container py-4 py-lg-5">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('status'))
            <div class="alert alert-info">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <div class="fw-semibold">Periksa kembali input berikut:</div>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
