<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('scores', function (Blueprint $table) {
            // Add match_id relationship if it doesn't exist
            if (! Schema::hasColumn('scores', 'match_id')) {
                $table->foreignId('match_id')
                    ->after('id')
                    ->constrained('matches')
                    ->cascadeOnUpdate()
                    ->cascadeOnDelete();
            }

            // Add map_number to track order of maps in the match
            if (! Schema::hasColumn('scores', 'map_number')) {
                $table->integer('map_number')->after('match_id')->default(1);
            }

            // Add mappool_map_id (nullable for manual entries without specific map)
            if (! Schema::hasColumn('scores', 'mappool_map_id')) {
                $table->foreignId('mappool_map_id')
                    ->nullable()
                    ->after('map_number')
                    ->constrained('mappool_maps')
                    ->cascadeOnUpdate()
                    ->cascadeOnDelete();
            }

            // Add winning_team_id to track which team won this specific map
            if (! Schema::hasColumn('scores', 'winning_team_id')) {
                $table->foreignId('winning_team_id')
                    ->nullable()
                    ->after('team_id')
                    ->constrained('teams')
                    ->cascadeOnUpdate()
                    ->cascadeOnDelete();
            }
        });

        // Drop game_id foreign key and column in separate statement
        // First check if the foreign key exists
        $foreignKeys = DB::select(
            "SELECT CONSTRAINT_NAME
             FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = DATABASE()
             AND TABLE_NAME = 'scores'
             AND COLUMN_NAME = 'game_id'
             AND REFERENCED_TABLE_NAME IS NOT NULL"
        );

        if (! empty($foreignKeys)) {
            $constraintName = $foreignKeys[0]->CONSTRAINT_NAME;
            DB::statement("ALTER TABLE scores DROP FOREIGN KEY {$constraintName}");
        }

        // Drop the column if it exists
        if (Schema::hasColumn('scores', 'game_id')) {
            Schema::table('scores', function (Blueprint $table) {
                $table->dropColumn('game_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scores', function (Blueprint $table) {
            // Restore game_id
            $table->foreignId('game_id')
                ->after('id')
                ->constrained('games')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // Remove new columns
            $table->dropForeign(['match_id']);
            $table->dropColumn('match_id');
            $table->dropColumn('map_number');
            $table->dropForeign(['mappool_map_id']);
            $table->dropColumn('mappool_map_id');
            $table->dropForeign(['winning_team_id']);
            $table->dropColumn('winning_team_id');
        });
    }
};
