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
        Schema::create('game_players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_session_id')->constrained()->cascadeOnDelete();
            $table->string('nickname', 50);
            $table->string('avatar_url')->nullable();
            $table->integer('total_score')->default(0);
            $table->integer('current_streak')->default(0);
            $table->integer('final_rank')->nullable();
            $table->boolean('is_connected')->default(true);
            $table->timestamps();

            $table->unique(['game_session_id', 'nickname'], 'uq_session_player');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_players');
    }
};
