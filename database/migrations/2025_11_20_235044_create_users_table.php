<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // osu! identity (authoritative)
            $table->unsignedBigInteger('osu_id')->unique();
            $table->string('name');                 // display name (osu! username)
            $table->string('osu_username')->nullable(); // optional separate field if you want
            $table->string('avatar_url')->nullable();
            $table->string('country_code', 2)->nullable(); // "NL", "US", etc.
            $table->string('mode', 16)->nullable();        // "osu", "taiko", etc.

            // Platform stuff
            $table->integer('elo')->default(0);     // future stat

            $table->foreignId('siterole_id')
                ->constrained('siteroles')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            // If you want to store osu OAuth tokens (optional but common):
            $table->string('osu_access_token', 191)->nullable();
            $table->string('osu_refresh_token', 191)->nullable();
            $table->dateTime('osu_token_expires_at')->nullable();

            // You *can* still keep an email for notifications, but it's not used for login.
            $table->string('email')->nullable()->unique();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
