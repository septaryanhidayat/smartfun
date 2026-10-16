<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuestionEnded implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param  array{correctOptionId: ?int, explanation: ?string, topFive: array<int, array{rank: int, nickname: string, avatar: ?string, score: int, streak: int}>, answerDistribution: array<int, int>}  $payload
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
        return 'QuestionEnded';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return $this->payload;
    }
}
