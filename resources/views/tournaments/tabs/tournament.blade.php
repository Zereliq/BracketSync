@php
    $isDashboard = request()->routeIs('dashboard.*');
    $canEdit = auth()->check() && auth()->user()->can('update', $tournament);
@endphp

<div x-data="{ editing: {{ $errors->any() ? 'true' : 'false' }}, showPublishModal: false }">
    @can('update', $tournament)
        <div class="flex justify-end gap-3 mb-6">
            @if($tournament->canManageStaff() && $tournament->status !== 'announced')
                <button @click="showPublishModal = true"
                        type="button"
                        class="px-6 py-2.5 bg-green-500 hover:bg-green-600 text-white font-medium rounded-lg transition-colors flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Publish Tournament</span>
                </button>
            @endif

            <button @click="editing = !editing; if(editing) { $nextTick(() => { document.getElementById('edit-form')?.scrollIntoView({ behavior: 'smooth', block: 'start' }) }) }"
                    type="button"
                    class="px-6 py-2.5 bg-fuchsia-500 hover:bg-fuchsia-600 text-white font-medium rounded-lg transition-colors flex items-center space-x-2">
                <svg x-show="!editing" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                <svg x-show="editing" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                <span x-text="editing ? 'Cancel Editing' : 'Edit Tournament'"></span>
            </button>
        </div>
    @endcan

    <!-- Publish Confirmation Modal -->
    <div x-show="showPublishModal"
         x-cloak
         @click.self="showPublishModal = false"
         class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
         x-transition:enter="transition-opacity ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div @click.stop
             class="bg-slate-900 border border-slate-800 rounded-xl shadow-2xl max-w-md w-full p-6"
             x-transition:enter="transition-all ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition-all ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">

            <div class="flex items-center space-x-3 mb-4">
                <div class="w-12 h-12 bg-yellow-500/10 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white">Publish Tournament</h3>
            </div>

            <p class="text-slate-300 mb-6">
                Are you sure you want to make this tournament public? Once published, the tournament will be visible to all users and players can start registering.
            </p>

            <div class="flex gap-3">
                <button @click="showPublishModal = false"
                        type="button"
                        class="flex-1 px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-medium rounded-lg transition-colors">
                    Cancel
                </button>
                <form action="{{ route('dashboard.tournaments.publish', $tournament) }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit"
                            class="w-full px-4 py-2.5 bg-green-500 hover:bg-green-600 text-white font-medium rounded-lg transition-colors">
                        Publish
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- View Mode (Always Visible when not editing) -->
    <div x-show="!editing" x-cloak>
        <div class="space-y-6">
            <!-- Hero Header -->
            <div class="bg-gradient-to-br from-slate-900 via-slate-900 to-fuchsia-900/20 border border-slate-800 rounded-xl p-8">
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                    <div>
                        <h2 class="text-3xl font-bold text-white mb-2">{{ $tournament->name }}</h2>
                        @if($tournament->edition || $tournament->abbreviation)
                            <div class="flex flex-wrap items-center gap-3 text-slate-300">
                                @if($tournament->edition)
                                    <span class="text-lg">{{ $tournament->edition }}</span>
                                @endif
                                @if($tournament->abbreviation)
                                    <span class="px-3 py-1 bg-slate-800/50 rounded-full text-sm font-medium">{{ $tournament->abbreviation }}</span>
                                @endif
                            </div>
                        @endif
                    </div>
                    <div>
                        <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold border-2
                            @if($tournament->status === 'published') bg-green-500/10 text-green-400 border-green-500/30
                            @elseif($tournament->status === 'ongoing') bg-blue-500/10 text-blue-400 border-blue-500/30
                            @elseif($tournament->status === 'finished') bg-purple-500/10 text-purple-400 border-purple-500/30
                            @elseif($tournament->status === 'archived') bg-slate-500/10 text-slate-400 border-slate-500/30
                            @else bg-yellow-500/10 text-yellow-400 border-yellow-500/30
                            @endif">
                            {{ ucfirst($tournament->status) }}
                        </span>
                    </div>
                </div>
                @if($tournament->description)
                    <div class="mt-4 text-slate-300 leading-relaxed markdown-content">
                        {!! str($tournament->description)->markdown() !!}
                    </div>
                @endif
            </div>

            <!-- Tournament Format -->
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
                <h3 class="text-xl font-bold text-white mb-6 flex items-center space-x-2">
                    <svg class="w-6 h-6 text-fuchsia-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                    </svg>
                    <span>Tournament Format</span>
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="bg-slate-800/50 rounded-lg p-4 border border-slate-700/50">
                        <p class="text-sm text-slate-400 mb-1">Game Mode</p>
                        <p class="text-white font-semibold text-lg">
                            @if($tournament->mode === 'standard') osu!standard
                            @elseif($tournament->mode === 'piano') osu!mania
                            @elseif($tournament->mode === 'fruit') osu!catch
                            @elseif($tournament->mode === 'drums') osu!taiko
                            @else {{ ucfirst($tournament->mode) }}
                            @endif
                        </p>
                    </div>
                    <div class="bg-slate-800/50 rounded-lg p-4 border border-slate-700/50">
                        <p class="text-sm text-slate-400 mb-1">Team Size</p>
                        <p class="text-white font-semibold text-lg">{{ $tournament->getFormattedTeamSize() }}</p>
                    </div>
                    <div class="bg-slate-800/50 rounded-lg p-4 border border-slate-700/50">
                        <p class="text-sm text-slate-400 mb-1">Bracket Size</p>
                        <p class="text-white font-semibold text-lg">{{ $tournament->bracket_size }} teams</p>
                    </div>
                    <div class="bg-slate-800/50 rounded-lg p-4 border border-slate-700/50">
                        <p class="text-sm text-slate-400 mb-1">Elimination Type</p>
                        <p class="text-white font-semibold text-lg">{{ ucfirst($tournament->elim_type) }}</p>
                    </div>
                    <div class="bg-slate-800/50 rounded-lg p-4 border border-slate-700/50">
                        <p class="text-sm text-slate-400 mb-1">Win Condition</p>
                        <p class="text-white font-semibold text-lg">{{ $tournament->win_condition }}</p>
                    </div>
                    <div class="bg-slate-800/50 rounded-lg p-4 border border-slate-700/50">
                        <p class="text-sm text-slate-400 mb-1">Signup Method</p>
                        <p class="text-white font-semibold text-lg">{{ ucfirst($tournament->signup_method) }}</p>
                    </div>
                    <div class="bg-slate-800/50 rounded-lg p-4 border border-slate-700/50">
                        <p class="text-sm text-slate-400 mb-1">Staff Can Play</p>
                        <p class="text-white font-semibold text-lg">
                            @if($tournament->staff_can_play)
                                <span class="text-green-400">Yes</span>
                            @else
                                <span class="text-red-400">No</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Qualifiers & Seeding -->
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
                <h3 class="text-xl font-bold text-white mb-6 flex items-center space-x-2">
                    <svg class="w-6 h-6 text-fuchsia-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                    <span>Qualifiers & Seeding</span>
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-slate-800/50 rounded-lg p-4 border border-slate-700/50">
                        <p class="text-sm text-slate-400 mb-1">Has Qualifiers</p>
                        <p class="text-white font-semibold text-lg">
                            @if($tournament->has_qualifiers)
                                <span class="text-green-400">Yes</span>
                            @else
                                <span class="text-slate-400">No</span>
                            @endif
                        </p>
                    </div>
                    <div class="bg-slate-800/50 rounded-lg p-4 border border-slate-700/50">
                        <p class="text-sm text-slate-400 mb-1">Seeding Type</p>
                        <p class="text-white font-semibold text-lg">{{ ucfirst(str_replace('_', ' ', $tournament->seeding_type)) }}</p>
                    </div>
                    @if($tournament->has_qualifiers)
                        <div class="bg-slate-800/50 rounded-lg p-4 border border-slate-700/50">
                            <p class="text-sm text-slate-400 mb-1">Qualifier Results Public</p>
                            <p class="text-white font-semibold text-lg">
                                @if($tournament->qualifier_results_public)
                                    <span class="text-green-400">Yes</span>
                                @else
                                    <span class="text-red-400">No</span>
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Signup & Restrictions -->
            @if($tournament->signup_start || $tournament->signup_end || $tournament->signup_restriction || $tournament->rank_min || $tournament->rank_max || $tournament->country_restriction_type !== 'none')
                <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
                    <h3 class="text-xl font-bold text-white mb-6 flex items-center space-x-2">
                        <svg class="w-6 h-6 text-fuchsia-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <span>Signup Information</span>
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if($tournament->signup_start)
                            <div class="bg-slate-800/50 rounded-lg p-4 border border-slate-700/50">
                                <p class="text-sm text-slate-400 mb-1">Signup Opens</p>
                                <p class="text-white font-semibold text-lg">{{ $tournament->signup_start->format('F j, Y') }}</p>
                            </div>
                        @endif
                        @if($tournament->signup_end)
                            <div class="bg-slate-800/50 rounded-lg p-4 border border-slate-700/50">
                                <p class="text-sm text-slate-400 mb-1">Signup Closes</p>
                                <p class="text-white font-semibold text-lg">{{ $tournament->signup_end->format('F j, Y') }}</p>
                            </div>
                        @endif
                        @if($tournament->signup_restriction)
                            <div class="bg-slate-800/50 rounded-lg p-4 border border-slate-700/50">
                                <p class="text-sm text-slate-400 mb-1">Signup Restriction</p>
                                <p class="text-white font-semibold text-lg">{{ ucfirst(str_replace('-', ' ', $tournament->signup_restriction)) }}</p>
                            </div>
                        @endif
                        @if($tournament->rank_min || $tournament->rank_max)
                            <div class="bg-slate-800/50 rounded-lg p-4 border border-slate-700/50">
                                <p class="text-sm text-slate-400 mb-1">Rank Range</p>
                                <p class="text-white font-semibold text-lg">{{ $tournament->getRankRangeDisplay() }}</p>
                            </div>
                        @endif
                        @if($tournament->country_restriction_type && $tournament->country_restriction_type !== 'none')
                            <div class="bg-slate-800/50 rounded-lg p-4 border border-slate-700/50">
                                <p class="text-sm text-slate-400 mb-1">Country Restriction</p>
                                <p class="text-white font-semibold text-lg">
                                    {{ ucfirst($tournament->country_restriction_type) }}
                                    @if($tournament->country_list && count($tournament->country_list) > 0)
                                        <span class="text-sm font-normal text-slate-300">
                                            ({{ implode(', ', $tournament->country_list) }})
                                        </span>
                                    @endif
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Edit Mode (Only for authorized users) -->
    @can('update', $tournament)
        <div x-show="editing" x-cloak id="edit-form">
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

            <form method="POST" action="{{ route('dashboard.tournaments.update', $tournament) }}" class="space-y-8"
                  x-data="{
                      format: {{ old('format', $tournament->format) }},
                      minTeamsize: {{ old('min_teamsize', $tournament->min_teamsize) }},
                      maxTeamsize: {{ old('max_teamsize', $tournament->max_teamsize) }},
                      countryRestrictionType: '{{ old('country_restriction_type', $tournament->country_restriction_type ?? 'none') }}'
                  }">
                @csrf
                @method('PATCH')

                <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
                    <h2 class="text-xl font-bold text-white mb-6">Basic Information</h2>
                    <div class="space-y-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-slate-300 mb-2">Tournament Name *</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $tournament->name) }}" required
                                   class="w-full bg-slate-800 border border-slate-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="edition" class="block text-sm font-medium text-slate-300 mb-2">Edition</label>
                                <input type="text" name="edition" id="edition" value="{{ old('edition', $tournament->edition) }}"
                                       placeholder="e.g., 2024, Winter, #1"
                                       class="w-full bg-slate-800 border border-slate-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                            </div>

                            <div>
                                <label for="abbreviation" class="block text-sm font-medium text-slate-300 mb-2">Abbreviation</label>
                                <input type="text" name="abbreviation" id="abbreviation" value="{{ old('abbreviation', $tournament->abbreviation) }}"
                                       placeholder="e.g., OWC, TST"
                                       class="w-full bg-slate-800 border border-slate-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                            </div>
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-slate-300 mb-2">Description</label>
                            <textarea name="description" id="description" rows="4"
                                      placeholder="Describe your tournament... (Markdown supported)"
                                      class="w-full bg-slate-800 border border-slate-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">{{ old('description', $tournament->description) }}</textarea>
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
                                <option value="standard" {{ old('mode', $tournament->mode) == 'standard' ? 'selected' : '' }}>osu!standard</option>
                                <option value="piano" {{ old('mode', $tournament->mode) == 'piano' ? 'selected' : '' }}>osu!mania</option>
                                <option value="fruit" {{ old('mode', $tournament->mode) == 'fruit' ? 'selected' : '' }}>osu!catch</option>
                                <option value="drums" {{ old('mode', $tournament->mode) == 'drums' ? 'selected' : '' }}>osu!taiko</option>
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
                                    <option value="single" {{ old('elim_type', $tournament->elim_type) == 'single' ? 'selected' : '' }}>Single Elimination</option>
                                    <option value="double" {{ old('elim_type', $tournament->elim_type) == 'double' ? 'selected' : '' }}>Double Elimination</option>
                                    <option value="caterpillar" {{ old('elim_type', $tournament->elim_type) == 'caterpillar' ? 'selected' : '' }}>Caterpillar</option>
                                </select>
                            </div>

                            <div>
                                <label for="bracket_size" class="block text-sm font-medium text-slate-300 mb-2">Bracket Size *</label>
                                <select name="bracket_size" id="bracket_size" required
                                        class="w-full bg-slate-800 border border-slate-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                    <option value="">Select size</option>
                                    <option value="8" {{ old('bracket_size', $tournament->bracket_size) == '8' ? 'selected' : '' }}>8 teams</option>
                                    <option value="16" {{ old('bracket_size', $tournament->bracket_size) == '16' ? 'selected' : '' }}>16 teams</option>
                                    <option value="32" {{ old('bracket_size', $tournament->bracket_size) == '32' ? 'selected' : '' }}>32 teams</option>
                                    <option value="64" {{ old('bracket_size', $tournament->bracket_size) == '64' ? 'selected' : '' }}>64 teams</option>
                                    <option value="128" {{ old('bracket_size', $tournament->bracket_size) == '128' ? 'selected' : '' }}>128 teams</option>
                                </select>
                                <div class="mt-3 flex items-center">
                                    <input type="hidden" name="auto_bracket_size" value="0">
                                    <input type="checkbox" name="auto_bracket_size" id="auto_bracket_size" value="1" {{ old('auto_bracket_size', $tournament->auto_bracket_size) ? 'checked' : '' }}
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
                                    <option value="scoreV2" {{ old('win_condition', $tournament->win_condition) == 'scoreV2' ? 'selected' : '' }}>Score V2</option>
                                    <option value="scoreV1" {{ old('win_condition', $tournament->win_condition) == 'scoreV1' ? 'selected' : '' }}>Score V1</option>
                                    <option value="acc" {{ old('win_condition', $tournament->win_condition) == 'acc' ? 'selected' : '' }}>Accuracy</option>
                                    <option value="combo" {{ old('win_condition', $tournament->win_condition) == 'combo' ? 'selected' : '' }}>Combo</option>
                                </select>
                            </div>

                            <div>
                                <label for="signup_method" class="block text-sm font-medium text-slate-300 mb-2">Signup Method *</label>
                                <select name="signup_method" id="signup_method" required
                                        class="w-full bg-slate-800 border border-slate-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                    <option value="">Select method</option>
                                    <option value="self" {{ old('signup_method', $tournament->signup_method) == 'self' ? 'selected' : '' }}>Self Signup</option>
                                    <option value="host" {{ old('signup_method', $tournament->signup_method) == 'host' ? 'selected' : '' }}>Host Invites</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <input type="hidden" name="staff_can_play" value="0">
                            <input type="checkbox" name="staff_can_play" id="staff_can_play" value="1" {{ old('staff_can_play', $tournament->staff_can_play) ? 'checked' : '' }}
                                   class="w-4 h-4 text-pink-500 bg-slate-800 border-slate-700 rounded focus:ring-pink-500">
                            <label for="staff_can_play" class="ml-2 text-sm font-medium text-slate-300">Allow staff members to play in the tournament</label>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
                    <h2 class="text-xl font-bold text-white mb-6">Qualifiers & Seeding</h2>
                    <div class="space-y-6">
                        <div class="flex items-center">
                            <input type="hidden" name="has_qualifiers" value="0">
                            <input type="checkbox" name="has_qualifiers" id="has_qualifiers" value="1" {{ old('has_qualifiers', $tournament->has_qualifiers) ? 'checked' : '' }}
                                   class="w-4 h-4 text-pink-500 bg-slate-800 border-slate-700 rounded focus:ring-pink-500">
                            <label for="has_qualifiers" class="ml-2 text-sm font-medium text-slate-300">Has Qualifiers</label>
                        </div>

                        <div>
                            <label for="seeding_type" class="block text-sm font-medium text-slate-300 mb-2">Seeding Type *</label>
                            <select name="seeding_type" id="seeding_type" required
                                    class="w-full bg-slate-800 border border-slate-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                <option value="">Select seeding type</option>
                                <option value="custom" {{ old('seeding_type', $tournament->seeding_type) == 'custom' ? 'selected' : '' }}>Custom</option>
                                <option value="avg_score" {{ old('seeding_type', $tournament->seeding_type) == 'avg_score' ? 'selected' : '' }}>Average Score</option>
                                <option value="mp_percent" {{ old('seeding_type', $tournament->seeding_type) == 'mp_percent' ? 'selected' : '' }}>Match Point Percent</option>
                                <option value="points" {{ old('seeding_type', $tournament->seeding_type) == 'points' ? 'selected' : '' }}>Points</option>
                                <option value="drawing" {{ old('seeding_type', $tournament->seeding_type) == 'drawing' ? 'selected' : '' }}>Random Drawing</option>
                            </select>
                        </div>

                        <div class="flex items-center">
                            <input type="hidden" name="qualifier_results_public" value="0">
                            <input type="checkbox" name="qualifier_results_public" id="qualifier_results_public" value="1" {{ old('qualifier_results_public', $tournament->qualifier_results_public) ? 'checked' : '' }}
                                   class="w-4 h-4 text-pink-500 bg-slate-800 border-slate-700 rounded focus:ring-pink-500">
                            <label for="qualifier_results_public" class="ml-2 text-sm font-medium text-slate-300">Make Qualifier Results Public</label>
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
                                <option value="rank" {{ old('signup_restriction', $tournament->signup_restriction) == 'rank' ? 'selected' : '' }}>Rank Range</option>
                                <option value="avg-rank" {{ old('signup_restriction', $tournament->signup_restriction) == 'avg-rank' ? 'selected' : '' }} x-show="format > 1 || minTeamsize > 1 || maxTeamsize > 1">Average Rank (Team Tournaments Only)</option>
                                <option value="badge-weighted" {{ old('signup_restriction', $tournament->signup_restriction) == 'badge-weighted' ? 'selected' : '' }} x-show="format > 1 || minTeamsize > 1 || maxTeamsize > 1">Badge-Weighted Rank (Team Tournaments Only)</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="rank_min" class="block text-sm font-medium text-slate-300 mb-2">Minimum Rank</label>
                                <input type="number" name="rank_min" id="rank_min" value="{{ old('rank_min', $tournament->rank_min) }}"
                                       placeholder="e.g., 1000"
                                       class="w-full bg-slate-800 border border-slate-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                            </div>

                            <div>
                                <label for="rank_max" class="block text-sm font-medium text-slate-300 mb-2">Maximum Rank</label>
                                <input type="number" name="rank_max" id="rank_max" value="{{ old('rank_max', $tournament->rank_max) }}"
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
                            <input type="text" name="country_list_input" id="country_list_input" value="{{ old('country_list') ? implode(', ', old('country_list')) : ($tournament->country_list ? implode(', ', $tournament->country_list) : '') }}"
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
                            <input type="date" name="signup_start" id="signup_start" value="{{ old('signup_start', $tournament->signup_start?->format('Y-m-d')) }}"
                                   class="w-full bg-slate-800 border border-slate-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                        </div>

                        <div>
                            <label for="signup_end" class="block text-sm font-medium text-slate-300 mb-2">Signup End</label>
                            <input type="date" name="signup_end" id="signup_end" value="{{ old('signup_end', $tournament->signup_end?->format('Y-m-d')) }}"
                                   class="w-full bg-slate-800 border border-slate-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                        </div>
                    </div>
                </div>

                {{-- Status is auto-managed and not editable by staff --}}

                <div class="flex items-center justify-end space-x-4">
                    <button type="button" @click="editing = false" class="px-6 py-3 bg-slate-800 hover:bg-slate-700 text-white font-medium rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-8 py-3 bg-pink-500 hover:bg-pink-600 text-white font-medium rounded-lg transition-colors">
                        Update Tournament
                    </button>
                </div>
            </form>
        </div>
    @endcan
