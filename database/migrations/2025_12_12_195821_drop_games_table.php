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
        Schema::dropIfExists('games');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();

            $table->foreignId('match_id')
                ->constrained('matches')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('mappool_map_id')
                ->constrained('mappool_maps')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->integer('order_in_match');
            $table->foreignId('winning_team_id')
                ->nullable()
                ->constrained('teams')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->timestamps();
        });
    }
};
