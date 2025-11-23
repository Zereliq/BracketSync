@extends('layouts.dashboard')

@section('title', 'Dashboard - BracketSync')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-white mb-2">Dashboard</h1>
        <p class="text-slate-400">Welcome back, {{ auth()->user()->name }}</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-pink-500/10 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-white mb-1">{{ $activeTournamentsCount }}</h3>
            <p class="text-slate-400 text-sm">Active Tournaments</p>
            <div class="mt-4 pt-4 border-t border-slate-800">
                <p class="text-xs text-slate-500">{{ 5 - $activeTournamentsCount }} slots remaining</p>
            </div>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-500/10 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-white mb-1">{{ $totalTournamentsCount }}</h3>
            <p class="text-slate-400 text-sm">Total Tournaments</p>
            <div class="mt-4 pt-4 border-t border-slate-800">
                <a href="{{ route('dashboard.tournaments.index') }}" class="text-xs text-purple-400 hover:text-purple-300">View all →</a>
            </div>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-500/10 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-white mb-1">{{ ucfirst(auth()->user()->siteRole?->name ?? 'Player') }}</h3>
            <p class="text-slate-400 text-sm">Account Role</p>
            <div class="mt-4 pt-4 border-t border-slate-800">
                <p class="text-xs text-slate-500">osu! ID: {{ auth()->user()->osu_id }}</p>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
        <h2 class="text-xl font-bold text-white mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('dashboard.tournaments.create') }}" class="flex items-center p-4 bg-slate-800/50 hover:bg-slate-800 rounded-lg transition-colors group border border-slate-700/50 hover:border-pink-500/50">
                <div class="w-10 h-10 bg-pink-500/10 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-white font-medium group-hover:text-pink-400 transition-colors">Create Tournament</h3>
                    <p class="text-slate-400 text-sm">Start a new tournament</p>
                </div>
            </a>

            <a href="{{ route('dashboard.tournaments.index') }}" class="flex items-center p-4 bg-slate-800/50 hover:bg-slate-800 rounded-lg transition-colors group border border-slate-700/50 hover:border-purple-500/50">
                <div class="w-10 h-10 bg-purple-500/10 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-white font-medium group-hover:text-purple-400 transition-colors">My Tournaments</h3>
                    <p class="text-slate-400 text-sm">View all tournaments</p>
                </div>
            </a>

            <a href="{{ route('tournaments.index') }}" class="flex items-center p-4 bg-slate-800/50 hover:bg-slate-800 rounded-lg transition-colors group border border-slate-700/50 hover:border-blue-500/50">
                <div class="w-10 h-10 bg-blue-500/10 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-white font-medium group-hover:text-blue-400 transition-colors">Browse Tournaments</h3>
                    <p class="text-slate-400 text-sm">Find tournaments to join</p>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
