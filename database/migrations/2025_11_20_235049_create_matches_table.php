<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tournament_id')
                ->constrained('tournaments')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('mappool_id')
                ->constrained('mappools')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('team1_id')
                ->constrained('teams')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('team2_id')
                ->constrained('teams')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('winner_team_id')
                ->nullable()
                ->constrained('teams')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->integer('round')->nullable(); // numeric round if you want
            $table->string('stage')->nullable();  // e.g. "RO32", "QF", "GF"

            $table->string('status')->default('scheduled'); // [scheduled, ongoing, finished, no_show, cancelled]

            $table->dateTime('scheduled_at')->nullable();
            $table->dateTime('match_start')->nullable();
            $table->dateTime('match_end')->nullable();

            $table->foreignId('referee_id')
                ->nullable()
                ->constrained('users')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
