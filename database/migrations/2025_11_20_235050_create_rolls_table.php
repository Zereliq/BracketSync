<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rolls', function (Blueprint $table) {
            $table->id();

            $table->foreignId('match_id')
                ->constrained('matches')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('team_id')
                ->constrained('teams')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->integer('roll'); // rolled value

            $table->timestamps();

            $table->unique(['match_id', 'team_id']); // one roll per team per match
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rolls');
    }
};
