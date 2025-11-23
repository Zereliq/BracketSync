@php
    $isDashboard = $isDashboard ?? request()->routeIs('dashboard.*');
    $layout = $isDashboard ? 'layouts.dashboard' : 'layouts.app';
@endphp

@extends($layout)

@section('title', $tournament->name . ' - BracketSync')

@section('content')
@if($isDashboard)
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
            @case('bracket')
                @include('tournaments.tabs.bracket')
                @break
            @case('mappools')
                @include('tournaments.tabs.mappools')
                @break
            @default
                @include('tournaments.tabs.tournament')
        @endswitch
    </div>
@else
    <div class="min-h-screen bg-slate-950 py-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h1 class="text-4xl font-bold text-white mb-2">{{ $tournament->name }}</h1>
                        @if($tournament->edition)
                            <p class="text-xl text-slate-400">{{ $tournament->edition }}</p>
                        @endif
                    </div>
                    <div>
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium
                            @if($tournament->status === 'published') bg-green-500/10 text-green-400 border border-green-500/30
                            @elseif($tournament->status === 'ongoing') bg-blue-500/10 text-blue-400 border border-blue-500/30
                            @elseif($tournament->status === 'finished') bg-purple-500/10 text-purple-400 border border-purple-500/30
                            @elseif($tournament->status === 'archived') bg-slate-500/10 text-slate-400 border border-slate-500/30
                            @else bg-yellow-500/10 text-yellow-400 border border-yellow-500/30
                            @endif">
                            {{ ucfirst($tournament->status) }}
                        </span>
                    </div>
                </div>

                @if($tournament->abbreviation)
                    <p class="text-slate-400">{{ $tournament->abbreviation }}</p>
                @endif
            </div>

            @include('tournaments.partials.tabs')

            @switch($currentTab)
                @case('tournament')
                    @include('tournaments.tabs.tournament')
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
                @case('bracket')
                    @include('tournaments.tabs.bracket')
                    @break
                @case('mappools')
                    @include('tournaments.tabs.mappools')
                    @break
                @default
                    @include('tournaments.tabs.tournament')
            @endswitch
        </div>
    </div>
@endif
@endsection
