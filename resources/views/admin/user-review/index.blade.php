<x-admin-layout>
    <div class="max-w-6xl mx-auto my-12 px-6">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">User review</h1>
                <p class="text-slate-600 mt-1">Pending accounts waiting for approval.</p>
            </div>
            <a
                href="{{ route('admin.dashboard') }}"
                class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
            >
                Back to dashboard
            </a>
        </div>

        @if (session('success'))
            <div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800">
                <p class="font-semibold">{{ session('success') }}</p>
            </div>
        @endif

        <div class="mt-8 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                <p class="font-bold text-slate-900">Verified users awaiting approval</p>
                <p class="text-sm text-slate-500">{{ $users->count() }} total</p>
            </div>

            @if($users->isEmpty())
                <div class="p-8 text-center">
                    <p class="font-semibold text-slate-700">No verified users awaiting approval.</p>
                    <p class="text-sm text-slate-500 mt-1">Users appear here after verifying their email.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">User</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-bold uppercase tracking-wider text-slate-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach($users as $user)
                                <tr class="hover:bg-slate-50/60">
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-slate-900">{{ $user->name }}</div>
                                        <div class="text-sm text-slate-500">{{ $user->email }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700">
                                            {{ $user->user_type_id?->label() ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $statusLabel = $user->status instanceof \App\UserStatus ? $user->status->label() : (string) $user->status;
                                            $statusClasses = $user->status instanceof \App\UserStatus && method_exists($user->status, 'colorClasses')
                                                ? $user->status->colorClasses()
                                                : 'bg-slate-100 text-slate-700';
                                        @endphp
                                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase {{ $statusClasses }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-end gap-2">
                                            <a
                                                href="{{ route('admin.users.review.show', $user) }}"
                                                class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                                            >
                                                View
                                            </a>

                                            <form action="{{ route('admin.users.review.activate', $user) }}" method="POST">
                                                @csrf
                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center rounded-lg bg-emerald-600 px-3 py-2 text-sm font-semibold text-white hover:bg-emerald-700"
                                                >
                                                    Activate
                                                </button>
                                            </form>

                                            <form action="{{ route('admin.users.review.reject', $user) }}" method="POST">
                                                @csrf
                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center rounded-lg bg-rose-600 px-3 py-2 text-sm font-semibold text-white hover:bg-rose-700"
                                                >
                                                    Reject
                                                </button>
                                            </form>

                                            <form action="{{ route('admin.users.review.block', $user) }}" method="POST">
                                                @csrf
                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center rounded-lg bg-slate-900 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-800"
                                                >
                                                    Block
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>

