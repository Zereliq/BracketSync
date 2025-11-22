<header class="sticky top-0 z-50 bg-slate-900/95 backdrop-blur-sm border-b border-slate-800">
    <nav class="max-w-6xl mx-auto px-4 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-8">
                <a href="{{ route('homepage') }}" class="text-xl font-bold bg-gradient-to-r from-pink-500 to-fuchsia-500 bg-clip-text text-transparent">
                    BracketSync Tournaments
                </a>
                <div class="hidden md:flex items-center space-x-6">
                    <a href="#tournaments" class="text-slate-300 hover:text-pink-500 transition-colors">Tournaments</a>
                    <a href="#matches" class="text-slate-300 hover:text-pink-500 transition-colors">Matches</a>
                    <a href="dashboard" class="text-slate-300 hover:text-pink-500 transition-colors">Dashboard</a>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                @auth
                    <div class="hidden md:flex items-center space-x-3">
                        <div class="flex items-center space-x-3 px-4 py-2 bg-slate-800 rounded-lg border border-slate-700">
                            @if(auth()->user()->avatar_url)
                                <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-8 h-8 rounded-full">
                            @endif
                            <span class="text-white font-medium">{{ auth()->user()->name }}</span>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-medium rounded-lg transition-colors border border-slate-700">
                                Logout
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('auth.osu.redirect') }}" class="hidden md:inline-flex items-center px-5 py-2.5 bg-pink-500 hover:bg-pink-600 text-white font-medium rounded-lg transition-colors shadow-lg shadow-pink-500/30">
                        Sign in with osu!
                    </a>
                @endauth
                <button id="mobile-menu-btn" class="md:hidden p-2 text-slate-300 hover:text-pink-500 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
        <div id="mobile-menu" class="hidden md:hidden pt-4 pb-2 space-y-3">
            <a href="#tournaments" class="block text-slate-300 hover:text-pink-500 transition-colors py-2">Tournaments</a>
            <a href="#matches" class="block text-slate-300 hover:text-pink-500 transition-colors py-2">Matches</a>
            <a href="#dashboard" class="block text-slate-300 hover:text-pink-500 transition-colors py-2">Dashboard</a>
            @auth
                <div class="flex items-center space-x-3 px-4 py-2 bg-slate-800 rounded-lg border border-slate-700">
                    @if(auth()->user()->avatar_url)
                        <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-8 h-8 rounded-full">
                    @endif
                    <span class="text-white font-medium">{{ auth()->user()->name }}</span>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-medium rounded-lg transition-colors border border-slate-700">
                        Logout
                    </button>
                </form>
            @else
                <a href="{{ route('auth.osu.redirect') }}" class="inline-flex items-center px-5 py-2.5 bg-pink-500 hover:bg-pink-600 text-white font-medium rounded-lg transition-colors shadow-lg shadow-pink-500/30">
                    Sign in with osu!
                </a>
            @endauth
        </div>
    </nav>
</header>
