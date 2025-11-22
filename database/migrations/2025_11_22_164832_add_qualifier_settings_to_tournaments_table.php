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
            $table->boolean('is_badged')->default(false)->after('has_qualifiers');
            $table->enum('qualifier_mode', ['slots_only', 'suggest_only', 'slots_and_suggest'])->default('slots_and_suggest')->after('is_badged');
            $table->boolean('qualifiers_required_referee')->default(false)->after('qualifier_mode');
            $table->integer('qualifiers_slot_length_minutes')->default(20)->after('qualifiers_required_referee');
            $table->dateTime('qualifiers_signup_deadline')->nullable()->after('qualifiers_slot_length_minutes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tournaments', function (Blueprint $table) {
            $table->dropColumn([
                'is_badged',
                'qualifier_mode',
                'qualifiers_required_referee',
                'qualifiers_slot_length_minutes',
                'qualifiers_signup_deadline',
            ]);
        });
    }
};
