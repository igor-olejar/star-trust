<x-admin-layout>
    <div class="max-w-6xl mx-auto my-12 px-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Admin Dashboard</h1>
                <p class="text-slate-600 mt-1">Signed in as <span class="font-semibold">{{ $admin?->email }}</span>.</p>
            </div>
            <a
                href="{{ route('landing') }}"
                class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
            >
                View public site
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Users pending review</p>
                <p class="text-3xl font-bold mt-2 text-slate-900">TBD</p>
                <p class="text-sm text-slate-500 mt-2">Next: add a controller + query for pending users.</p>
            </div>

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Active users</p>
                <p class="text-3xl font-bold mt-2 text-slate-900">TBD</p>
                <p class="text-sm text-slate-500 mt-2">Next: aggregate counts for quick visibility.</p>
            </div>

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Admin actions</p>
                <div class="mt-4 space-y-2">
                    <a href="#" class="block text-sm font-semibold text-indigo-600 hover:underline">Review users (next)</a>
                    <a href="#" class="block text-sm font-semibold text-indigo-600 hover:underline">Manage weights (next)</a>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>

