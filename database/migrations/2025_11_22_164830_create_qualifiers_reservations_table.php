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
        Schema::create('qualifiers_reservations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('qualifiers_slot_id')
                ->nullable()
                ->constrained('qualifiers_slots')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('tournament_id')
                ->constrained('tournaments')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('team_id')
                ->nullable()
                ->constrained('teams')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('reserved_by_user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->enum('status', ['reserved', 'checked_in', 'played', 'no_show', 'cancelled'])->default('reserved');
            $table->dateTime('suggested_time')->nullable();
            $table->text('comment')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qualifiers_reservations');
    }
};
