<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maps', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('osu_beatmap_id')->unique();
            $table->unsignedBigInteger('osu_beatmapset_id')->nullable();

            $table->string('artist');
            $table->string('title');
            $table->string('version');                 // difficulty name
            $table->string('mode');                    // [standard, fruit, piano, drums]

            $table->decimal('star_rating', 4, 2)->nullable();
            $table->integer('length_seconds')->nullable();
            $table->string('mapper')->nullable();

            $table->string('beatmap_url');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maps');
    }
};
