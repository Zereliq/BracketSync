@extends('layouts.dashboard')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('dashboard.tournaments.show', ['tournament' => $tournament, 'tab' => 'mappools']) }}" class="text-pink-400 hover:text-pink-300 transition-colors inline-flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Back to Mappools</span>
        </a>
    </div>

    <div class="bg-slate-900 border border-slate-800 rounded-xl p-8">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-white">Create Mappool</h1>
            <p class="text-slate-400 mt-2">Create a new mappool for {{ $tournament->name }}</p>
        </div>

        <form action="{{ route('dashboard.tournaments.mappools.store', $tournament) }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-slate-300 mb-2">
                    Mappool Name <span class="text-red-400">*</span>
                </label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                    placeholder="e.g., Qualifiers Pool"
                    required
                >
                @error('name')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="stage" class="block text-sm font-medium text-slate-300 mb-2">
                    Stage <span class="text-red-400">*</span>
                </label>
                <input
                    type="text"
                    id="stage"
                    name="stage"
                    value="{{ old('stage') }}"
                    class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                    placeholder="e.g., Qualifiers, Round of 16, Quarterfinals"
                    required
                >
                @error('stage')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="flex items-center space-x-3 cursor-pointer">
                    <input
                        type="checkbox"
                        name="is_public"
                        value="1"
                        {{ old('is_public') ? 'checked' : '' }}
                        class="w-5 h-5 bg-slate-800 border-slate-700 rounded text-pink-500 focus:ring-2 focus:ring-pink-500 focus:ring-offset-0"
                    >
                    <div>
                        <span class="text-sm font-medium text-slate-300">Make mappool public</span>
                        <p class="text-xs text-slate-500">If checked, this mappool will be visible to all players</p>
                    </div>
                </label>
                @error('is_public')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-slate-800">
                <a href="{{ route('dashboard.tournaments.show', ['tournament' => $tournament, 'tab' => 'mappools']) }}" class="px-6 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-medium rounded-lg transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-pink-500 hover:bg-pink-600 text-white font-medium rounded-lg transition-colors inline-flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Create Mappool</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
