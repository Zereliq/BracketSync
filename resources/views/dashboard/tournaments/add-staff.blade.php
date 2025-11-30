@extends('layouts.dashboard')

@section('title', 'Add Staff Member - ' . $tournament->name)

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-8">
        <div class="flex items-center space-x-4 mb-4">
            <a href="{{ route('dashboard.tournaments.staff', $tournament) }}" class="text-slate-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-white">Add Staff Member</h1>
                <p class="text-slate-400">{{ $tournament->name }}</p>
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

    <form method="POST" action="{{ route('dashboard.tournaments.staff.store', $tournament) }}" class="bg-slate-900 border border-slate-800 rounded-xl p-6 space-y-6"
          x-data="{
              query: '{{ old('osu_username') }}',
              suggestions: [],
              showSuggestions: false,
              loading: false,
              debounceTimer: null,

              async searchUsers() {
                  if (this.query.length < 2) {
                      this.suggestions = [];
                      this.showSuggestions = false;
                      return;
                  }

                  clearTimeout(this.debounceTimer);
                  this.debounceTimer = setTimeout(async () => {
                      this.loading = true;
                      try {
                          const response = await fetch(`{{ route('dashboard.users.search') }}?query=${encodeURIComponent(this.query)}`);
                          this.suggestions = await response.json();
                          this.showSuggestions = this.suggestions.length > 0;
                      } catch (error) {
                          console.error('Error fetching suggestions:', error);
                          this.suggestions = [];
                      } finally {
                          this.loading = false;
                      }
                  }, 300);
              },

              selectUser(username) {
                  this.query = username;
                  this.showSuggestions = false;
                  this.suggestions = [];
              }
          }"
          @click.away="showSuggestions = false">
        @csrf

        <div class="relative">
            <label for="osu_username" class="block text-sm font-medium text-slate-300 mb-2">osu! Username *</label>
            <input type="text"
                   name="osu_username"
                   id="osu_username"
                   x-model="query"
                   @input="searchUsers()"
                   @focus="query.length >= 2 && suggestions.length > 0 ? showSuggestions = true : null"
                   required
                   placeholder="Start typing to search..."
                   autocomplete="off"
                   class="w-full bg-slate-800 border border-slate-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-fuchsia-500 focus:border-transparent">

            <!-- Suggestions Dropdown -->
            <div x-show="showSuggestions"
                 x-cloak
                 class="absolute z-50 w-full mt-1 bg-slate-800 border border-slate-700 rounded-lg shadow-xl max-h-64 overflow-y-auto">
                <template x-for="user in suggestions" :key="user.id">
                    <button type="button"
                            @click="selectUser(user.name)"
                            class="w-full flex items-center space-x-3 px-4 py-3 hover:bg-slate-700 transition-colors text-left">
                        <template x-if="user.avatar_url">
                            <img :src="user.avatar_url"
                                 :alt="user.name"
                                 class="w-10 h-10 rounded-full border-2 border-slate-600">
                        </template>
                        <template x-if="!user.avatar_url">
                            <div class="w-10 h-10 rounded-full bg-slate-700 border-2 border-slate-600 flex items-center justify-center">
                                <span class="text-slate-300 font-medium" x-text="user.name.charAt(0).toUpperCase()"></span>
                            </div>
                        </template>
                        <span class="text-white font-medium" x-text="user.name"></span>
                    </button>
                </template>

                <div x-show="loading" class="px-4 py-3 text-center">
                    <span class="text-slate-400 text-sm">Searching...</span>
                </div>
            </div>

            <p class="mt-2 text-sm text-slate-400">Note: The user must have logged into BracketSync at least once.</p>
        </div>

        <div>
            <label for="role_id" class="block text-sm font-medium text-slate-300 mb-2">Role *</label>
            <select name="role_id" id="role_id" required
                    class="w-full bg-slate-800 border border-slate-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-fuchsia-500 focus:border-transparent">
                <option value="">Select a role</option>
                @foreach($roles as $role)
                    @if($role->name === 'Host' && $hasHost)
                        <option value="{{ $role->id }}" disabled class="text-slate-500">
                            {{ $role->name }} (Already assigned)
                        </option>
                    @else
                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endif
                @endforeach
            </select>
            @if($hasHost)
                <p class="mt-2 text-sm text-yellow-400">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Only one Host can be assigned per tournament.
                </p>
            @endif
        </div>

        <div class="flex items-center justify-between pt-4 border-t border-slate-800">
            <a href="{{ route('dashboard.tournaments.staff', $tournament) }}" class="px-6 py-3 bg-slate-800 hover:bg-slate-700 text-white font-medium rounded-lg transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-8 py-3 bg-fuchsia-500 hover:bg-fuchsia-600 text-white font-medium rounded-lg transition-colors">
                Add Staff Member
            </button>
        </div>
    </form>

    <div class="mt-6 bg-slate-900/50 border border-slate-800 rounded-xl p-6">
        @if($roles->isEmpty())
            <div class="text-center py-6">
                <p class="text-slate-400 mb-4">No roles have been created for this tournament yet.</p>
                @if($tournament->isHost())
                    <a href="{{ route('dashboard.tournaments.roles.index', $tournament) }}"
                       class="inline-block px-6 py-2.5 bg-fuchsia-500 hover:bg-fuchsia-600 text-white font-medium rounded-lg transition-colors">
                        Create Roles
                    </a>
                @endif
            </div>
        @else
            <h3 class="text-lg font-bold text-white mb-4">Available Roles</h3>
            <div class="space-y-4">
                @foreach($roles as $role)
                    <div class="flex items-start space-x-3 p-4 bg-slate-800/30 rounded-lg">
                        <div class="w-2 h-2 bg-fuchsia-500 rounded-full mt-2"></div>
                        <div class="flex-1">
                            <p class="text-white font-medium">{{ $role->name }}</p>
                            @if($role->description)
                                <p class="text-sm text-slate-400 mt-1">{{ $role->description }}</p>
                            @endif
                            @if($role->permissions->isNotEmpty())
                                <div class="flex flex-wrap gap-2 mt-2">
                                    @foreach($role->permissions->where('permission', '!=', 'none') as $permission)
                                        @php
                                            $colorClass = $permission->permission === 'edit'
                                                ? 'bg-green-500/20 text-green-400'
                                                : 'bg-blue-500/20 text-blue-400';
                                        @endphp
                                        <span class="px-2 py-0.5 rounded text-xs {{ $colorClass }}">
                                            {{ ucfirst($permission->resource) }}: {{ ucfirst($permission->permission) }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
