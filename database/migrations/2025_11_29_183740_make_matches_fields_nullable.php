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
        Schema::table('matches', function (Blueprint $table) {
            $table->foreignId('mappool_id')
                ->nullable()
                ->change();

            $table->foreignId('team1_id')
                ->nullable()
                ->change();

            $table->foreignId('team2_id')
                ->nullable()
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->foreignId('mappool_id')
                ->nullable(false)
                ->change();

            $table->foreignId('team1_id')
                ->nullable(false)
                ->change();

            $table->foreignId('team2_id')
                ->nullable(false)
                ->change();
        });
    }
};
