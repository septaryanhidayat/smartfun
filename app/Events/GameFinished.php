<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GameFinished implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param  array{podium: array<int, array{rank: int, nickname: string, avatar: ?string, score: int}>, summaryAnalytics: array{totalPlayers: int, totalQuestions: int, accuracyRate: float, highestStreak: int}}  $payload
     */
    public function __construct(
        public string $pin,
        public array $payload
    ) {}

    /**
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('presence-game.'.$this->pin),
            new Channel('game.'.$this->pin),
        ];
    }

    public function broadcastAs(): string
    {
        return 'GameFinished';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return $this->payload;
    }
}
