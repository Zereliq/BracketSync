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
        Schema::table('tournamentroles', function (Blueprint $table) {
            $table->foreignId('tournament_id')
                ->nullable()
                ->after('id')
                ->constrained('tournaments')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->boolean('is_protected')
                ->default(false)
                ->after('name')
                ->comment('Protected roles cannot be deleted or modified');

            $table->string('description')->nullable()->after('is_protected');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tournamentroles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tournament_id');
            $table->dropColumn(['is_protected', 'description']);
        });
    }
};
