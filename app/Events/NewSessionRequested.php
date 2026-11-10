<?php

namespace App\Events;

use App\Models\SessionCours;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewSessionRequested implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $sessionData;

    /**
     * Create a new event instance.
     */
    public function __construct(SessionCours $session)
    {
        $this->sessionData = [
            'id' => $session->id,
            'uuid' => $session->uuid,
            'age_eleve' => $session->age_eleve,
            'classe_eleve' => $session->classe_eleve,
            'sujet' => $session->sujet,
            'status' => $session->status,
            'created_at' => $session->created_at->toIso8601String(),
            'time_ago' => $session->created_at->diffForHumans(),
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        // Diffusé sur le canal public de veille des enseignants disponibles
        return [
            new Channel('teachers-lobby'),
        ];
    }

    /**
     * Nom de l'événement pour l'écoute côté frontend (Echo.channel(...).listen('.NewSessionRequested', ...))
     */
    public function broadcastAs(): string
    {
        return 'NewSessionRequested';
    }
}
