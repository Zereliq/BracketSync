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
        Schema::table('users', function (Blueprint $table) {
            // osu! standard stats
            $table->integer('osu_rank')->nullable()->after('avatar_url');
            $table->decimal('osu_pp', 10, 2)->nullable()->after('osu_rank');
            $table->decimal('osu_hit_accuracy', 5, 2)->nullable()->after('osu_pp');

            // Taiko stats
            $table->integer('taiko_rank')->nullable()->after('osu_hit_accuracy');
            $table->decimal('taiko_pp', 10, 2)->nullable()->after('taiko_rank');
            $table->decimal('taiko_hit_accuracy', 5, 2)->nullable()->after('taiko_pp');

            // Catch the Beat (fruits) stats
            $table->integer('fruits_rank')->nullable()->after('taiko_hit_accuracy');
            $table->decimal('fruits_pp', 10, 2)->nullable()->after('fruits_rank');
            $table->decimal('fruits_hit_accuracy', 5, 2)->nullable()->after('fruits_pp');

            // Mania stats
            $table->integer('mania_rank')->nullable()->after('fruits_hit_accuracy');
            $table->decimal('mania_pp', 10, 2)->nullable()->after('mania_rank');
            $table->decimal('mania_hit_accuracy', 5, 2)->nullable()->after('mania_pp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'osu_rank', 'osu_pp', 'osu_hit_accuracy',
                'taiko_rank', 'taiko_pp', 'taiko_hit_accuracy',
                'fruits_rank', 'fruits_pp', 'fruits_hit_accuracy',
                'mania_rank', 'mania_pp', 'mania_hit_accuracy',
            ]);
        });
    }
};
