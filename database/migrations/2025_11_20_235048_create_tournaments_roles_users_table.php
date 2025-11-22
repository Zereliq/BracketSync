<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tournaments_roles_users', function (Blueprint $table) {
            $table->id();

            $table->foreignId('role_id')
                ->constrained('tournamentroles')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('tournament_id')
                ->constrained('tournaments')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['role_id', 'tournament_id', 'user_id'], 'tur_role_tour_user_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tournaments_roles_users');
    }
};
