<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mappool_maps', function (Blueprint $table) {
            $table->id();

            $table->foreignId('mappool_id')
                ->constrained('mappools')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('map_id')
                ->constrained('maps')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('slot');           // "NM1", "HD2", "TB1"
            $table->string('mod_type');       // [NM, HD, HR, DT, FM, TB, ...]
            $table->boolean('is_tiebreaker')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mappool_maps');
    }
};
