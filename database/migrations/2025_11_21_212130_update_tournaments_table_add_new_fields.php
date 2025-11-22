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
        Schema::table('tournaments', function (Blueprint $table) {
            // Add new team size fields
            $table->integer('min_teamsize')->default(1)->after('bracket_size');
            $table->integer('max_teamsize')->default(1)->after('min_teamsize');

            // Rename team_size to format
            $table->renameColumn('team_size', 'format');

            // Add auto bracket size
            $table->boolean('auto_bracket_size')->default(false)->after('bracket_size');

            // Add description field
            $table->text('description')->nullable()->after('abbreviation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tournaments', function (Blueprint $table) {
            $table->dropColumn(['min_teamsize', 'max_teamsize', 'auto_bracket_size', 'description']);
            $table->renameColumn('format', 'team_size');
        });
    }
};
