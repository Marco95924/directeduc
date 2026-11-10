<?php

namespace App\Events;

use App\Models\SessionCours;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SessionEnded implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $uuid;
    public string $reason;

    /**
     * Create a new event instance.
     */
    public function __construct(SessionCours $session, string $reason = 'hangup')
    {
        $this->uuid = $session->uuid;
        $this->reason = $reason;
    }

    /**
     * Broadcast to the specific session channel
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('session.' . $this->uuid),
        ];
    }

    public function broadcastAs(): string
    {
        return 'SessionEnded';
    }
}
