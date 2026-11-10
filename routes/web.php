<?php

use App\Http\Controllers\LiveSessionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Instant EdTech
|--------------------------------------------------------------------------
*/

// Accueil Apprenant
Route::get('/', [LiveSessionController::class, 'index'])->name('home');
Route::post('/session/create', [LiveSessionController::class, 'store'])->name('session.store');

// Espace Enseignant
Route::get('/enseignant', [LiveSessionController::class, 'teacherDashboard'])->name('teacher.dashboard');
Route::post('/session/{uuid}/join', [LiveSessionController::class, 'join'])->name('session.join');

// Salle WebRTC Visioconférence
Route::get('/session/{uuid}', [LiveSessionController::class, 'room'])->name('session.room');
Route::post('/session/{uuid}/peer', [LiveSessionController::class, 'registerPeer'])->name('session.registerPeer');
Route::post('/session/{uuid}/end', [LiveSessionController::class, 'end'])->name('session.end');
