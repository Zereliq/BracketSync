<section class="relative overflow-hidden py-16 md:py-24">
    <div class="absolute inset-0 bg-gradient-to-br from-pink-500/10 via-transparent to-fuchsia-500/10"></div>
    <div class="max-w-6xl mx-auto px-4 relative">
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <div class="space-y-6">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight">
                    Organize and play
                    <span class="bg-gradient-to-r from-pink-500 to-fuchsia-500 bg-clip-text text-transparent">osu! tournaments</span>
                    with ease
                </h1>
                <p class="text-lg text-slate-400 leading-relaxed">
                    Streamline your competitive experience with automated seeding, bracket management,
                    mappool organization, and live scoring. Everything you need to run professional tournaments.
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('tournaments.index') }}" class="inline-flex items-center justify-center px-6 py-3 bg-pink-500 hover:bg-pink-600 text-white font-medium rounded-lg transition-colors shadow-lg shadow-pink-500/30">
                        View active tournaments
                    </a>
                    <a href="#host" class="inline-flex items-center justify-center px-6 py-3 bg-slate-800 hover:bg-slate-700 text-white font-medium rounded-lg transition-colors border border-slate-700">
                        Host a tournament
                    </a>
                </div>
            </div>
            <div class="bg-slate-900 rounded-2xl shadow-2xl border border-slate-800 p-6 space-y-4">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-white">BracketSync Cup 2025</h3>
                        <p class="text-sm text-slate-400 mt-1">Spring Edition</p>
                    </div>
                    <span class="px-3 py-1 bg-green-500/20 text-green-400 text-xs font-medium rounded-full border border-green-500/30">
                        Ongoing
                    </span>
                </div>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div class="space-y-1">
                        <p class="text-slate-500 text-xs">Mode</p>
                        <p class="text-slate-200 font-medium">osu! standard</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-slate-500 text-xs">Format</p>
                        <p class="text-slate-200 font-medium">2v2 Team</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-slate-500 text-xs">Bracket</p>
                        <p class="text-slate-200 font-medium">Double Elimination</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-slate-500 text-xs">Teams</p>
                        <p class="text-slate-200 font-medium">32 registered</p>
                    </div>
                </div>
                <div class="pt-4 border-t border-slate-800">
                    <p class="text-xs text-slate-500 mb-3">Current Round</p>
                    <div class="grid grid-cols-4 gap-2">
                        <div class="h-16 bg-slate-800 rounded-lg border border-slate-700 flex items-center justify-center">
                            <div class="text-center">
                                <p class="text-xs text-slate-500">QF1</p>
                                <p class="text-xs text-green-400 font-medium mt-1">Live</p>
                            </div>
                        </div>
                        <div class="h-16 bg-slate-800 rounded-lg border border-slate-700 flex items-center justify-center">
                            <div class="text-center">
                                <p class="text-xs text-slate-500">QF2</p>
                                <p class="text-xs text-slate-400 mt-1">Pending</p>
                            </div>
                        </div>
                        <div class="h-16 bg-slate-800 rounded-lg border border-slate-700 flex items-center justify-center">
                            <div class="text-center">
                                <p class="text-xs text-slate-500">QF3</p>
                                <p class="text-xs text-slate-400 mt-1">Pending</p>
                            </div>
                        </div>
                        <div class="h-16 bg-slate-800 rounded-lg border border-slate-700 flex items-center justify-center">
                            <div class="text-center">
                                <p class="text-xs text-slate-500">QF4</p>
                                <p class="text-xs text-slate-400 mt-1">Pending</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
