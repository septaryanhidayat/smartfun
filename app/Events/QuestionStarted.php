<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuestionStarted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param  array{questionIndex: int, totalQuestions: int, questionId: int, text: string, media: ?string, mediaType: ?string, options: array<int, array{id: int, text: string, media: ?string}>, timeLimit: int, basePoints: int}  $payload
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
        return 'QuestionStarted';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return $this->payload;
    }
}
