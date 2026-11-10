<?php

namespace App\Http\Controllers;

use App\Models\LiveSession;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LiveSessionController extends Controller
{
    // Page d'accueil (Formulaire Élève)
    public function index()
    {
        return view('welcome');
    }

    // Traitement du formulaire élève sans inscription
    public function store(Request $request)
    {
        $validated = $request->validate([
            'age' => 'required|integer|min:5|max:99',
            'niveau' => 'required|string',
            'sujet' => 'required|string|max:255',
        ]);

        $session = LiveSession::create([
            'session_id' => (string) Str::uuid(),
            'age' => $validated['age'],
            'niveau' => $validated['niveau'],
            'sujet' => $validated['sujet'],
            'status' => 'pending',
        ]);

        return redirect()->route('session.room', ['uuid' => $session->session_id, 'role' => 'student']);
    }

    // Liste des demandes en attente pour les enseignants
    public function teacherDashboard()
    {
        $pendingSessions = LiveSession::where('status', 'pending')->latest()->get();
        return view('teacher', compact('pendingSessions'));
    }

    // Salle de visio (Élève & Enseignant)
    public function room($uuid, Request $request)
    {
        $session = LiveSession::where('session_id', $uuid)->firstOrFail();
        $role = $request->query('role', 'student');

        if ($role === 'teacher' && $session->status === 'pending') {
            $session->update(['status' => 'active']);
        }

        return view('room', compact('session', 'role'));
    }

    // API pour mettre à jour les Peer IDs
    public function updatePeer(Request $request, $uuid)
    {
        $session = LiveSession::where('session_id', $uuid)->firstOrFail();
        
        if ($request->role === 'student') {
            $session->update(['peer_id_student' => $request->peer_id]);
        } else {
            $session->update(['peer_id_teacher' => $request->peer_id]);
        }

        return response()->json(['status' => 'success']);
    }

    // API pour récupérer le Peer ID de l'autre participant
    public function getPeer($uuid)
    {
        $session = LiveSession::where('session_id', $uuid)->firstOrFail();
        return response()->json([
            'student' => $session->peer_id_student,
            'teacher' => $session->peer_id_teacher,
            'status' => $session->status
        ]);
    }
}