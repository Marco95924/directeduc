<?php

namespace App\Console\Commands;

use App\Models\SessionCours;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CleanExpiredSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sessions:clean-expired {--delete : Supprimer définitivement les sessions très anciennes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Nettoie les sessions de cours abandonnées ou expirées';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Début du nettoyage des sessions temporaires...');

        // 1. Expirer les demandes en attente vieilles de plus de 15 minutes
        $expiredCount = SessionCours::pending()
            ->where('created_at', '<', Carbon::now()->subMinutes(15))
            ->update(['status' => 'expired']);

        $this->info("{$expiredCount} session(s) en attente marquée(s) comme expirée(s).");

        // 2. Clôturer les sessions actives orphelines de plus de 3 heures
        $abandonedCount = SessionCours::active()
            ->where('started_at', '<', Carbon::now()->subHours(3))
            ->update([
                'status' => 'ended',
                'ended_at' => Carbon::now(),
            ]);

        $this->info("{$abandonedCount} session(s) orpheline(s) clôturée(s).");

        // 3. Optionnel : Suppression physique des données après 24 heures (RGPD / Confidentialité)
        if ($this->option('delete')) {
            $deletedCount = SessionCours::where('created_at', '<', Carbon::now()->subHours(24))->delete();
            $this->info("{$deletedCount} session(s) purgée(s) définitivement de la base de données.");
        }

        return Command::SUCCESS;
    }
}
