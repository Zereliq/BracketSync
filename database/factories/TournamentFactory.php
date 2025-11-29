<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tournament>
 */
class TournamentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $modes = ['standard', 'taiko', 'fruits', 'mania'];
        $elimTypes = ['single', 'double'];
        $bracketSizes = [16, 32, 64];
        $seedingTypes = ['custom', 'avg_score', 'mp_percent', 'points', 'drawing'];
        $winConditions = ['scoreV2', 'scoreV1', 'acc', 'combo'];
        $signupMethods = ['self', 'invitationals'];

        return [
            'name' => fake()->words(3, true).' Tournament',
            'edition' => fake()->year(),
            'abbreviation' => strtoupper(fake()->lexify('???')),
            'qualifier_results_public' => fake()->boolean(),
            'elim_type' => fake()->randomElement($elimTypes),
            'bracket_size' => fake()->randomElement($bracketSizes),
            'format' => fake()->numberBetween(1, 4),
            'min_teamsize' => 1,
            'max_teamsize' => 4,
            'has_qualifiers' => fake()->boolean(),
            'seeding_type' => fake()->randomElement($seedingTypes),
            'win_condition' => fake()->randomElement($winConditions),
            'signup_method' => fake()->randomElement($signupMethods),
            'mode' => fake()->randomElement($modes),
            'signup_restriction' => fake()->randomElement(['rank', 'avg-rank', 'country', null]),
            'rank_min' => fake()->optional()->numberBetween(1, 100000),
            'rank_max' => fake()->optional()->numberBetween(100000, 1000000),
            'country_restriction_type' => fake()->randomElement(['none', 'whitelist', 'blacklist']),
            'country_list' => [],
            'signup_start' => now()->addDays(fake()->numberBetween(-30, 0)),
            'signup_end' => now()->addDays(fake()->numberBetween(1, 30)),
            'status' => 'published',
            'created_by' => \App\Models\User::factory(),
        ];
    }
}
