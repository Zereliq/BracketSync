@extends('layouts.dashboard')

@section('title', 'Support Tickets - BracketSync')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">Support Tickets</h1>
            <p class="text-slate-400">
                @if(auth()->user()->isAdmin() || auth()->user()->isMod())
                    Manage all support tickets
                @else
                    View and manage your support tickets
                @endif
            </p>
        </div>
        <a href="{{ route('dashboard.tickets.create') }}" class="px-4 py-2 bg-pink-500 hover:bg-pink-600 text-white text-sm font-medium rounded-lg transition-colors">
            Create Ticket
        </a>
    </div>

    @if(auth()->user()->isAdmin() || auth()->user()->isMod())
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-yellow-500/10 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-white">{{ $tickets->where('status', 'open')->count() }}</h3>
                    <p class="text-slate-400 text-sm">Open</p>
                </div>
            </div>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-blue-500/10 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-white">{{ $tickets->where('status', 'in_progress')->count() }}</h3>
                    <p class="text-slate-400 text-sm">In Progress</p>
                </div>
            </div>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-green-500/10 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-white">{{ $tickets->where('status', 'resolved')->count() }}</h3>
                    <p class="text-slate-400 text-sm">Resolved</p>
                </div>
            </div>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-slate-500/10 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-white">{{ $tickets->where('status', 'closed')->count() }}</h3>
                    <p class="text-slate-400 text-sm">Closed</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-800">
            <h2 class="text-xl font-bold text-white">All Tickets</h2>
        </div>

        <div class="p-6">
            @if($tickets->count() > 0)
                <div class="space-y-3">
                    @foreach($tickets as $ticket)
                        <a href="{{ route('dashboard.tickets.show', $ticket) }}" class="block group">
                            <div class="p-5 bg-slate-800/30 hover:bg-slate-800/50 rounded-lg transition-all border border-slate-700/50 hover:border-pink-500/50">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-2">
                                            <h3 class="text-lg text-white font-semibold group-hover:text-pink-400 transition-colors">
                                                {{ $ticket->subject }}
                                            </h3>
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($ticket->status === 'open') bg-yellow-500/20 text-yellow-300 border border-yellow-500/30
                                                @elseif($ticket->status === 'in_progress') bg-blue-500/20 text-blue-300 border border-blue-500/30
                                                @elseif($ticket->status === 'resolved') bg-green-500/20 text-green-300 border border-green-500/30
                                                @else bg-slate-500/20 text-slate-300 border border-slate-500/30
                                                @endif">
                                                {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                            </span>
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($ticket->priority === 'high') bg-red-500/20 text-red-300 border border-red-500/30
                                                @elseif($ticket->priority === 'medium') bg-orange-500/20 text-orange-300 border border-orange-500/30
                                                @else bg-slate-500/20 text-slate-300 border border-slate-500/30
                                                @endif">
                                                {{ ucfirst($ticket->priority) }}
                                            </span>
                                        </div>

                                        <p class="text-slate-400 text-sm mb-3 line-clamp-2">{{ $ticket->description }}</p>

                                        <div class="flex items-center space-x-4 text-xs text-slate-500">
                                            <div class="flex items-center space-x-1.5">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                                <span>{{ $ticket->user->name }}</span>
                                            </div>
                                            <div class="flex items-center space-x-1.5">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span>{{ $ticket->created_at->diffForHumans() }}</span>
                                            </div>
                                            @if($ticket->assignedTo)
                                                <div class="flex items-center space-x-1.5">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <span>Assigned to {{ $ticket->assignedTo->name }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <svg class="w-5 h-5 text-slate-500 group-hover:text-pink-400 transition-colors ml-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $tickets->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-white mb-2">No tickets yet</h3>
                    <p class="text-slate-400 mb-6">Create a support ticket if you need help.</p>
                    <a href="{{ route('dashboard.tickets.create') }}" class="inline-flex items-center px-6 py-3 bg-pink-500 hover:bg-pink-600 text-white font-medium rounded-lg transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Create Your First Ticket
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
