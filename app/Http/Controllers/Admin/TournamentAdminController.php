<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use Illuminate\Http\Request;

class TournamentAdminController extends Controller
{
    /**
     * Display a listing of all tournaments (admin view).
     */
    public function index(): mixed
    {
        $tournaments = Tournament::with('creator')
            ->latest()
            ->paginate(20);

        return view('dashboard.admin.tournaments.index', compact('tournaments'));
    }
}
