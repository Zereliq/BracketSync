<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('siteroles', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // [dev, host, owner, user, ...]
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siteroles');
    }
};
