<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Star Trust Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-50 antialiased">
    <nav class="bg-white border-b border-slate-200 py-4">
        <div class="max-w-6xl mx-auto px-6 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <h1 class="text-xl font-bold">
                    Star<span class="text-indigo-600">Trust</span>
                    <span class="ml-2 text-xs font-semibold uppercase tracking-wider text-slate-400">Admin</span>
                </h1>
            </div>

            <div class="flex items-center gap-4">
                @auth('admin')
                    <span class="text-sm text-slate-600">{{ auth('admin')->user()->email }}</span>
                    <form action="{{ route('admin.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-sm font-semibold text-slate-600 hover:text-slate-900">
                            Log out
                        </button>
                    </form>
                @endauth

                @guest('admin')
                    <a href="{{ route('admin.login') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">Log in</a>
                @endguest
            </div>
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

