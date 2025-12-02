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
        Schema::table('maps', function (Blueprint $table) {
            $table->string('cover_url')->nullable()->after('beatmap_url');
            $table->decimal('bpm', 6, 2)->nullable()->after('length_seconds');
            $table->decimal('cs', 4, 2)->nullable()->after('bpm');
            $table->decimal('ar', 4, 2)->nullable()->after('cs');
            $table->decimal('od', 4, 2)->nullable()->after('ar');
            $table->decimal('hp', 4, 2)->nullable()->after('od');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maps', function (Blueprint $table) {
            $table->dropColumn(['cover_url', 'bpm', 'cs', 'ar', 'od', 'hp']);
        });
    }
};
