<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="d-flex align-items-center justify-content-center min-vh-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-5">
                    <div class="card shadow-5-strong">
                        <div class="card-body p-5">
                            <div class="text-center mb-4">
                                <h2 class="mb-3">{{ config('app.name', 'Laravel') }}</h2>
                                <p class="text-muted">Gestionale Freelance</p>
                            </div>

                            @yield('content')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
