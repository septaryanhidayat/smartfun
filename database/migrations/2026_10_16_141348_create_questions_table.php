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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->cascadeOnDelete();
            $table->text('question_text');
            $table->string('question_type', 30)->default('multiple_choice');
            $table->string('media_url')->nullable();
            $table->string('media_type', 20)->nullable();
            $table->unsignedInteger('time_limit_seconds')->default(30);
            $table->unsignedInteger('base_points')->default(1000);
            $table->unsignedInteger('order_index')->default(1);
            $table->text('explanation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
