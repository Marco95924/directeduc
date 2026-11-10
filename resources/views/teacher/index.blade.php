<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Enseignant - DIRECTEDUC</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col">

    <header class="bg-white border-b border-slate-200 py-4 px-6 shadow-sm">
        <div class="max-w-6xl mx-auto flex justify-between items-center">
            <span class="text-xl font-bold bg-gradient-to-r from-indigo-600 to-violet-600 bg-clip-text text-transparent">DIRECTEDUC - Enseignant</span>
            <a href="{{ route('home') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">Accueil Apprenant</a>
        </div>
    </header>

    <main class="flex-grow max-w-6xl w-full mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-slate-900">Sessions en attente</h1>
            <p class="text-sm text-slate-500 mt-1">Prenez en charge un élève pour lancer la visioconférence instantanée.</p>
        </div>

        @if($sessions->isEmpty())
            <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
                <p class="text-slate-500 font-medium">Aucune session en attente pour le moment.</p>
            </div>
        @else
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                @foreach($sessions as $session)
                    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-center mb-3">
                                <span class="bg-indigo-50 text-indigo-700 text-xs font-semibold px-2.5 py-1 rounded-full">Classe : {{ $session->classe }}</span>
                                <span class="text-xs text-slate-400">Âge : {{ $session->age }} ans</span>
                            </div>
                            <h3 class="font-semibold text-slate-800 mb-2">Sujet :</h3>
                            <p class="text-sm text-slate-600 bg-slate-50 p-3 rounded-xl border border-slate-100 mb-6">{{ $session->sujet }}</p>
                        </div>
                        
                        <form action="{{ route('session.join', $session->uuid) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-medium py-2.5 rounded-xl transition duration-200 shadow-md shadow-emerald-100">
                                Rejoindre la salle
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    </main>

</body>
</html>