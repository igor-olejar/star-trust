<x-admin-layout>
    <div class="max-w-6xl mx-auto my-12 px-6">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Pending users</h1>
                <p class="text-slate-600 mt-1">Accounts that haven’t completed the onboarding flow yet.</p>
            </div>
            <a
                href="{{ route('admin.dashboard') }}"
                class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
            >
                Back to dashboard
            </a>
        </div>

        <div class="mt-6 flex flex-wrap gap-2">
            <a href="{{ route('admin.users.list.pending') }}" class="rounded-lg px-3 py-2 text-sm font-semibold bg-slate-900 text-white">Pending</a>
            <a href="{{ route('admin.users.list.active') }}" class="rounded-lg px-3 py-2 text-sm font-semibold bg-white border border-slate-200 text-slate-700 hover:bg-slate-50">Active</a>
            <a href="{{ route('admin.users.list.blocked') }}" class="rounded-lg px-3 py-2 text-sm font-semibold bg-white border border-slate-200 text-slate-700 hover:bg-slate-50">Blocked</a>
            <a href="{{ route('admin.users.list.rejected') }}" class="rounded-lg px-3 py-2 text-sm font-semibold bg-white border border-slate-200 text-slate-700 hover:bg-slate-50">Rejected</a>
            <a href="{{ route('admin.users.review') }}" class="rounded-lg px-3 py-2 text-sm font-semibold bg-indigo-600 text-white hover:bg-indigo-700">Review queue</a>
        </div>

        <div class="mt-8 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                <p class="font-bold text-slate-900">Users (Pending)</p>
                <p class="text-sm text-slate-500">{{ $users->count() }} total</p>
            </div>

            @include('admin.users.list._table', ['users' => $users])
        </div>
    </div>
</x-admin-layout>