</div>

<style>
    [x-cloak] { display: none !important; }

    /* Markdown content styling */
    .markdown-content h1, .markdown-content h2, .markdown-content h3, .markdown-content h4, .markdown-content h5, .markdown-content h6 {
        color: #fff;
        font-weight: 700;
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
    }
    .markdown-content h1 { font-size: 2rem; }
    .markdown-content h2 { font-size: 1.5rem; }
    .markdown-content h3 { font-size: 1.25rem; }
    .markdown-content p {
        margin-bottom: 1rem;
    }
    .markdown-content strong {
        color: #fff;
        font-weight: 600;
    }
    .markdown-content em {
        font-style: italic;
    }
    .markdown-content a {
        color: #e879f9;
        text-decoration: underline;
    }
    .markdown-content a:hover {
        color: #f0abfc;
    }
    .markdown-content ul, .markdown-content ol {
        margin-left: 1.5rem;
        margin-bottom: 1rem;
    }
    .markdown-content ul {
        list-style-type: disc;
    }
    .markdown-content ol {
        list-style-type: decimal;
    }
    .markdown-content li {
        margin-bottom: 0.5rem;
    }
    .markdown-content code {
        background-color: rgba(100, 116, 139, 0.3);
        padding: 0.125rem 0.375rem;
        border-radius: 0.25rem;
        font-size: 0.875rem;
        font-family: monospace;
    }
    .markdown-content pre {
        background-color: rgba(100, 116, 139, 0.3);
        padding: 1rem;
        border-radius: 0.5rem;
        overflow-x: auto;
        margin-bottom: 1rem;
    }
    .markdown-content pre code {
        background-color: transparent;
        padding: 0;
    }
    .markdown-content blockquote {
        border-left: 4px solid #e879f9;
        padding-left: 1rem;
        margin-left: 0;
        margin-bottom: 1rem;
        color: #cbd5e1;
        font-style: italic;
    }
    .markdown-content hr {
        border-top: 1px solid #475569;
        margin: 1.5rem 0;
    }
    .markdown-content table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 1rem;
    }
    .markdown-content th, .markdown-content td {
        border: 1px solid #475569;
        padding: 0.5rem;
        text-align: left;
    }
    .markdown-content th {
        background-color: rgba(100, 116, 139, 0.3);
        font-weight: 600;
        color: #fff;
    }
</style>
