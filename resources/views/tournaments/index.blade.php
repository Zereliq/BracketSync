@extends('layouts.app')

@section('content')
<div x-data="tournamentFilters()" class="min-h-screen bg-slate-950">
    <div class="relative overflow-hidden py-12 bg-gradient-to-br from-pink-500/10 via-transparent to-fuchsia-500/10">
        <div class="max-w-6xl mx-auto px-4">
            <div class="text-center mb-8">
                <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">
                    All Tournaments
                </h1>
                <p class="text-lg text-slate-400">
                    Browse and join competitive osu! tournaments
                </p>
            </div>

            <!-- Filters -->
            <div class="bg-slate-900/50 rounded-2xl border border-slate-800 p-6 mb-8">
                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Mode Filter -->
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Mode</label>
                        <select x-model="filters.mode" @change="applyFilters()" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 text-slate-200 focus:border-pink-500 focus:outline-none">
                            <option value="">All Modes</option>
                            <option value="standard">osu! standard</option>
                            <option value="taiko">osu!taiko</option>
                            <option value="fruits">osu!catch</option>
                            <option value="mania">osu!mania</option>
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Status</label>
                        <select x-model="filters.status" @change="applyFilters()" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 text-slate-200 focus:border-pink-500 focus:outline-none">
                            <option value="">All Status</option>
                            <option value="published">Registration Open</option>
                            <option value="ongoing">Ongoing</option>
                        </select>
                    </div>

                    <!-- Team Size Filter -->
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Format</label>
                        <select x-model="filters.team_size" @change="applyFilters()" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 text-slate-200 focus:border-pink-500 focus:outline-none">
                            <option value="">All Formats</option>
                            <option value="solo">Solo (1v1)</option>
                            <option value="team">Team</option>
                        </select>
                    </div>

                    <!-- Sort By -->
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Sort By</label>
                        <select x-model="filters.sort" @change="applyFilters()" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 text-slate-200 focus:border-pink-500 focus:outline-none">
                            <option value="likes">Most Liked</option>
                            <option value="newest">Newest</option>
                            <option value="signup_start">Starting Soon</option>
                        </select>
                    </div>
                </div>

                <!-- Quick Filters -->
                @auth
                <div class="mt-4 flex flex-wrap gap-3">
                    <button
                        @click="toggleFilter('my_tournaments')"
                        :class="filters.my_tournaments ? 'bg-pink-500 text-white' : 'bg-slate-800 text-slate-300 hover:bg-slate-700'"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-colors border border-slate-700"
                    >
                        My Tournaments
                    </button>
                    <button
                        @click="toggleFilter('liked')"
                        :class="filters.liked ? 'bg-pink-500 text-white' : 'bg-slate-800 text-slate-300 hover:bg-slate-700'"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-colors border border-slate-700"
                    >
                        Liked Tournaments
                    </button>
                    <button
                        @click="clearFilters()"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-colors bg-slate-800 text-slate-300 hover:bg-slate-700 border border-slate-700"
                    >
                        Clear Filters
                    </button>
                </div>
                @endauth
            </div>
        </div>
    </div>

    <!-- Tournaments Grid -->
    <div class="max-w-6xl mx-auto px-4 pb-16">
        @if($tournaments->isEmpty())
            <div class="text-center py-16">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-800 mb-4">
                    <svg class="w-8 h-8 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-white mb-2">No tournaments found</h3>
                <p class="text-slate-400">Try adjusting your filters or check back later for new tournaments.</p>
            </div>
        @else
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($tournaments as $tournament)
                    <div
                        x-data="tournamentCard({{ $tournament->id }}, {{ in_array($tournament->id, $likedTournamentIds) ? 'true' : 'false' }}, {{ $tournament->likes_count }})"
                        class="bg-slate-900 rounded-2xl shadow-lg border border-slate-800 hover:border-pink-500/50 transition-all group relative"
                    >
                        <!-- Clickable Card Content -->
                        <a href="{{ route('tournaments.show', $tournament) }}" class="block p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <h3 class="text-lg font-bold text-white group-hover:text-pink-400 transition-colors">
                                        {{ $tournament->name }}
                                    </h3>
                                    @if($tournament->edition || $tournament->abbreviation)
                                        <p class="text-sm text-slate-400 mt-1">
                                            {{ $tournament->abbreviation ?? $tournament->edition }}
                                        </p>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2 ml-4">
                                    <span class="px-3 py-1 text-xs font-medium rounded-full border whitespace-nowrap
                                        @if($tournament->status === 'ongoing')
                                            bg-green-500/20 text-green-400 border-green-500/30
                                        @elseif($tournament->status === 'published')
                                            bg-blue-500/20 text-blue-400 border-blue-500/30
                                        @else
                                            bg-slate-500/20 text-slate-400 border-slate-500/30
                                        @endif
                                    ">
                                        @if($tournament->status === 'ongoing')
                                            Ongoing
                                        @elseif($tournament->status === 'published' && $tournament->signupsOpen())
                                            Registration Open
                                        @elseif($tournament->status === 'published')
                                            Published
                                        @else
                                            {{ ucfirst($tournament->status) }}
                                        @endif
                                    </span>
                                </div>
                            </div>

                            <div class="space-y-3 text-sm">
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500">Mode</span>
                                    <span class="text-slate-200 font-medium">
                                        @if($tournament->mode === 'standard')
                                            osu! standard
                                        @elseif($tournament->mode === 'taiko')
                                            osu!taiko
                                        @elseif($tournament->mode === 'fruits')
                                            osu!catch
                                        @elseif($tournament->mode === 'mania')
                                            osu!mania
                                        @else
                                            {{ ucfirst($tournament->mode) }}
                                        @endif
                                    </span>
                                </div>

                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500">Format</span>
                                    <span class="text-slate-200 font-medium">
                                        {{ $tournament->format }}v{{ $tournament->format }}, {{ ucfirst($tournament->elim_type) }} Elim
                                    </span>
                                </div>

                                @if($tournament->rank_min || $tournament->rank_max)
                                    <div class="flex items-center justify-between">
                                        <span class="text-slate-500">Rank Range</span>
                                        <span class="text-slate-200 font-medium">{{ $tournament->getRankRangeDisplay() }}</span>
                                    </div>
                                @endif
                            </div>
                        </a>

                        <!-- Footer with Like Button -->
                        <div class="px-6 pb-4 pt-3 border-t border-slate-800 flex items-center justify-between">
                            @auth
                            <button
                                @click.prevent="toggleLike()"
                                class="flex items-center gap-1.5 text-xs transition-colors group/like"
                                :class="liked ? 'text-pink-500' : 'text-slate-400 hover:text-pink-400'"
                            >
                                <svg class="w-5 h-5 transition-transform group-hover/like:scale-110" :class="liked ? 'fill-current' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                                <span x-text="likesCount" class="font-medium"></span>
                            </button>
                            @else
                            <div class="flex items-center gap-1.5 text-slate-400 text-xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                                <span x-text="likesCount" class="font-medium"></span>
                            </div>
                            @endauth
                            @if($tournament->status === 'published' && $tournament->signupsOpen())
                                <p class="text-slate-400 text-xs">
                                    Registration closes {{ $tournament->signup_end->format('M d, Y') }}
                                </p>
                            @elseif($tournament->status === 'ongoing')
                                <p class="text-slate-400 text-xs">
                                    {{ ucfirst($tournament->getCurrentStage()) }}
                                </p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function tournamentFilters() {
    return {
        filters: {
            mode: new URLSearchParams(window.location.search).get('mode') || '',
            status: new URLSearchParams(window.location.search).get('status') || '',
            team_size: new URLSearchParams(window.location.search).get('team_size') || '',
            sort: new URLSearchParams(window.location.search).get('sort') || 'likes',
            my_tournaments: new URLSearchParams(window.location.search).get('my_tournaments') === 'true',
            liked: new URLSearchParams(window.location.search).get('liked') === 'true'
        },

        applyFilters() {
            const params = new URLSearchParams();

            Object.keys(this.filters).forEach(key => {
                if (this.filters[key] && this.filters[key] !== '') {
                    params.set(key, this.filters[key]);
                }
            });

            window.location.search = params.toString();
        },

        toggleFilter(filterName) {
            this.filters[filterName] = !this.filters[filterName];
            this.applyFilters();
        },

        clearFilters() {
            window.location.href = window.location.pathname;
        }
    }
}

// Tournament card component with like functionality
function tournamentCard(tournamentId, initialLiked, initialCount) {
    return {
        tournamentId: tournamentId,
        liked: initialLiked,
        likesCount: initialCount,
        loading: false,

        async toggleLike() {
            if (this.loading) return;

            this.loading = true;
            const wasLiked = this.liked;

            // Optimistic update
            this.liked = !wasLiked;
            this.likesCount = wasLiked ? this.likesCount - 1 : this.likesCount + 1;

            try {
                const method = wasLiked ? 'DELETE' : 'POST';
                const response = await fetch(`/tournaments/${this.tournamentId}/like`, {
                    method: method,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });

                if (!response.ok) {
                    if (response.status === 401) {
                        window.location.href = '/auth/osu/redirect';
                        return;
                    }
                    throw new Error('Failed to toggle like');
                }

                const data = await response.json();
                // Update with actual count from server
                this.likesCount = data.likes_count;
            } catch (error) {
                // Revert on error
                console.error('Error toggling like:', error);
                this.liked = wasLiked;
                this.likesCount = wasLiked ? this.likesCount + 1 : this.likesCount - 1;
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
@endpush
@endsection
