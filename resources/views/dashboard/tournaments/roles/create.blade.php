@extends('layouts.dashboard')

@section('title', 'Create Role - ' . $tournament->name)

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <div class="flex items-center space-x-4">
            <a href="{{ route('dashboard.tournaments.roles.index', $tournament) }}" class="text-slate-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-white">Create New Role</h1>
                <p class="text-slate-400">{{ $tournament->name }}</p>
            </div>
        </div>
    </div>

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

    <form method="POST" action="{{ route('dashboard.tournaments.roles.store', $tournament) }}" class="space-y-6">
        @csrf

        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 space-y-6">
            <div>
                <label for="name" class="block text-sm font-medium text-slate-300 mb-2">Role Name *</label>
                <input type="text"
                       name="name"
                       id="name"
                       value="{{ old('name') }}"
                       required
                       placeholder="e.g., Map Selector, Schedule Manager"
                       class="w-full bg-slate-800 border border-slate-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-fuchsia-500 focus:border-transparent">
                <p class="mt-2 text-sm text-slate-400">Give this role a descriptive name that reflects its purpose.</p>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-slate-300 mb-2">Description (Optional)</label>
                <textarea name="description"
                          id="description"
                          rows="3"
                          placeholder="Brief description of this role's responsibilities..."
                          class="w-full bg-slate-800 border border-slate-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-fuchsia-500 focus:border-transparent">{{ old('description') }}</textarea>
            </div>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
            <h3 class="text-lg font-bold text-white mb-4">Permissions</h3>
            <p class="text-sm text-slate-400 mb-6">Set the permission level for each section. Staff with this role will only be able to access sections you grant them permission to.</p>

            <div class="space-y-4">
                @foreach($resources as $resource => $label)
                    <div class="bg-slate-800/50 border border-slate-700 rounded-lg p-4">
                        <h4 class="font-medium text-white mb-3">{{ $label }}</h4>
                        <div class="flex space-x-4">
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="radio"
                                       name="permissions[{{ $resource }}]"
                                       value="none"
                                       {{ old('permissions.'.$resource) === 'none' || !old('permissions.'.$resource) ? 'checked' : '' }}
                                       class="w-4 h-4 text-fuchsia-500 bg-slate-700 border-slate-600 focus:ring-fuchsia-500">
                                <span class="text-slate-400">None</span>
                            </label>
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="radio"
                                       name="permissions[{{ $resource }}]"
                                       value="view"
                                       {{ old('permissions.'.$resource) === 'view' ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-500 bg-slate-700 border-slate-600 focus:ring-blue-500">
                                <span class="text-blue-400">View</span>
                            </label>
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="radio"
                                       name="permissions[{{ $resource }}]"
                                       value="edit"
                                       {{ old('permissions.'.$resource) === 'edit' ? 'checked' : '' }}
                                       class="w-4 h-4 text-green-500 bg-slate-700 border-slate-600 focus:ring-green-500">
                                <span class="text-green-400">Edit</span>
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 bg-slate-800/30 border border-slate-700 rounded-lg p-4">
                <h4 class="text-sm font-medium text-slate-300 mb-2">Permission Levels Explained</h4>
                <div class="space-y-2 text-sm text-slate-400">
                    <p><span class="text-slate-300 font-medium">None:</span> Section is hidden and inaccessible</p>
                    <p><span class="text-blue-400 font-medium">View:</span> Can view content but cannot make changes</p>
                    <p><span class="text-green-400 font-medium">Edit:</span> Full access to view and modify content</p>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between pt-4">
            <a href="{{ route('dashboard.tournaments.roles.index', $tournament) }}"
               class="px-6 py-3 bg-slate-800 hover:bg-slate-700 text-white font-medium rounded-lg transition-colors">
                Cancel
            </a>
            <button type="submit"
                    class="px-8 py-3 bg-fuchsia-500 hover:bg-fuchsia-600 text-white font-medium rounded-lg transition-colors">
                Create Role
            </button>
        </div>
    </form>
</div>
@endsection
