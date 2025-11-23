@extends('layouts.dashboard')

@section('title', 'Manage Roles - ' . $tournament->name)

@section('content')
<div class="max-w-full mx-auto" x-data="roleManager()">
    <div class="mb-8">
        <div class="flex items-center space-x-4">
            <a href="{{ route('dashboard.tournaments.staff', $tournament) }}" class="text-slate-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-white">Role Permissions</h1>
                <p class="text-slate-400">{{ $tournament->name }}</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-500/20 border border-green-500/30 text-green-400 px-6 py-4 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

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

    <form method="POST" action="{{ route('dashboard.tournaments.roles.update-all', $tournament) }}">
        @csrf
        @method('PUT')

        <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-800">
                            <th class="text-left p-4 bg-slate-800/50 sticky left-0 z-10 min-w-[200px]">
                                <span class="text-sm font-medium text-slate-300">Role</span>
                            </th>
                            <th class="text-center p-4 bg-slate-800/50 min-w-[120px]">
                                <span class="text-xs font-medium text-slate-300">Tournament</span>
                            </th>
                            <th class="text-center p-4 bg-slate-800/50 min-w-[120px]">
                                <span class="text-xs font-medium text-slate-300">Staff</span>
                            </th>
                            <th class="text-center p-4 bg-slate-800/50 min-w-[120px]">
                                <span class="text-xs font-medium text-slate-300">Players</span>
                            </th>
                            <th class="text-center p-4 bg-slate-800/50 min-w-[120px]">
                                <span class="text-xs font-medium text-slate-300">Teams</span>
                            </th>
                            <th class="text-center p-4 bg-slate-800/50 min-w-[120px]">
                                <span class="text-xs font-medium text-slate-300">Qualifiers</span>
                            </th>
                            <th class="text-center p-4 bg-slate-800/50 min-w-[120px]">
                                <span class="text-xs font-medium text-slate-300">Matches</span>
                            </th>
                            <th class="text-center p-4 bg-slate-800/50 min-w-[120px]">
                                <span class="text-xs font-medium text-slate-300">Bracket</span>
                            </th>
                            <th class="text-center p-4 bg-slate-800/50 min-w-[120px]">
                                <span class="text-xs font-medium text-slate-300">Mappools</span>
                            </th>
                            <th class="text-center p-4 bg-slate-800/50 min-w-[80px]">
                                <span class="text-xs font-medium text-slate-300">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $role)
                            @php
                                $permissions = $role->permissions->keyBy('resource');
                                $resources = ['tournament', 'staff', 'players', 'teams', 'qualifiers', 'matches', 'bracket', 'mappools'];
                            @endphp
                            <tr class="border-b border-slate-800 hover:bg-slate-800/30 transition-colors">
                                <td class="p-4 bg-slate-900 sticky left-0 z-10">
                                    <div>
                                        <div class="flex items-center space-x-2">
                                            <span class="text-white font-medium">{{ $role->name }}</span>
                                            @if($role->is_protected)
                                                <span class="px-2 py-0.5 bg-amber-500/20 border border-amber-500/30 text-amber-400 text-xs rounded">
                                                    Protected
                                                </span>
                                            @endif
                                        </div>
                                        @if($role->description)
                                            <p class="text-xs text-slate-400 mt-1">{{ $role->description }}</p>
                                        @endif
                                    </div>
                                </td>
                                @foreach($resources as $resource)
                                    <td class="p-2 text-center">
                                        @php
                                            $currentPermission = $permissions->get($resource)?->permission ?? 'none';
                                        @endphp
                                        <select name="roles[{{ $role->id }}][{{ $resource }}]"
                                                class="w-full bg-slate-800 border border-slate-700 text-white rounded px-2 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-fuchsia-500 focus:border-transparent"
                                                @change="markAsChanged()">
                                            <option value="none" {{ $currentPermission === 'none' ? 'selected' : '' }}>None</option>
                                            <option value="view" {{ $currentPermission === 'view' ? 'selected' : '' }}>View</option>
                                            <option value="edit" {{ $currentPermission === 'edit' ? 'selected' : '' }}>Edit</option>
                                        </select>
                                    </td>
                                @endforeach
                                <td class="p-2 text-center">
                                    @unless($role->is_protected)
                                        @php
                                            $staffCount = $role->links()->count();
                                        @endphp
                                        @if($staffCount > 0)
                                            <button type="button"
                                                    disabled
                                                    title="Cannot delete: {{ $staffCount }} staff member(s) assigned"
                                                    class="p-1.5 bg-slate-700/50 text-slate-500 rounded cursor-not-allowed">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        @else
                                            <form method="POST" action="{{ route('dashboard.tournaments.roles.destroy', [$tournament, $role]) }}" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        onclick="return confirm('Are you sure you want to delete the {{ $role->name }} role?')"
                                                        title="Delete role"
                                                        class="p-1.5 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    @endunless
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-between">
            <div class="bg-slate-900/50 border border-slate-800 rounded-lg px-4 py-3">
                <div class="flex items-center space-x-4 text-xs text-slate-400">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-slate-600 rounded"></div>
                        <span>None (Hidden)</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-blue-500 rounded"></div>
                        <span>View (Read-only)</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-green-500 rounded"></div>
                        <span>Edit (Full access)</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center space-x-4">
                <button type="button"
                        @click="resetForm()"
                        x-show="hasChanges"
                        class="px-6 py-3 bg-slate-800 hover:bg-slate-700 text-white font-medium rounded-lg transition-colors">
                    Reset Changes
                </button>
                <button type="submit"
                        class="px-8 py-3 bg-fuchsia-500 hover:bg-fuchsia-600 text-white font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        :class="{ 'ring-2 ring-fuchsia-400': hasChanges }">
                    <span x-show="!hasChanges">Save Permissions</span>
                    <span x-show="hasChanges">Save Changes</span>
                </button>
            </div>
        </div>
    </form>

    <div class="mt-6 bg-slate-900 border border-slate-800 rounded-xl p-6">
        <h3 class="text-lg font-bold text-white mb-4">Add Custom Role</h3>
        <p class="text-sm text-slate-400 mb-4">Create additional roles specific to this tournament. Custom roles can be deleted if no longer needed.</p>

        <form method="POST" action="{{ route('dashboard.tournaments.roles.create-custom', $tournament) }}" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="custom_role_name" class="block text-sm font-medium text-slate-300 mb-2">Role Name *</label>
                    <input type="text"
                           name="name"
                           id="custom_role_name"
                           required
                           placeholder="e.g., Head Admin, Schedule Manager"
                           class="w-full bg-slate-800 border border-slate-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-fuchsia-500 focus:border-transparent">
                </div>

                <div>
                    <label for="custom_role_description" class="block text-sm font-medium text-slate-300 mb-2">Description (Optional)</label>
                    <input type="text"
                           name="description"
                           id="custom_role_description"
                           placeholder="Brief description of this role"
                           class="w-full bg-slate-800 border border-slate-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-fuchsia-500 focus:border-transparent">
                </div>
            </div>

            <div class="flex items-center justify-between pt-2">
                <p class="text-xs text-slate-500">
                    New roles start with no permissions. Set permissions after creating the role.
                </p>
                <button type="submit"
                        class="px-6 py-2.5 bg-fuchsia-500 hover:bg-fuchsia-600 text-white font-medium rounded-lg transition-colors">
                    Add Role
                </button>
            </div>
        </form>
    </div>

    <div class="mt-6 bg-slate-900/50 border border-slate-800 rounded-xl p-6">
        <h3 class="text-lg font-bold text-white mb-4">About Roles & Permissions</h3>
        <div class="space-y-3 text-sm text-slate-400">
            <p><span class="text-green-400 font-medium">Edit:</span> Full access to view and modify the section</p>
            <p><span class="text-blue-400 font-medium">View:</span> Read-only access to the section</p>
            <p><span class="text-slate-400 font-medium">None:</span> Section is hidden and inaccessible</p>
            <p class="mt-4 pt-4 border-t border-slate-700 text-xs text-slate-500">
                <strong>Protected Roles:</strong> Standard roles that come with every tournament. They can have permissions modified but cannot be deleted.<br>
                <strong>Custom Roles:</strong> Tournament-specific roles you create. These can be deleted if no staff members are assigned to them.<br>
                <strong>Creator Permissions:</strong> The tournament creator always has full permissions regardless of role settings.
            </p>
        </div>
    </div>
</div>

<script>
function roleManager() {
    return {
        hasChanges: false,
        originalFormData: null,

        init() {
            this.$nextTick(() => {
                this.captureOriginalState();
            });
        },

        captureOriginalState() {
            const form = this.$root.querySelector('form');
            this.originalFormData = new FormData(form);
        },

        markAsChanged() {
            this.hasChanges = true;
        },

        resetForm() {
            if (confirm('Are you sure you want to reset all changes?')) {
                window.location.reload();
            }
        }
    }
}
</script>
@endsection
