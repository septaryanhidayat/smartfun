<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('game_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('game_player_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->foreignId('option_id')->nullable()->constrained()->nullOnDelete();
            $table->text('text_answer')->nullable();
            $table->boolean('is_correct')->default(false);
            $table->unsignedInteger('response_time_ms');
            $table->unsignedInteger('points_awarded')->default(0);
            $table->decimal('streak_multiplier', 3, 2)->default(1.00);
            $table->timestamps();

            $table->index(['game_session_id', 'question_id'], 'idx_session_question');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_answers');
    }
};
