<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeaderboardUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param  array<int, array{rank: int, nickname: string, avatar: ?string, score: int, streak: int}>  $leaderboard
     */
    public function __construct(
        public string $pin,
        public array $leaderboard
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
        return 'LeaderboardUpdated';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'leaderboard' => $this->leaderboard,
        ];
    }
}
