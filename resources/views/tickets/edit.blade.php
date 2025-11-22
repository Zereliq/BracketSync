@extends('layouts.dashboard')

@section('title', 'Manage Ticket #' . $ticket->id . ' - BracketSync')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <a href="{{ route('dashboard.tickets.show', $ticket) }}" class="inline-flex items-center text-slate-400 hover:text-white mb-4 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Ticket
        </a>
        <h1 class="text-3xl font-bold text-white mb-2">Manage Ticket #{{ $ticket->id }}</h1>
        <p class="text-slate-400">{{ $ticket->subject }}</p>
    </div>

    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
        <h2 class="text-xl font-bold text-white mb-6">Ticket Details</h2>

        <div class="mb-6 p-4 bg-slate-800/50 rounded-lg">
            <p class="text-sm text-slate-500 mb-2">Submitted by {{ $ticket->user->name }}</p>
            <p class="text-white whitespace-pre-wrap">{{ $ticket->description }}</p>
        </div>

        <form action="{{ route('dashboard.tickets.update', $ticket) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="status" class="block text-sm font-medium text-slate-300 mb-2">Status</label>
                <select
                    id="status"
                    name="status"
                    class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                    <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Open</option>
                    <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                    <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
                @error('status')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="priority" class="block text-sm font-medium text-slate-300 mb-2">Priority</label>
                <select
                    id="priority"
                    name="priority"
                    class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                    <option value="low" {{ $ticket->priority === 'low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ $ticket->priority === 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high" {{ $ticket->priority === 'high' ? 'selected' : '' }}>High</option>
                </select>
                @error('priority')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="assigned_to" class="block text-sm font-medium text-slate-300 mb-2">Assign to</label>
                <select
                    id="assigned_to"
                    name="assigned_to"
                    class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                    <option value="">Unassigned</option>
                    @php
                        $staffUsers = \App\Models\User::whereHas('siteRole', function($q) {
                            $q->whereIn('name', ['admin', 'mod']);
                        })->get();
                    @endphp
                    @foreach($staffUsers as $staffUser)
                        <option value="{{ $staffUser->id }}" {{ $ticket->assigned_to == $staffUser->id ? 'selected' : '' }}>
                            {{ $staffUser->name }} ({{ ucfirst($staffUser->siteRole->name) }})
                        </option>
                    @endforeach
                </select>
                @error('assigned_to')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            @if($ticket->resolved_at)
                <div class="p-4 bg-green-500/10 border border-green-500/30 rounded-lg">
                    <p class="text-sm text-green-300">
                        This ticket was resolved on {{ $ticket->resolved_at->format('M j, Y g:i A') }}
                        @if($ticket->resolvedBy)
                            by {{ $ticket->resolvedBy->name }}
                        @endif
                    </p>
                </div>
            @endif

            <div class="flex items-center space-x-4 pt-4">
                <button
                    type="submit"
                    class="px-6 py-3 bg-pink-500 hover:bg-pink-600 text-white font-medium rounded-lg transition-colors">
                    Update Ticket
                </button>
                <a
                    href="{{ route('dashboard.tickets.show', $ticket) }}"
                    class="px-6 py-3 bg-slate-800 hover:bg-slate-700 text-white font-medium rounded-lg transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
