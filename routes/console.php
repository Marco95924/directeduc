<?php

use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes & Task Scheduling (Laravel 11+)
|--------------------------------------------------------------------------
*/

// Nettoyage automatique des sessions abandonnées ou expirées toutes les 10 minutes
Schedule::command('sessions:clean-expired')->everyTenMinutes();

// Purge quotidienne complète des sessions de plus de 24 heures à minuit
Schedule::command('sessions:clean-expired --delete')->daily();
