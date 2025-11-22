<?php

namespace App\Http\Controllers;

use App\Models\MatchModel;
use Illuminate\Http\Request;

class MatchController extends Controller
{
    /**
     * Display a listing of matches.
     */
    public function index(): mixed
    {
        $matches = MatchModel::with(['tournament', 'referee'])
            ->latest()
            ->paginate(15);

        return view('dashboard.matches.index', compact('matches'));
    }

    /**
     * Display the specified match.
     */
    public function show(MatchModel $match): mixed
    {
        $match->load(['tournament', 'referee', 'scores']);

        return view('dashboard.matches.show', compact('match'));
    }
}
