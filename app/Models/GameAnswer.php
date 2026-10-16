<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_session_id',
        'game_player_id',
        'question_id',
        'option_id',
        'text_answer',
        'is_correct',
        'response_time_ms',
        'points_awarded',
        'streak_multiplier',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_correct' => 'boolean',
            'response_time_ms' => 'integer',
            'points_awarded' => 'integer',
            'streak_multiplier' => 'decimal:2',
        ];
    }

    /**
     * @return BelongsTo<GameSession, $this>
     */
    public function gameSession(): BelongsTo
    {
        return $this->belongsTo(GameSession::class);
    }

    /**
     * @return BelongsTo<GamePlayer, $this>
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(GamePlayer::class, 'game_player_id');
    }

    /**
     * @return BelongsTo<Question, $this>
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * @return BelongsTo<Option, $this>
     */
    public function option(): BelongsTo
    {
        return $this->belongsTo(Option::class);
    }
}
