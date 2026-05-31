<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'NutriScreen-ES') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <main class="container min-vh-100 d-flex align-items-center justify-content-center py-5">
        <div class="w-100" style="max-width: 440px;">
            <div class="text-center mb-4">
                <h1 class="h3 fw-bold text-success">NutriScreen-ES</h1>
                <p class="text-secondary mb-0">Screening awal dan edukasi risiko stunting/malnutrisi.</p>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </main>
</body>
</html>
