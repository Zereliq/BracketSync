@extends('layouts.dashboard')

@section('title', 'Create Tournament - Dashboard')

@section('content')
<div class="max-w-4xl">
    <div class="mb-8">
        <div class="flex items-center space-x-4 mb-4">
            <a href="{{ route('dashboard.tournaments.index') }}" class="text-slate-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-white">Create Tournament</h1>
                <p class="text-slate-400">Set up a new tournament</p>
            </div>
        </div>
    </div>

    @if(session('error'))
        <div class="mb-6 bg-red-500/20 border border-red-500/30 text-red-400 px-6 py-4 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 bg-red-500/20 border border-red-500/30 text-red-400 px-6 py-4 rounded-lg">
            <p class="font-medium mb-2">Please fix the following errors:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('dashboard.tournaments.store') }}" class="space-y-8"
          x-data="{
              format: {{ old('format', 1) }},
              minTeamsize: {{ old('min_teamsize', 1) }},
              maxTeamsize: {{ old('max_teamsize', 1) }},
              countryRestrictionType: '{{ old('country_restriction_type', 'none') }}',
              hasQualifiers: {{ old('has_qualifiers') ? 'true' : 'false' }},
              nameLength: {{ old('name') ? strlen(old('name')) : 0 }},
              editionLength: {{ old('edition') ? strlen(old('edition')) : 0 }},
              abbreviationLength: {{ old('abbreviation') ? strlen(old('abbreviation')) : 0 }},
              descriptionLength: {{ old('description') ? strlen(old('description')) : 0 }}
          }">
        @csrf

        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
            <h2 class="text-xl font-bold text-white mb-6">Basic Information</h2>
            <div class="space-y-6">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="name" class="block text-sm font-medium text-slate-300">Tournament Name *</label>
                        <span class="text-xs text-slate-400" x-text="nameLength + '/255'"></span>
                    </div>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required maxlength="255"
                           @input="nameLength = $event.target.value.length"
                           class="w-full bg-slate-800 border border-slate-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="edition" class="block text-sm font-medium text-slate-300">Edition</label>
                            <span class="text-xs text-slate-400" x-text="editionLength + '/100'"></span>
                        </div>
                        <input type="text" name="edition" id="edition" value="{{ old('edition') }}" maxlength="100"
                               @input="editionLength = $event.target.value.length"
                               placeholder="e.g., 2024, Winter, #1"
                               class="w-full bg-slate-800 border border-slate-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="abbreviation" class="block text-sm font-medium text-slate-300">Abbreviation</label>
                            <span class="text-xs text-slate-400" x-text="abbreviationLength + '/20'"></span>
                        </div>
                        <input type="text" name="abbreviation" id="abbreviation" value="{{ old('abbreviation') }}" maxlength="20"
                               @input="abbreviationLength = $event.target.value.length"
                               placeholder="e.g., OWC, TST"
                               class="w-full bg-slate-800 border border-slate-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="description" class="block text-sm font-medium text-slate-300">Description</label>
                        <span class="text-xs text-slate-400" x-text="descriptionLength + '/5000'"></span>
                    </div>
                    <textarea name="description" id="description" rows="4" maxlength="5000"
                              @input="descriptionLength = $event.target.value.length"
                              placeholder="Describe your tournament... (Markdown supported)"
                              class="w-full bg-slate-800 border border-slate-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">{{ old('description') }}</textarea>
                    <p class="mt-1 text-sm text-slate-400">Supports Markdown: **bold**, *italic*, [links](url), lists, etc.</p>
                </div>
            </div>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
            <h2 class="text-xl font-bold text-white mb-6">Format</h2>
            <div class="space-y-6">
                <div>
                    <label for="mode" class="block text-sm font-medium text-slate-300 mb-2">Game Mode *</label>
                    <select name="mode" id="mode" required
                            class="w-full bg-slate-800 border border-slate-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                        <option value="">Select mode</option>
                        <option value="standard" {{ old('mode') == 'standard' ? 'selected' : '' }}>osu!standard</option>
                        <option value="piano" {{ old('mode') == 'piano' ? 'selected' : '' }}>osu!mania</option>
                        <option value="fruit" {{ old('mode') == 'fruit' ? 'selected' : '' }}>osu!catch</option>
                        <option value="drums" {{ old('mode') == 'drums' ? 'selected' : '' }}>osu!taiko</option>
                    </select>
                </div>

                <div>
                    <label for="format" class="block text-sm font-medium text-slate-300 mb-2">Format *</label>
                    <input type="number" name="format" id="format" x-model="format" min="1" max="8" required
                           class="w-full bg-slate-800 border border-slate-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                    <p class="mt-1 text-sm text-slate-400">e.g., 1 = 1v1, 2 = 2v2, 3 = 3v3</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="min_teamsize" class="block text-sm font-medium text-slate-300 mb-2">Minimum Team Size *</label>
                        <input type="number" name="min_teamsize" id="min_teamsize" x-model="minTeamsize" min="1" max="8" required
                               class="w-full bg-slate-800 border border-slate-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                    </div>

                    <div>
                        <label for="max_teamsize" class="block text-sm font-medium text-slate-300 mb-2">Maximum Team Size *</label>
                        <input type="number" name="max_teamsize" id="max_teamsize" x-model="maxTeamsize" min="1" max="8" required
                               class="w-full bg-slate-800 border border-slate-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="elim_type" class="block text-sm font-medium text-slate-300 mb-2">Elimination Type *</label>
                        <select name="elim_type" id="elim_type" required
                                class="w-full bg-slate-800 border border-slate-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                            <option value="">Select type</option>
                            <option value="single" {{ old('elim_type') == 'single' ? 'selected' : '' }}>Single Elimination</option>
                            <option value="double" {{ old('elim_type') == 'double' ? 'selected' : '' }}>Double Elimination</option>
                            <option value="caterpillar" {{ old('elim_type') == 'caterpillar' ? 'selected' : '' }}>Caterpillar</option>
                        </select>
                    </div>

                    <div>
                        <label for="bracket_size" class="block text-sm font-medium text-slate-300 mb-2">
                            Bracket Size *
                            <span class="text-xs text-slate-500 font-normal">(<span x-text="minTeamsize === 1 && maxTeamsize === 1 ? 'players' : 'teams'">teams</span>)</span>
                        </label>
                        <select name="bracket_size" id="bracket_size" required
                                class="w-full bg-slate-800 border border-slate-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                            <option value="">Select size</option>
                            <option value="8" {{ old('bracket_size') == '8' ? 'selected' : '' }}>8</option>
                            <option value="16" {{ old('bracket_size') == '16' ? 'selected' : '' }}>16</option>
                            <option value="32" {{ old('bracket_size') == '32' ? 'selected' : '' }}>32</option>
                            <option value="64" {{ old('bracket_size') == '64' ? 'selected' : '' }}>64</option>
                            <option value="128" {{ old('bracket_size') == '128' ? 'selected' : '' }}>128</option>
                        </select>
                        <div class="mt-3 flex items-center">
                            <input type="hidden" name="auto_bracket_size" value="0">
                            <input type="checkbox" name="auto_bracket_size" id="auto_bracket_size" value="1" {{ old('auto_bracket_size') ? 'checked' : '' }}
                                   class="w-4 h-4 text-pink-500 bg-slate-800 border-slate-700 rounded focus:ring-pink-500">
                            <label for="auto_bracket_size" class="ml-2 text-sm font-medium text-slate-300">Automatically adjust bracket size based on signups</label>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="win_condition" class="block text-sm font-medium text-slate-300 mb-2">Win Condition *</label>
                        <select name="win_condition" id="win_condition" required
                                class="w-full bg-slate-800 border border-slate-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                            <option value="">Select condition</option>
                            <option value="scoreV2" {{ old('win_condition') == 'scoreV2' ? 'selected' : '' }}>Score V2</option>
                            <option value="scoreV1" {{ old('win_condition') == 'scoreV1' ? 'selected' : '' }}>Score V1</option>
                            <option value="acc" {{ old('win_condition') == 'acc' ? 'selected' : '' }}>Accuracy</option>
                            <option value="combo" {{ old('win_condition') == 'combo' ? 'selected' : '' }}>Combo</option>
                        </select>
                    </div>

                    <div>
                        <label for="signup_method" class="block text-sm font-medium text-slate-300 mb-2">Signup Method *</label>
                        <select name="signup_method" id="signup_method" required
                                class="w-full bg-slate-800 border border-slate-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                            <option value="">Select method</option>
                            <option value="self" {{ old('signup_method') == 'self' ? 'selected' : '' }}>Self Signup</option>
                            <option value="invitationals" {{ old('signup_method') == 'invitationals' ? 'selected' : '' }}>Invitationals</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center">
                    <input type="hidden" name="staff_can_play" value="0">
                    <input type="checkbox" name="staff_can_play" id="staff_can_play" value="1" {{ old('staff_can_play', true) ? 'checked' : '' }}
                           class="w-4 h-4 text-pink-500 bg-slate-800 border-slate-700 rounded focus:ring-pink-500">
                    <label for="staff_can_play" class="ml-2 text-sm font-medium text-slate-300">Allow staff members to play in the tournament</label>
                </div>
            </div>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
            <h2 class="text-xl font-bold text-white mb-6">Qualifiers & Seeding</h2>
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="flex items-center">
                        <input type="hidden" name="has_qualifiers" value="0">
                        <input type="checkbox" name="has_qualifiers" id="has_qualifiers" value="1" {{ old('has_qualifiers') ? 'checked' : '' }}
                               x-model="hasQualifiers"
                               class="w-4 h-4 text-pink-500 bg-slate-800 border-slate-700 rounded focus:ring-pink-500">
                        <label for="has_qualifiers" class="ml-2 text-sm font-medium text-slate-300">Has Qualifiers</label>
                    </div>

                    <div class="flex items-center">
                        <input type="hidden" name="qualifier_results_public" value="0">
                        <input type="checkbox" name="qualifier_results_public" id="qualifier_results_public" value="1" {{ old('qualifier_results_public') ? 'checked' : '' }}
                               class="w-4 h-4 text-pink-500 bg-slate-800 border-slate-700 rounded focus:ring-pink-500">
                        <label for="qualifier_results_public" class="ml-2 text-sm font-medium text-slate-300">Make Qualifier Results Public</label>
                    </div>
                </div>

                <div>
                    <label for="seeding_type" class="block text-sm font-medium text-slate-300 mb-2">Seeding Type *</label>
                    <select name="seeding_type" id="seeding_type" required
                            class="w-full bg-slate-800 border border-slate-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                        <option value="">Select seeding type</option>
                        <option value="rank" {{ old('seeding_type') == 'rank' ? 'selected' : '' }}>Rank</option>
                        <option value="custom" {{ old('seeding_type') == 'custom' ? 'selected' : '' }}>Custom</option>
                        <option value="avg_score" {{ old('seeding_type') == 'avg_score' ? 'selected' : '' }} x-show="hasQualifiers">Average Score</option>
                        <option value="mp_percent" {{ old('seeding_type') == 'mp_percent' ? 'selected' : '' }} x-show="hasQualifiers">Match Point Percent</option>
                        <option value="points" {{ old('seeding_type') == 'points' ? 'selected' : '' }} x-show="hasQualifiers">Points</option>
                        <option value="drawing" {{ old('seeding_type') == 'drawing' ? 'selected' : '' }} x-show="hasQualifiers">Random Drawing</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
            <h2 class="text-xl font-bold text-white mb-6">Restrictions</h2>
            <div class="space-y-6">
                <div>
                    <label for="signup_restriction" class="block text-sm font-medium text-slate-300 mb-2">Signup Restriction</label>
                    <select name="signup_restriction" id="signup_restriction"
                            class="w-full bg-slate-800 border border-slate-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                        <option value="">No restriction</option>
                        <option value="rank" {{ old('signup_restriction') == 'rank' ? 'selected' : '' }}>Rank Range</option>
                        <option value="avg-rank" {{ old('signup_restriction') == 'avg-rank' ? 'selected' : '' }} x-show="format > 1 || minTeamsize > 1 || maxTeamsize > 1">Average Rank (Team Tournaments Only)</option>
                        <option value="badge-weighted" {{ old('signup_restriction') == 'badge-weighted' ? 'selected' : '' }} x-show="format > 1 || minTeamsize > 1 || maxTeamsize > 1">Badge-Weighted Rank (Team Tournaments Only)</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="rank_min" class="block text-sm font-medium text-slate-300 mb-2">Minimum Rank</label>
                        <input type="number" name="rank_min" id="rank_min" value="{{ old('rank_min') }}"
                               placeholder="e.g., 1000"
                               class="w-full bg-slate-800 border border-slate-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                    </div>

                    <div>
                        <label for="rank_max" class="block text-sm font-medium text-slate-300 mb-2">Maximum Rank</label>
                        <input type="number" name="rank_max" id="rank_max" value="{{ old('rank_max') }}"
                               placeholder="e.g., 10000"
                               class="w-full bg-slate-800 border border-slate-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                    </div>
                </div>

                <div>
                    <label for="country_restriction_type" class="block text-sm font-medium text-slate-300 mb-2">Country Restriction</label>
                    <select name="country_restriction_type" id="country_restriction_type" x-model="countryRestrictionType"
                            class="w-full bg-slate-800 border border-slate-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                        <option value="none">None</option>
                        <option value="whitelist">Whitelist (Only allow specific countries)</option>
                        <option value="blacklist">Blacklist (Block specific countries)</option>
                    </select>
                </div>

                <div x-show="countryRestrictionType === 'whitelist' || countryRestrictionType === 'blacklist'" x-cloak>
                    <label for="country_list_input" class="block text-sm font-medium text-slate-300 mb-2">
                        <span x-text="countryRestrictionType === 'whitelist' ? 'Allowed Countries' : 'Blocked Countries'"></span>
                    </label>
                    <input type="text" name="country_list_input" id="country_list_input" value="{{ old('country_list') ? implode(', ', old('country_list')) : '' }}"
                           placeholder="e.g., US, CA, GB, JP, AU"
                           class="w-full bg-slate-800 border border-slate-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                    <p class="mt-1 text-sm text-slate-400">Enter 2-letter country codes separated by commas</p>
                </div>
            </div>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
            <h2 class="text-xl font-bold text-white mb-6">Dates</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="signup_start" class="block text-sm font-medium text-slate-300 mb-2">Signup Start</label>
                    <input type="date" name="signup_start" id="signup_start" value="{{ old('signup_start') }}"
                           class="w-full bg-slate-800 border border-slate-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                </div>

                <div>
                    <label for="signup_end" class="block text-sm font-medium text-slate-300 mb-2">Signup End</label>
                    <input type="date" name="signup_end" id="signup_end" value="{{ old('signup_end') }}"
                           class="w-full bg-slate-800 border border-slate-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                </div>
            </div>
        </div>

        {{-- <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
            <h2 class="text-xl font-bold text-white mb-6">Status</h2>
            <div>
                <label for="status" class="block text-sm font-medium text-slate-300 mb-2">Tournament Status</label>
                <select name="status" id="status"
                        class="w-full bg-slate-800 border border-slate-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                    <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
                    <option value="ongoing" {{ old('status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                    <option value="finished" {{ old('status') == 'finished' ? 'selected' : '' }}>Finished</option>
                    <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
            </div>
        </div> --}}

        <div class="flex items-center justify-between">
            <a href="{{ route('dashboard.tournaments.index') }}" class="px-6 py-3 bg-slate-800 hover:bg-slate-700 text-white font-medium rounded-lg transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-8 py-3 bg-pink-500 hover:bg-pink-600 text-white font-medium rounded-lg transition-colors">
                Create Tournament
            </button>
        </div>
    </form>
</div>
@endsection
