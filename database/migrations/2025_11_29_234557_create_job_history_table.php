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
        Schema::create('job_history', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->nullable();
            $table->string('job_name')->nullable();
            $table->longText('payload')->nullable();
            $table->string('status'); // 'completed', 'failed'
            $table->longText('exception')->nullable();
            $table->integer('attempts')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('duration_ms')->nullable(); // Duration in milliseconds
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index('job_name');
            $table->index('queue');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_history');
    }
};
