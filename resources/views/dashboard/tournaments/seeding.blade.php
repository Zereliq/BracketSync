@extends('layouts.dashboard')

@section('title', 'Configure Seeding - ' . $tournament->name)

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-8">
        <div class="flex items-center space-x-4 mb-4">
            <a href="{{ route('dashboard.tournaments.bracket', $tournament) }}" class="text-slate-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-white">Configure Seeding</h1>
                <p class="text-slate-400">Drag and drop {{ $isTeamTournament ? 'teams' : 'players' }} to set the bracket seeding order</p>
            </div>
        </div>
    </div>

    @if(session('info'))
        <div class="mb-6 bg-blue-500/20 border border-blue-500/30 text-blue-400 px-6 py-4 rounded-lg">
            {{ session('info') }}
        </div>
    @endif

    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
        <div class="mb-6">
            <h2 class="text-xl font-bold text-white mb-2">Seeding Order</h2>
            <p class="text-slate-400 text-sm">
                Drag and drop to reorder. The top {{ $isTeamTournament ? 'team' : 'player' }} will be seed #1, second will be seed #2, and so on.
            </p>
        </div>

        <form action="{{ route('dashboard.tournaments.bracket.seeding.generate', $tournament) }}" method="POST" id="seeding-form">
            @csrf

            <div id="sortable-participants" class="space-y-3 mb-6">
                @foreach($participants as $index => $participant)
                    <div class="participant-item bg-slate-800/50 border border-slate-700 rounded-lg p-4 cursor-move hover:border-pink-500/50 transition-colors"
                         data-id="{{ $participant->id }}">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                {{-- Drag Handle --}}
                                <div class="text-slate-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                    </svg>
                                </div>

                                {{-- Seed Number --}}
                                <div class="seed-number flex-shrink-0 w-12 h-12 rounded-full bg-pink-500/10 border-2 border-pink-500 flex items-center justify-center">
                                    <span class="text-pink-400 font-bold text-lg">{{ $index + 1 }}</span>
                                </div>

                                {{-- Participant Info --}}
                                <div class="flex-1">
                                    @if($isTeamTournament)
                                        <h3 class="text-white font-semibold">{{ $participant->name }}</h3>
                                        <p class="text-sm text-slate-400">
                                            {{ $participant->members->count() }} {{ Str::plural('member', $participant->members->count()) }}
                                        </p>
                                    @else
                                        <h3 class="text-white font-semibold">{{ $participant->user->name }}</h3>
                                        <div class="flex items-center gap-3 text-sm text-slate-400">
                                            @if($participant->user->rank)
                                                <span>Rank: #{{ number_format($participant->user->rank) }}</span>
                                            @endif
                                            @if($participant->user->country_code)
                                                <span class="uppercase">{{ $participant->user->country_code }}</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Hidden inputs for seeding order --}}
            <div id="seeding-inputs"></div>

            <div class="flex items-center justify-between pt-6 border-t border-slate-800">
                <a href="{{ route('dashboard.tournaments.bracket', $tournament) }}"
                   class="px-6 py-3 bg-slate-700 hover:bg-slate-600 text-white font-medium rounded-lg transition-colors">
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-3 bg-pink-500 hover:bg-pink-600 text-white font-medium rounded-lg transition-colors">
                    Generate Bracket with This Seeding
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sortableEl = document.getElementById('sortable-participants');
    const seedingInputs = document.getElementById('seeding-inputs');
    const form = document.getElementById('seeding-form');

    // Initialize Sortable
    const sortable = new Sortable(sortableEl, {
        animation: 150,
        handle: '.participant-item',
        ghostClass: 'opacity-50',
        onUpdate: function() {
            updateSeedNumbers();
            updateHiddenInputs();
        }
    });

    // Update seed numbers when order changes
    function updateSeedNumbers() {
        const items = sortableEl.querySelectorAll('.participant-item');
        items.forEach((item, index) => {
            const seedNumber = item.querySelector('.seed-number span');
            seedNumber.textContent = index + 1;
        });
    }

    // Update hidden inputs with current order
    function updateHiddenInputs() {
        seedingInputs.innerHTML = '';
        const items = sortableEl.querySelectorAll('.participant-item');
        items.forEach((item, index) => {
            const participantId = item.getAttribute('data-id');
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = `seeding_order[${participantId}]`;
            input.value = index + 1;
            seedingInputs.appendChild(input);
        });
    }

    // Initialize hidden inputs on page load
    updateHiddenInputs();
});
</script>
@endsection
