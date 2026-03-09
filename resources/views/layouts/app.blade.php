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
    
    <!-- Favicon -->
    @if (file_exists(public_path('images/favicon.png')))
        <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('images/favicon.png') }}">
    @elseif (file_exists(public_path('images/favicon.ico')))
        <link rel="icon" href="{{ asset('images/favicon.ico') }}">
    @endif

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
        
        <div class="ms-3 d-flex align-items-center">
            @if (file_exists(public_path('images/favicon.png')))
                <img src="{{ asset('images/favicon.png') }}" alt="{{ config('app.name', 'Gestionale') }}" style="height:32px; width:auto; object-fit:contain;" class="me-2">
            @elseif (file_exists(public_path('images/favicon.ico')))
                <img src="{{ asset('images/favicon.ico') }}" alt="{{ config('app.name', 'Gestionale') }}" style="height:32px; width:auto; object-fit:contain;" class="me-2">
            @endif
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
            <a class="nav-link {{ request()->routeIs('chatbot.*') ? 'active' : '' }}" href="{{ route('chatbot.index') }}">
                <i class="bi bi-robot"></i>
                <span>Assistente AI</span>
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

    <!-- ===== CHATBOT WIDGET FLOATING ===== -->
    @auth
    <div id="chatbotWidget" style="position:fixed;bottom:24px;right:24px;z-index:9999;display:flex;flex-direction:column;align-items:flex-end;">

        <!-- Pannello chat -->
        <div id="chatbotPanel" style="display:none;width:360px;max-height:500px;display:none;flex-direction:column;border-radius:16px;overflow:hidden;box-shadow:0 8px 32px rgba(102,126,234,0.25);margin-bottom:10px;">

            <!-- Header -->
            <div style="background:linear-gradient(135deg,#667eea,#764ba2);padding:0.8rem 1rem;display:flex;align-items:center;justify-content:space-between;">
                <div class="d-flex align-items-center gap-2" style="color:#fff;">
                    <i class="bi bi-robot fs-5"></i>
                    <span style="font-weight:600;font-size:0.92rem;">Assistente AI</span>
                    <span style="font-size:0.7rem;opacity:0.8;">{{ config('services.openai.model', 'gpt-4o-mini') }}</span>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('chatbot.index') }}" title="Apri pagina intera" style="color:rgba(255,255,255,0.7);font-size:0.85rem;"><i class="bi bi-arrows-fullscreen"></i></a>
                    <button onclick="toggleChatbotWidget()" style="background:none;border:none;color:rgba(255,255,255,0.8);cursor:pointer;line-height:1;"><i class="bi bi-x-lg"></i></button>
                </div>
            </div>

            <!-- Messages -->
            <div id="widgetMessages" style="flex:1;overflow-y:auto;background:#f8f9fa;padding:1rem;max-height:320px;scroll-behavior:smooth;">
                <div id="widgetWelcome" style="text-align:center;padding:1rem 0.5rem;color:#6c757d;font-size:0.85rem;">
                    <i class="bi bi-robot" style="font-size:1.8rem;background:linear-gradient(135deg,#667eea,#764ba2);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;"></i>
                    <p class="mt-2 mb-0">Ciao! Chiedimi qualcosa sui tuoi dati.</p>
                </div>
            </div>

            <!-- Input -->
            <div style="background:#fff;padding:0.7rem;border-top:1px solid #e9ecef;">
                <form id="widgetForm" class="d-flex gap-2" autocomplete="off">
                    @csrf
                    <input type="text" id="widgetInput" class="form-control form-control-sm" placeholder="Scrivi una domanda..." maxlength="1000" style="border-radius:20px;font-size:0.85rem;">
                    <button type="submit" class="btn btn-sm px-3" style="background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;border:none;border-radius:20px;">
                        <i class="bi bi-send-fill"></i>
                    </button>
                </form>
            </div>
        </div>

        <!-- FAB Button -->
        <button id="chatbotFab" onclick="toggleChatbotWidget()" aria-label="Apri assistente AI"
            style="width:56px;height:56px;border-radius:50%;border:none;cursor:pointer;background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;box-shadow:0 4px 18px rgba(102,126,234,0.45);display:flex;align-items:center;justify-content:center;font-size:1.4rem;transition:transform 0.2s;">
            <i class="bi bi-robot" id="fabIcon"></i>
        </button>
    </div>
    @endauth

    <script>
    (function () {
        const panel      = document.getElementById('chatbotPanel');
        const fabIcon    = document.getElementById('fabIcon');
        const widgetForm = document.getElementById('widgetForm');
        const widgetMessages = document.getElementById('widgetMessages');
        const widgetInput    = document.getElementById('widgetInput');
        const widgetWelcome  = document.getElementById('widgetWelcome');

        let widgetOpen = false;
        let widgetCronologia = [];

        window.toggleChatbotWidget = function () {
            widgetOpen = !widgetOpen;
            panel.style.display = widgetOpen ? 'flex' : 'none';
            panel.style.flexDirection = 'column';
            fabIcon.className = widgetOpen ? 'bi bi-x-lg' : 'bi bi-robot';
            if (widgetOpen) { widgetInput.focus(); widgetMessages.scrollTop = widgetMessages.scrollHeight; }
        };

        if (widgetForm) {
            widgetForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const domanda = widgetInput.value.trim();
                if (!domanda) return;
                widgetInput.value = '';
                if (widgetWelcome) widgetWelcome.remove();
                widgetAppend('user', domanda);
                const typingEl = widgetAppendTyping();
                widgetInput.disabled = true;

                axios.post('{{ route('chatbot.ask') }}', {
                    domanda:    domanda,
                    cronologia: widgetCronologia.slice(-10)
                }).then(function (r) {
                    typingEl.remove();
                    const risposta = r.data.risposta || 'Nessuna risposta.';
                    widgetAppend('bot', risposta);
                    widgetCronologia.push({ role: 'user',      content: domanda  });
                    widgetCronologia.push({ role: 'assistant', content: risposta });
                    if (widgetCronologia.length > 20) widgetCronologia = widgetCronologia.slice(-20);
                }).catch(function () {
                    typingEl.remove();
                    widgetAppend('bot', 'Si è verificato un errore. Riprova.');
                }).finally(function () {
                    widgetInput.disabled = false;
                    widgetInput.focus();
                });
            });
        }

        function widgetAppend(role, testo) {
            const div = document.createElement('div');
            if (role === 'user') {
                div.style.cssText = 'display:flex;justify-content:flex-end;margin-bottom:0.6rem;';
                div.innerHTML = `<div style="background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;border-radius:14px 14px 3px 14px;padding:0.5rem 0.8rem;font-size:0.83rem;max-width:85%;word-break:break-word;">${widgetEscape(testo)}</div>`;
            } else {
                div.style.cssText = 'display:flex;align-items:flex-start;gap:0.5rem;margin-bottom:0.6rem;';
                div.innerHTML = `<div style="width:26px;height:26px;background:linear-gradient(135deg,#667eea,#764ba2);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="bi bi-robot" style="color:#fff;font-size:0.75rem;"></i></div><div style="background:#fff;border:1px solid #e9ecef;border-radius:3px 14px 14px 14px;padding:0.5rem 0.8rem;font-size:0.83rem;max-width:85%;word-break:break-word;">${widgetRender(testo)}</div>`;
            }
            widgetMessages.appendChild(div);
            widgetMessages.scrollTop = widgetMessages.scrollHeight;
        }

        function widgetAppendTyping() {
            const div = document.createElement('div');
            div.style.cssText = 'display:flex;align-items:flex-start;gap:0.5rem;margin-bottom:0.6rem;';
            div.innerHTML = `<div style="width:26px;height:26px;background:linear-gradient(135deg,#667eea,#764ba2);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="bi bi-robot" style="color:#fff;font-size:0.75rem;"></i></div><div style="background:#fff;border:1px solid #e9ecef;border-radius:3px 14px 14px 14px;padding:0.5rem 0.8rem;"><span style="display:inline-block;width:6px;height:6px;background:#9c82d4;border-radius:50%;margin:0 1px;animation:typing 1.2s infinite;"></span><span style="display:inline-block;width:6px;height:6px;background:#9c82d4;border-radius:50%;margin:0 1px;animation:typing 1.2s 0.2s infinite;"></span><span style="display:inline-block;width:6px;height:6px;background:#9c82d4;border-radius:50%;margin:0 1px;animation:typing 1.2s 0.4s infinite;"></span></div>`;
            widgetMessages.appendChild(div);
            widgetMessages.scrollTop = widgetMessages.scrollHeight;
            return div;
        }

        function widgetEscape(t) {
            return t.replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'})[m]);
        }

        function widgetRender(t) {
            let h = widgetEscape(t);
            h = h.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
            return h.replace(/\n/g, '<br>');
        }
    })();
    </script>

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
