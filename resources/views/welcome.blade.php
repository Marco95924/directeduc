<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DIRECTEDUC - Cours en direct sans inscription</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800 antialiased flex flex-col min-h-screen">

    <header class="bg-white border-b border-slate-200 py-4 px-6 shadow-sm">
        <div class="max-w-4xl mx-auto flex justify-between items-center">
            <span class="text-xl font-bold bg-gradient-to-r from-indigo-600 to-violet-600 bg-clip-text text-transparent">DIRECTEDUC</span>
            <a href="{{ route('teacher.dashboard') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 bg-indigo-50 px-4 py-2 rounded-lg transition">Espace Enseignant</a>
        </div>
    </header>

    <main class="flex-grow flex items-center justify-center px-4 py-12">
        <div class="max-w-md w-full bg-white rounded-2xl shadow-xl border border-slate-100 p-8">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-slate-900">Besoin d'un professeur ?</h1>
                <p class="text-sm text-slate-500 mt-2">Connectez-vous instantanément en visio, sans inscription.</p>
            </div>

            @if(session('success'))
                <div class="mb-4 p-3 bg-emerald-50 text-emerald-700 text-sm rounded-lg border border-emerald-200">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('session.store') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Votre Âge</label>
                    <input type="number" name="age" required min="5" max="25" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition" placeholder="Ex: 14">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Votre Classe</label>
                    <input type="text" name="classe" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition" placeholder="Ex: 4ème / Seconde S">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Sujet ou question à aborder</label>
                    <textarea name="sujet" required rows="3" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition resize-none" placeholder="Ex: J'ai besoin d'aide sur les équations du premier degré..."></textarea>
                </div>

                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 rounded-xl shadow-lg shadow-indigo-200 transition duration-200">
                    Lancer la session en direct
                </button>
            </form>
        </div>
    </main>

</body>
</html>