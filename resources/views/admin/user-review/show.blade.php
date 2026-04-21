<x-admin-layout>
    <div class="max-w-4xl mx-auto my-12 px-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Review user</h1>
                <p class="text-slate-600 mt-1">{{ $user->name }} <span class="text-slate-400">({{ $user->email }})</span></p>
            </div>
            <a
                href="{{ route('admin.users.review') }}"
                class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
            >
                Back to list
            </a>
        </div>

        @php
            $statusLabel = $user->status instanceof \App\UserStatus ? $user->status->label() : (string) $user->status;
            $statusClasses = $user->status instanceof \App\UserStatus && method_exists($user->status, 'colorClasses')
                ? $user->status->colorClasses()
                : 'bg-slate-100 text-slate-700';
        @endphp

        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200">
                    <p class="font-bold text-slate-900">Account details</p>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between gap-4">
                        <p class="text-sm font-semibold text-slate-600">User type</p>
                        <p class="text-sm font-bold text-slate-900">{{ $user->user_type_id?->label() ?? '—' }}</p>
                    </div>
                    <div class="flex items-center justify-between gap-4">
                        <p class="text-sm font-semibold text-slate-600">Status</p>
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase {{ $statusClasses }}">
                            {{ $statusLabel }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between gap-4">
                        <p class="text-sm font-semibold text-slate-600">Email verified</p>
                        <p class="text-sm font-bold text-slate-900">
                            {{ $user->email_verified_at ? $user->email_verified_at->format('Y-m-d H:i') : 'No' }}
                        </p>
                    </div>
                    <div class="flex items-center justify-between gap-4">
                        <p class="text-sm font-semibold text-slate-600">Created</p>
                        <p class="text-sm font-bold text-slate-900">{{ $user->created_at?->format('Y-m-d H:i') ?? '—' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200">
                    <p class="font-bold text-slate-900">Actions</p>
                </div>
                <div class="p-6 space-y-3">
                    <form action="{{ route('admin.users.review.approve', $user) }}" method="POST">
                        @csrf
                        <button
                            type="submit"
                            class="w-full inline-flex items-center justify-center rounded-lg bg-emerald-600 px-4 py-3 text-sm font-semibold text-white hover:bg-emerald-700"
                        >
                            Approve (set Active)
                        </button>
                    </form>

                    <form action="{{ route('admin.users.review.reject', $user) }}" method="POST">
                        @csrf
                        <button
                            type="submit"
                            class="w-full inline-flex items-center justify-center rounded-lg bg-rose-600 px-4 py-3 text-sm font-semibold text-white hover:bg-rose-700"
                        >
                            Reject
                        </button>
                    </form>

                    <div class="pt-3 border-t border-slate-200">
                        <p class="text-xs text-slate-500">
                            Next: add a required “rejection reason” and an audit trail (admin id + timestamp) when you change a user’s status.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>

