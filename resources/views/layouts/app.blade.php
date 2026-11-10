<!DOCTYPE html>
<html lang="fr" class="h-full bg-slate-900 text-slate-100 antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ConnectÉtude') }} - Instant EdTech</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (CDN pour démarrage immédiat ou compilation Vite) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>

    <!-- Laravel Echo & Pusher / Reverb JS (CDN pour exécution directe sans build requis) -->
    <script src="https://cdn.jsdelivr.net/npm/pusher-js@8.4.0-rc2/dist/web/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>

    <!-- PeerJS pour la visioconférence WebRTC directe -->
    <script src="https://unpkg.com/peerjs@1.5.4/dist/peerjs.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        .gradient-glow {
            background: radial-gradient(circle at 50% 0%, rgba(99, 102, 241, 0.25) 0%, rgba(15, 23, 42, 0) 70%);
        }
    </style>

    @stack('styles')
</head>
<body class="min-h-full flex flex-col bg-slate-950 font-sans selection:bg-brand-500 selection:text-white">

    <!-- Navigation Bar -->
    <header class="border-b border-slate-800/80 bg-slate-900/60 backdrop-blur-md sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-600 to-violet-500 flex items-center justify-center shadow-lg shadow-brand-500/30 group-hover:scale-105 transition-transform">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <div>
                    <span class="text-xl font-extrabold tracking-tight text-white">Connect<span class="text-brand-500">Étude</span></span>
                    <span class="block text-[10px] uppercase font-semibold tracking-wider text-slate-400">Instant EdTech</span>
                </div>
            </a>

            <!-- Right Links -->
            <nav class="flex items-center gap-3 sm:gap-4">
                <a href="{{ route('home') }}" class="text-sm font-medium text-slate-300 hover:text-white px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('home') ? 'bg-slate-800 text-white' : '' }}">
                    Élève
                </a>
                <a href="{{ route('teacher.lobby') }}" class="flex items-center gap-2 text-sm font-medium text-slate-300 hover:text-white px-3 py-2 rounded-lg transition-colors border border-slate-700/80 hover:border-brand-500/50 {{ request()->routeIs('teacher.lobby') ? 'bg-brand-600/10 border-brand-500/40 text-brand-300' : '' }}">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    Espace Enseignant
                </a>
            </nav>
        </div>
    </header>

    <!-- Flash Messages -->
    @if(session('success') || session('info') || session('error'))
        <div class="max-w-4xl mx-auto px-4 mt-4 w-full">
            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-sm flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if(session('info'))
                <div class="p-4 rounded-xl bg-blue-500/10 border border-blue-500/30 text-blue-300 text-sm flex items-center gap-3">
                    <svg class="w-5 h-5 text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('info') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-sm flex items-center gap-3">
                    <svg class="w-5 h-5 text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif
        </div>
    @endif

    <!-- Main Content -->
    <main class="flex-1 flex flex-col">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-900 bg-slate-950/80 text-center py-6 text-xs text-slate-500">
        <p>&copy; {{ date('Y') }} ConnectÉtude - Visioconférence Peer-to-Peer instantanée sans inscription.</p>
    </footer>

    <!-- Initialisation globale de Laravel Echo / Reverb -->
    <script>
        window.initEcho = function() {
            // Configuration pour Laravel Reverb (ou Pusher)
            const reverbKey = "{{ env('VITE_REVERB_APP_KEY', env('REVERB_APP_KEY', 'reverb_app_key')) }}";
            const reverbHost = "{{ env('VITE_REVERB_HOST', 'localhost') }}";
            const reverbPort = "{{ env('VITE_REVERB_PORT', 8080) }}";
            const reverbScheme = "{{ env('VITE_REVERB_SCHEME', 'http') }}";

            window.Pusher = Pusher;
            window.Echo = new Echo({
                broadcaster: 'reverb',
                key: reverbKey,
                wsHost: reverbHost,
                wsPort: reverbPort,
                wssPort: reverbPort,
                forceTLS: (reverbScheme === 'https'),
                enabledTransports: ['ws', 'wss'],
            });
        };

        try {
            window.initEcho();
        } catch(e) {
            console.warn('Laravel Echo initialisé en mode fallback local:', e);
        }
    </script>

    @stack('scripts')
</body>
</html>
