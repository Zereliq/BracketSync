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
        Schema::create('tournament_role_permissions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('role_id')
                ->constrained('tournamentroles')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->enum('resource', [
                'tournament',
                'staff',
                'players',
                'teams',
                'qualifiers',
                'matches',
                'bracket',
                'mappools',
            ])->comment('The tab/section this permission applies to');

            $table->enum('permission', ['none', 'view', 'edit'])
                ->default('none')
                ->comment('none = hidden, view = read-only, edit = full access');

            $table->timestamps();

            $table->unique(['role_id', 'resource'], 'role_resource_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournament_role_permissions');
    }
};
