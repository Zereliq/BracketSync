@extends('layouts.app')

@section('title', 'User Settings - BracketSync Tournaments')

@section('content')
<div class="min-h-screen bg-slate-950 py-12">
    <div class="max-w-4xl mx-auto px-4">
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">Account Settings</h1>
            <p class="text-slate-400">Manage your personal information and preferences</p>
        </div>

        {{-- Success Message --}}
        @if(session('success'))
            <div class="mb-6 bg-green-500/10 border border-green-500/50 rounded-xl p-4 flex items-start space-x-3">
                <svg class="w-6 h-6 text-green-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-green-400">{{ session('success') }}</p>
            </div>
        @endif

        {{-- Profile Section --}}
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 mb-6">
            <h2 class="text-xl font-bold text-white mb-6">osu! Profile</h2>

            <div class="flex items-center space-x-4 p-4 bg-slate-800/50 rounded-lg border border-slate-700">
                @if($user->avatar_url)
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-16 h-16 rounded-full border-2 border-pink-500">
                @endif
                <div>
                    <p class="text-white font-bold text-lg">{{ $user->name }}</p>
                    <p class="text-slate-400 text-sm">Connected via osu!</p>
                    @if($user->rank)
                        <p class="text-pink-400 text-sm mt-1">Rank: #{{ number_format($user->rank) }}</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Personal Information Form --}}
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
            <h2 class="text-xl font-bold text-white mb-6">Personal Information</h2>

            <form action="{{ route('settings.update') }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-300 mb-2">
                        Email Address
                        <span class="text-slate-500">(optional)</span>
                    </label>
                    <div class="relative">
                        <input type="email"
                               name="email"
                               id="email"
                               value="{{ old('email', $user->email) }}"
                               class="w-full bg-slate-800 border border-slate-700 text-white rounded-lg px-4 py-2.5 pl-10 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                               placeholder="your.email@example.com">
                        <svg class="w-5 h-5 text-slate-500 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    @error('email')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-slate-500">Your email will be kept private and used for notifications only</p>
                </div>

                {{-- Discord Username --}}
                <div>
                    <label for="discord_username" class="block text-sm font-medium text-slate-300 mb-2">
                        Discord Username
                        <span class="text-slate-500">(optional)</span>
                    </label>
                    <div class="relative">
                        <input type="text"
                               name="discord_username"
                               id="discord_username"
                               value="{{ old('discord_username', $user->discord_username) }}"
                               class="w-full bg-slate-800 border border-slate-700 text-white rounded-lg px-4 py-2.5 pl-10 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                               placeholder="username">
                        <svg class="w-5 h-5 text-slate-500 absolute left-3 top-3" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20.317 4.37a19.791 19.791 0 00-4.885-1.515.074.074 0 00-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 00-5.487 0 12.64 12.64 0 00-.617-1.25.077.077 0 00-.079-.037A19.736 19.736 0 003.677 4.37a.07.07 0 00-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 00.031.057 19.9 19.9 0 005.993 3.03.078.078 0 00.084-.028c.462-.63.874-1.295 1.226-1.994a.076.076 0 00-.041-.106 13.107 13.107 0 01-1.872-.892.077.077 0 01-.008-.128 10.2 10.2 0 00.372-.292.074.074 0 01.077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 01.078.01c.12.098.246.198.373.292a.077.077 0 01-.006.127 12.299 12.299 0 01-1.873.892.077.077 0 00-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 00.084.028 19.839 19.839 0 006.002-3.03.077.077 0 00.032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 00-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.418-2.157 2.418z"/>
                        </svg>
                    </div>
                    @error('discord_username')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-slate-500">Your Discord username for easy communication with staff and teammates</p>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center justify-between pt-4 border-t border-slate-800">
                    <a href="{{ route('dashboard.index') }}" class="text-slate-400 hover:text-white transition-colors">
                        Back to Dashboard
                    </a>
                    <button type="submit"
                            class="px-6 py-2.5 bg-pink-500 hover:bg-pink-600 text-white font-medium rounded-lg transition-colors shadow-lg shadow-pink-500/20">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>

        {{-- Danger Zone --}}
        <div class="bg-red-500/5 border border-red-500/20 rounded-xl p-6 mt-6">
            <h2 class="text-xl font-bold text-red-400 mb-4">Clear Information</h2>
            <p class="text-slate-400 mb-4">Remove your email and Discord username from your account.</p>

            <form action="{{ route('settings.update') }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="email" value="">
                <input type="hidden" name="discord_username" value="">

                <button type="submit"
                        onclick="return confirm('Are you sure you want to remove your email and Discord username?')"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors">
                    Clear All Personal Data
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
