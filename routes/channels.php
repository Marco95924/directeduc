<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Ici, nous utilisons des canaux publics ouverts car l'application est
| conçue "sans inscription ni authentification".
|
*/

// Canal public pour le lobby des enseignants
Broadcast::channel('teachers-lobby', function () {
    return true;
});

// Canal public dédié à une session spécifique (élève + enseignant)
Broadcast::channel('session.{uuid}', function ($user, $uuid) {
    return true;
});
