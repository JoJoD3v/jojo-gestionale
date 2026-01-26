<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Gestionale') }} - @yield('title', 'Dashboard')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @stack('styles')
</head>
<body>
    <!-- Topbar -->
    <nav class="topbar">
        <button class="btn btn-link text-dark d-md-none" id="mobileSidebarToggle" type="button">
            <i class="bi bi-list" style="font-size: 1.5rem;"></i>
        </button>
        <button class="btn btn-link text-dark d-none d-md-block" id="sidebarToggle" type="button">
            <i class="bi bi-list" style="font-size: 1.5rem;"></i>
        </button>
        
        <div class="ms-3">
            <strong>{{ config('app.name', 'Gestionale') }}</strong>
        </div>

        <div class="ms-auto d-flex align-items-center">
            <span class="me-3">{{ Auth::user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-box-arrow-right"></i> Esci
                </button>
            </form>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <nav class="nav flex-column">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
            <a class="nav-link {{ request()->routeIs('clienti.*') ? 'active' : '' }}" href="{{ route('clienti.index') }}">
                <i class="bi bi-people"></i>
                <span>Clienti</span>
            </a>
            <a class="nav-link {{ request()->routeIs('lavori.*') ? 'active' : '' }}" href="{{ route('lavori.index') }}">
                <i class="bi bi-briefcase"></i>
                <span>Lavori</span>
            </a>
            
            <!-- Pagamenti Submenu -->
            <div class="nav-item">
                <a class="nav-link {{ request()->routeIs('pagamenti.*') ? 'active' : '' }}" 
                   href="#" 
                   id="pagamentiToggle"
                   onclick="toggleSubmenu(event)">
                    <i class="bi bi-cash-coin"></i>
                    <span>Pagamenti</span>
                    <i class="bi bi-chevron-down ms-auto" id="pagamentiChevron"></i>
                </a>
                <div id="pagamentiSubmenu" style="display: {{ request()->routeIs('pagamenti.*') ? 'block' : 'none' }};">
                    <nav class="nav flex-column ps-3">
                        <a class="nav-link {{ request()->routeIs('pagamenti.unici.*') ? 'active' : '' }}" href="{{ route('pagamenti.unici.index') }}">
                            <i class="bi bi-coin"></i>
                            <span>Pagamenti Unici</span>
                        </a>
                        <a class="nav-link {{ request()->routeIs('pagamenti.periodici.*') ? 'active' : '' }}" href="{{ route('pagamenti.periodici.index') }}">
                            <i class="bi bi-arrow-repeat"></i>
                            <span>Pagamenti Periodici</span>
                        </a>
                    </nav>
                </div>
            </div>
            
            <a class="nav-link {{ request()->routeIs('calendario.*') ? 'active' : '' }}" href="{{ route('calendario.index') }}">
                <i class="bi bi-calendar3"></i>
                <span>Calendario</span>
            </a>
            <a class="nav-link {{ request()->routeIs('tasks.*') ? 'active' : '' }}" href="{{ route('tasks.index') }}">
                <i class="bi bi-check2-square"></i>
                <span>Tasks</span>
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <div class="container-fluid py-4">
            <!-- Alerts -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-mdb-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-mdb-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Attenzione!</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-mdb-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Page Content -->
            @yield('content')
        </div>
    </div>

    @stack('scripts')
    
    <script>
        function toggleSubmenu(event) {
            event.preventDefault();
            const submenu = document.getElementById('pagamentiSubmenu');
            const chevron = document.getElementById('pagamentiChevron');
            
            if (submenu.style.display === 'none') {
                submenu.style.display = 'block';
                chevron.style.transform = 'rotate(180deg)';
            } else {
                submenu.style.display = 'none';
                chevron.style.transform = 'rotate(0deg)';
            }
        }
        
        // Imposta lo stato iniziale del chevron
        document.addEventListener('DOMContentLoaded', function() {
            const submenu = document.getElementById('pagamentiSubmenu');
            const chevron = document.getElementById('pagamentiChevron');
            if (submenu && submenu.style.display === 'block') {
                chevron.style.transform = 'rotate(180deg)';
            }
        });
    </script>
</body>
</html>
