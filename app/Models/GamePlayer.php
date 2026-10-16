<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GamePlayer extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_session_id',
        'nickname',
        'avatar_url',
        'total_score',
        'current_streak',
        'final_rank',
        'is_connected',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'total_score' => 'integer',
            'current_streak' => 'integer',
            'final_rank' => 'integer',
            'is_connected' => 'boolean',
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
     * @return HasMany<GameAnswer, $this>
     */
    public function answers(): HasMany
    {
        return $this->hasMany(GameAnswer::class);
    }
}
