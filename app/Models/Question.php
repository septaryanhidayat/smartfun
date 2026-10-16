<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'question_text',
        'question_type',
        'media_url',
        'media_type',
        'time_limit_seconds',
        'base_points',
        'order_index',
        'explanation',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'time_limit_seconds' => 'integer',
            'base_points' => 'integer',
            'order_index' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<Quiz, $this>
     */
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * @return HasMany<Option, $this>
     */
    public function options(): HasMany
    {
        return $this->hasMany(Option::class)->orderBy('order_index');
    }

    /**
     * @return HasMany<GameAnswer, $this>
     */
    public function answers(): HasMany
    {
        return $this->hasMany(GameAnswer::class);
    }
}
