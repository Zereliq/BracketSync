<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tournaments', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('edition')->nullable();        // e.g. "2025", "Winter"
            $table->string('abbreviation')->nullable();   // short tag

            $table->boolean('qualifier_results_public')->default(false);

            $table->string('elim_type');                  // [single, double, caterpillar]
            $table->integer('bracket_size');              // [16,32,64,...]
            $table->integer('team_size');                 // 0=solo, 2,3,4,...

            $table->boolean('has_qualifiers')->default(false);
            $table->string('seeding_type');               // [custom, avg_score, mp_percent, points, drawing]
            $table->string('win_condition');              // [scoreV2, scoreV1, acc, combo]

            $table->string('signup_method');              // [self, host]
            $table->string('mode');                       // [standard, fruit, piano, drums]

            $table->string('signup_restriction')->nullable(); // [avg-rank, rank, country]
            $table->integer('rank_min')->nullable();
            $table->integer('rank_max')->nullable();

            $table->string('country_restriction_type')->nullable(); // [none, whitelist, blacklist]
            $table->text('country_list')->nullable();               // comma-separated codes

            $table->dateTime('signup_start');
            $table->dateTime('signup_end');

            $table->string('status')->default('draft');  // [draft, published, ongoing, finished, archived]

            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tournaments');
    }
};
