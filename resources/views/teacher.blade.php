<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Espace Enseignant - Demandes en direct</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 text-white min-h-screen p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-emerald-400 mb-6">Demandes d'Élèves en Attente</h1>

        @if($pendingSessions->isEmpty())
            <div class="bg-slate-800 p-6 rounded-xl border border-slate-700 text-center text-slate-400">
                Aucune demande pour le moment. Laissez cette page ouverte.
            </div>
        @else
            <div class="grid gap-4">
                @foreach($pendingSessions as $session)
                    <div class="bg-slate-800 p-5 rounded-xl border border-slate-700 flex justify-between items-center">
                        <div>
                            <span class="bg-emerald-500/10 text-emerald-400 text-xs font-semibold px-2.5 py-0.5 rounded border border-emerald-500/20">{{ $session->niveau }}</span>
                            <h2 class="text-xl font-bold mt-2">{{ $session->sujet }}</h2>
                            <p class="text-sm text-slate-400">Élève de {{ $session->age }} ans • Attente depuis {{ $session->created_at->diffForHumans() }}</p>
                        </div>
                        <a href="{{ route('session.room', ['uuid' => $session->session_id, 'role' => 'teacher']) }}" class="bg-blue-600 hover:bg-blue-700 px-5 py-2.5 rounded-lg font-bold text-white transition">
                            Rejoindre la visio
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</body>
</html>