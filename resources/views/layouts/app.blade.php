<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta_description', 'Organize and play osu! tournaments with ease. Browse active tournaments, view live matches, and manage your competitive gaming experience.')">
    <title>@yield('title', 'BracketSync Tournaments - osu! Tournament Platform')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('head')
</head>
<body class="bg-slate-950 text-slate-100 antialiased">
    @include('components.navigation')

    <div id="toast-container" class="fixed top-20 right-4 z-50 space-y-3 pointer-events-none"></div>

    <main>
        @yield('content')
    </main>

    @include('components.footer')

    @include('components.scripts')
    @stack('scripts')
</body>
</html>
