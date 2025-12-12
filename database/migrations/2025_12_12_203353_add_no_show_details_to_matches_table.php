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
            // Track which team didn't show or was disqualified
            $table->foreignId('no_show_team_id')
                ->nullable()
                ->after('winner_team_id')
                ->constrained('teams')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            // Track the type of forfeit: 'no_show' or 'disqualified'
            $table->enum('no_show_type', ['no_show', 'disqualified'])
                ->nullable()
                ->after('no_show_team_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->dropForeign(['no_show_team_id']);
            $table->dropColumn('no_show_team_id');
            $table->dropColumn('no_show_type');
        });
    }
};
