@php
    /** @var \Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Collection $users */
@endphp

@if($users->isEmpty())
    <div class="p-8 text-center">
        <p class="font-semibold text-slate-700">No users found.</p>
        <p class="text-sm text-slate-500 mt-1">When users match this status, they’ll appear here.</p>
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

                            @php
                                $status = $user->status instanceof \App\UserStatus ? $user->status : null;
                                $canActivate = $status && in_array($status, [\App\UserStatus::PENDING, \App\UserStatus::VERIFIED, \App\UserStatus::BLOCKED], true);
                                $canReject = $status && in_array($status, [\App\UserStatus::PENDING, \App\UserStatus::VERIFIED, \App\UserStatus::ACTIVE], true);
                                $canBlock = $status && in_array($status, [\App\UserStatus::VERIFIED, \App\UserStatus::ACTIVE], true);
                            @endphp

                            @if($canActivate)
                                <form action="{{ route('admin.users.review.activate', $user) }}" method="POST">
                                    @csrf
                                    <button
                                        type="submit"
                                        class="inline-flex cursor-pointer items-center rounded-lg bg-emerald-600 px-3 py-2 text-sm font-semibold text-white hover:bg-emerald-700"
                                    >
                                        Activate
                                    </button>
                                </form>
                            @endif

                            @if($canReject)
                                <form action="{{ route('admin.users.review.reject', $user) }}" method="POST">
                                    @csrf
                                    <button
                                        type="submit"
                                        class="inline-flex cursor-pointer items-center rounded-lg bg-rose-600 px-3 py-2 text-sm font-semibold text-white hover:bg-rose-700"
                                    >
                                        Reject
                                    </button>
                                </form>
                            @endif

                            @if($canBlock)
                                <form action="{{ route('admin.users.review.block', $user) }}" method="POST">
                                    @csrf
                                    <button
                                        type="submit"
                                        class="inline-flex cursor-pointer items-center rounded-lg bg-slate-900 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-800"
                                    >
                                        Block
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif

