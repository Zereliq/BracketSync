<section class="py-20 bg-gradient-to-br from-pink-500/10 via-slate-900/50 to-fuchsia-500/10">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Ready to compete?</h2>
        <p class="text-lg text-slate-400 mb-8">
            Join thousands of players in the ultimate osu! tournament experience
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="#tournaments" class="inline-flex items-center justify-center px-8 py-4 bg-pink-500 hover:bg-pink-600 text-white font-medium rounded-lg transition-colors shadow-lg shadow-pink-500/30">
                Browse tournaments
            </a>
            @guest
                <a href="{{ route('auth.osu.redirect') }}" class="inline-flex items-center justify-center px-8 py-4 bg-slate-800 hover:bg-slate-700 text-white font-medium rounded-lg transition-colors border border-slate-700">
                    Sign in with osu!
                </a>
            @endguest
        </div>
    </div>
</section>
