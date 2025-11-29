@php
    $isDashboard = $isDashboard ?? request()->routeIs('dashboard.*');
    $canEditStaff = auth()->check() && auth()->user()->can('editStaff', $tournament);
    $staffByRole = $staffByRole ?? collect();
    $routePrefix = $isDashboard ? 'dashboard.tournaments.' : 'tournaments.';
@endphp

<div class="space-y-6">
    @if($canEditStaff)
        <div class="flex justify-end gap-3">
            <a href="{{ route('dashboard.tournaments.roles.index', $tournament) }}" class="px-6 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-medium rounded-lg transition-colors flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span>Manage Roles</span>
            </a>
            <a href="{{ route('dashboard.tournaments.staff.add', $tournament) }}" class="px-6 py-2.5 bg-fuchsia-500 hover:bg-fuchsia-600 text-white font-medium rounded-lg transition-colors">
                Add Staff Member
            </a>
        </div>
    @endif

    @if($staffByRole->isEmpty())
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-slate-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
            <h3 class="text-xl font-bold text-slate-400 mb-2">No Staff Members</h3>
            <p class="text-slate-500">This tournament does not have any staff members assigned yet.</p>
        </div>
    @else
        @foreach($staffByRole as $roleName => $members)
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-white">{{ $roleName }}</h2>
                    <span class="text-sm text-slate-400">{{ $members->count() }} {{ $members->count() === 1 ? 'member' : 'members' }}</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($members as $member)
                        <div class="flex items-center space-x-3 p-4 bg-slate-800/50 rounded-lg border border-slate-700 hover:border-slate-600 transition-colors">
                            @if($member->user->avatar_url)
                                <img src="{{ $member->user->avatar_url }}" alt="{{ $member->user->name }}" class="w-12 h-12 rounded-full border-2 border-slate-700">
                            @else
                                <div class="w-12 h-12 rounded-full bg-slate-700 flex items-center justify-center border-2 border-slate-600">
                                    <span class="text-slate-300 text-lg font-medium">{{ substr($member->user->name, 0, 1) }}</span>
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <p class="text-white font-medium truncate">{{ $member->user->name }}</p>
                                <p class="text-sm text-slate-400">{{ $member->role->name }}</p>
                                @if($member->user->discord_username)
                                    <div class="inline-flex items-center mt-1 px-2 py-0.5 bg-indigo-500/20 text-indigo-400 text-xs font-medium rounded border border-indigo-500/30">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M20.317 4.37a19.791 19.791 0 00-4.885-1.515.074.074 0 00-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 00-5.487 0 12.64 12.64 0 00-.617-1.25.077.077 0 00-.079-.037A19.736 19.736 0 003.677 4.37a.07.07 0 00-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 00.031.057 19.9 19.9 0 005.993 3.03.078.078 0 00.084-.028c.462-.63.874-1.295 1.226-1.994a.076.076 0 00-.041-.106 13.107 13.107 0 01-1.872-.892.077.077 0 01-.008-.128 10.2 10.2 0 00.372-.292.074.074 0 01.077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 01.078.01c.12.098.246.198.373.292a.077.077 0 01-.006.127 12.299 12.299 0 01-1.873.892.077.077 0 00-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 00.084.028 19.839 19.839 0 006.002-3.03.077.077 0 00.032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 00-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.418-2.157 2.418z"/>
                                        </svg>
                                        <span class="truncate">{{ $member->user->discord_username }}</span>
                                    </div>
                                @endif
                            </div>
                            @php
                                $canDelete = false;
                                if ($canEditStaff) {
                                    // Don't allow deletion of Host role
                                    if ($member->role->name === 'Host') {
                                        $canDelete = false;
                                    } else {
                                        $canDelete = true;
                                    }
                                }
                            @endphp
                            @if($canDelete)
                                <form method="POST" action="{{ route('dashboard.tournaments.staff.remove', [$tournament, $member]) }}" onsubmit="return confirm('Are you sure you want to remove this staff member?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-red-400 transition-colors" title="Remove">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    @endif
</div>
