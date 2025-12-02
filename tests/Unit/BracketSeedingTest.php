<?php

use App\Services\BracketGenerationService;

test('generates correct seeding for 8 player bracket', function () {
    $service = new BracketGenerationService;
    $reflection = new ReflectionClass($service);
    $method = $reflection->getMethod('getSingleElimMatchups');
    $method->setAccessible(true);

    $matchups = $method->invoke($service, 8);

    // Expected: 1v8, 4v5, 2v7, 3v6
    expect($matchups)->toBe([
        [1, 8],
        [4, 5],
        [2, 7],
        [3, 6],
    ]);
});

test('generates correct seeding for 16 player bracket', function () {
    $service = new BracketGenerationService;
    $reflection = new ReflectionClass($service);
    $method = $reflection->getMethod('getSingleElimMatchups');
    $method->setAccessible(true);

    $matchups = $method->invoke($service, 16);

    // Expected: 1v16, 8v9, 4v13, 5v12, 2v15, 7v10, 3v14, 6v11
    expect($matchups)->toBe([
        [1, 16],
        [8, 9],
        [4, 13],
        [5, 12],
        [2, 15],
        [7, 10],
        [3, 14],
        [6, 11],
    ]);
});

test('generates correct seeding for 32 player bracket', function () {
    $service = new BracketGenerationService;
    $reflection = new ReflectionClass($service);
    $method = $reflection->getMethod('getSingleElimMatchups');
    $method->setAccessible(true);

    $matchups = $method->invoke($service, 32);

    // First 8 matchups for validation
    expect($matchups[0])->toBe([1, 32]);
    expect($matchups[1])->toBe([16, 17]);
    expect($matchups[2])->toBe([8, 25]);
    expect($matchups[3])->toBe([9, 24]);
    expect($matchups[4])->toBe([4, 29]);
    expect($matchups[5])->toBe([13, 20]);
    expect($matchups[6])->toBe([5, 28]);
    expect($matchups[7])->toBe([12, 21]);

    // Verify total count
    expect($matchups)->toHaveCount(16);
});

test('seed 1 and 2 are in opposite bracket halves', function () {
    $service = new BracketGenerationService;
    $reflection = new ReflectionClass($service);
    $method = $reflection->getMethod('getSingleElimMatchups');
    $method->setAccessible(true);

    $matchups = $method->invoke($service, 8);

    // Find which matches contain seed 1 and seed 2
    $seed1MatchIndex = null;
    $seed2MatchIndex = null;

    foreach ($matchups as $index => $matchup) {
        if (in_array(1, $matchup)) {
            $seed1MatchIndex = $index;
        }
        if (in_array(2, $matchup)) {
            $seed2MatchIndex = $index;
        }
    }

    // For an 8-player bracket, seed 1 should be in match 0 or 1 (top half)
    // and seed 2 should be in match 2 or 3 (bottom half)
    $halfSize = count($matchups) / 2;
    $seed1InTopHalf = $seed1MatchIndex < $halfSize;
    $seed2InBottomHalf = $seed2MatchIndex >= $halfSize;

    expect($seed1InTopHalf)->toBeTrue();
    expect($seed2InBottomHalf)->toBeTrue();
});
