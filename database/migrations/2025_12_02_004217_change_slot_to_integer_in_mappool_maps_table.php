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
        // First, convert existing slots from "NM1" format to just the number
        $mappoolMaps = DB::table('mappool_maps')->get();
        foreach ($mappoolMaps as $mappoolMap) {
            $slot = $mappoolMap->slot;
            // Extract the number from strings like "NM1", "HD2", etc.
            $numericSlot = (int) preg_replace('/[^0-9]/', '', $slot);
            // If no number found, default to 1
            if ($numericSlot === 0) {
                $numericSlot = 1;
            }
            DB::table('mappool_maps')
                ->where('id', $mappoolMap->id)
                ->update(['slot' => $numericSlot]);
        }

        // Now change the column type to integer
        Schema::table('mappool_maps', function (Blueprint $table) {
            $table->unsignedInteger('slot')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Convert back to string and add mod_type prefix
        Schema::table('mappool_maps', function (Blueprint $table) {
            $table->string('slot')->change();
        });

        $mappoolMaps = DB::table('mappool_maps')->get();
        foreach ($mappoolMaps as $mappoolMap) {
            $slot = $mappoolMap->mod_type.$mappoolMap->slot;
            DB::table('mappool_maps')
                ->where('id', $mappoolMap->id)
                ->update(['slot' => $slot]);
        }
    }
};
