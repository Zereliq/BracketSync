@extends('layouts.dashboard')

@section('title', 'Ticket #' . $ticket->id . ' - BracketSync')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div>
        <a href="{{ route('dashboard.tickets.index') }}" class="inline-flex items-center text-slate-400 hover:text-white mb-4 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Tickets
        </a>
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">Ticket #{{ $ticket->id }}</h1>
                <p class="text-slate-400">{{ $ticket->subject }}</p>
            </div>
            @can('update', $ticket)
                <a href="{{ route('dashboard.tickets.edit', $ticket) }}" class="px-4 py-2 bg-pink-500 hover:bg-pink-600 text-white text-sm font-medium rounded-lg transition-colors">
                    Manage Ticket
                </a>
            @endcan
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 space-y-6">
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
                <h2 class="text-xl font-bold text-white mb-4">Description</h2>
                <p class="text-slate-300 whitespace-pre-wrap">{{ $ticket->description }}</p>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
                <h2 class="text-lg font-bold text-white mb-4">Details</h2>
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-slate-500 mb-1">Status</p>
                        <span class="px-3 py-1.5 rounded-full text-sm font-medium
                            @if($ticket->status === 'open') bg-yellow-500/20 text-yellow-300 border border-yellow-500/30
                            @elseif($ticket->status === 'in_progress') bg-blue-500/20 text-blue-300 border border-blue-500/30
                            @elseif($ticket->status === 'resolved') bg-green-500/20 text-green-300 border border-green-500/30
                            @else bg-slate-500/20 text-slate-300 border border-slate-500/30
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                        </span>
                    </div>

                    <div>
                        <p class="text-sm text-slate-500 mb-1">Priority</p>
                        <span class="px-3 py-1.5 rounded-full text-sm font-medium
                            @if($ticket->priority === 'high') bg-red-500/20 text-red-300 border border-red-500/30
                            @elseif($ticket->priority === 'medium') bg-orange-500/20 text-orange-300 border border-orange-500/30
                            @else bg-slate-500/20 text-slate-300 border border-slate-500/30
                            @endif">
                            {{ ucfirst($ticket->priority) }}
                        </span>
                    </div>

                    <div>
                        <p class="text-sm text-slate-500 mb-1">Submitted by</p>
                        <div class="flex items-center space-x-2">
                            @if($ticket->user->avatar_url)
                                <img src="{{ $ticket->user->avatar_url }}" alt="{{ $ticket->user->name }}" class="w-6 h-6 rounded-full">
                            @endif
                            <p class="text-white">{{ $ticket->user->name }}</p>
                        </div>
                    </div>

                    <div>
                        <p class="text-sm text-slate-500 mb-1">Created</p>
                        <p class="text-white">{{ $ticket->created_at->format('M j, Y g:i A') }}</p>
                        <p class="text-xs text-slate-500">{{ $ticket->created_at->diffForHumans() }}</p>
                    </div>

                    @if($ticket->assignedTo)
                        <div>
                            <p class="text-sm text-slate-500 mb-1">Assigned to</p>
                            <div class="flex items-center space-x-2">
                                @if($ticket->assignedTo->avatar_url)
                                    <img src="{{ $ticket->assignedTo->avatar_url }}" alt="{{ $ticket->assignedTo->name }}" class="w-6 h-6 rounded-full">
                                @endif
                                <p class="text-white">{{ $ticket->assignedTo->name }}</p>
                            </div>
                        </div>
                    @endif

                    @if($ticket->resolved_at)
                        <div>
                            <p class="text-sm text-slate-500 mb-1">Resolved</p>
                            <p class="text-white">{{ $ticket->resolved_at->format('M j, Y g:i A') }}</p>
                            @if($ticket->resolvedBy)
                                <p class="text-xs text-slate-500">by {{ $ticket->resolvedBy->name }}</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            @can('delete', $ticket)
                <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
                    <h2 class="text-lg font-bold text-white mb-4">Danger Zone</h2>
                    <form action="{{ route('dashboard.tickets.destroy', $ticket) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this ticket? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors">
                            Delete Ticket
                        </button>
                    </form>
                </div>
            @endcan
        </div>
    </div>
</div>
@endsection
