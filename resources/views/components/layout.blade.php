<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Star Trust</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="bg-slate-50 antialiased">

    <nav class="bg-white border-b border-slate-200 py-4">
        <div class="max-w-6xl mx-auto px-6 flex justify-between items-center">
            <h1 class="text-xl font-bold">Star<span class="text-indigo-600">Trust</span></h1>
            @auth
                <a href="/logout" class="text-sm font-semibold text-slate-600">Log out</a>
            @endauth

            @guest
                <a href="/login" class="text-sm font-semibold text-slate-600">Log in</a>
            @endguest
        </div>
    </nav>

    <main>
        {{ $slot }}
    </main>

    <footer class="text-center py-10 text-slate-400 text-sm">
        &copy; 2026 Star Trust. All rights reserved.
    </footer>

</body>

</html>