@extends('layouts.dashboard')

@section('title', 'Edit Tournament - Dashboard')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-8">
        <div class="flex items-center space-x-4 mb-4">
            <a href="{{ route('dashboard.tournaments.index') }}" class="text-slate-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-white">{{ $tournament->name }}</h1>
                <p class="text-slate-400">Manage tournament settings and configuration</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-500/20 border border-green-500/30 text-green-400 px-6 py-4 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-500/20 border border-red-500/30 text-red-400 px-6 py-4 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    @include('tournaments.partials.tabs')

    @switch($currentTab)
        @case('tournament')
            @include('tournaments.tabs.tournament')
            @break
        @case('bracket')
            <div class="bg-slate-900 border border-slate-800 rounded-lg p-6">
                <h2 class="text-xl font-bold text-white mb-4">Tournament Bracket</h2>
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-slate-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 17a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1v-2zM14 17a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1h-4a1 1 0 01-1-1v-2z"></path>
                    </svg>
                    <p class="text-slate-400 text-lg">Bracket functionality coming soon</p>
                    <p class="text-slate-500 text-sm mt-2">The bracket view will display the tournament structure and matches</p>
                </div>
            </div>
            @break
        @case('staff')
            @include('tournaments.tabs.staff')
            @break
        @case('players')
            @include('tournaments.tabs.players')
            @break
        @case('teams')
            @include('tournaments.tabs.teams')
            @break
        @case('qualifiers')
            @include('tournaments.tabs.qualifiers')
            @break
        @case('matches')
            @include('tournaments.tabs.matches')
            @break
        @case('mappools')
            @include('tournaments.tabs.mappools')
            @break
        @default
            @include('tournaments.tabs.tournament')
    @endswitch
</div>
@endsection
