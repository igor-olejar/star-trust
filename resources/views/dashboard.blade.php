<x-layout>
    <div class="max-w-4xl mx-auto my-12 px-4">
        <h1 class="text-3xl font-bold text-slate-900">Dashboard</h1>
        
        <div class="mt-6 flex flex-wrap gap-4 items-center justify-between bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
            <div>
                <p class="text-sm text-slate-500 uppercase tracking-wider font-semibold">Profile Type</p>
                <h3 class="text-lg font-bold text-indigo-600">{{ $user->user_type_id->label() }}</h3>
            </div>
            
            <div class="text-right">
                <p class="text-sm text-slate-500 uppercase tracking-wider font-semibold">Account Status</p>
                
                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase {{ $user->status->colorClasses() }}">
                    {{ $user->status->label() }}
                </span>
            </div>
        </div>

        <div class="mt-6 p-4 rounded-lg border {{ $user->status === 'active' ? 'bg-emerald-50 border-emerald-200' : 'bg-slate-50 border-slate-200' }}">
            <h4 class="font-bold flex items-center gap-2">
                @if($user->status === 'active')
                    <span class="text-emerald-600">●</span> You have full voting rights.
                @else
                    <span class="text-amber-500">●</span> Access Restricted
                @endif
            </h4>
            <p class="text-sm text-slate-600 mt-1">
                Note: Only {{ strtoupper(\App\UserStatus::ACTIVE->label()) }} users can vote. To reach <strong>{{ strtoupper(\App\UserStatus::ACTIVE->label()) }}</strong> status, you must wait for an administrator to review and approve your credentials.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
                <div class="bg-white p-6 rounded-xl border border-slate-200">
                    <h3 class="font-bold text-slate-800 mb-4">Your Weighted Scores</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between border-b pb-2">
                            <span>Influence</span>
                            <span class="font-mono font-bold">8.5</span>
                        </div>
                        <div class="flex justify-between border-b pb-2">
                            <span>Reliability</span>
                            <span class="font-mono font-bold text-slate-400">TBD</span>
                        </div>
                    </div>
                </div>

            <div class="bg-white p-6 rounded-xl border border-slate-200">
                <h3 class="font-bold text-slate-800 mb-4">Social Profiles</h3>
                <form action="{{ route('profile.socials.update') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label class="text-xs font-bold text-slate-500">Instagram Handle</label>
                        <input type="text" name="instagram" value="{{ $user->instagram }}" placeholder="@username" class="w-full mt-1 rounded-md border-slate-300">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500">Website / Portfolio</label>
                        <input type="url" name="website" value="{{ $user->website }}" placeholder="https://..." class="w-full mt-1 rounded-md border-slate-300">
                    </div>
                    <button class="w-full bg-slate-800 text-black py-2 rounded-lg text-sm font-semibold hover:bg-slate-700 transition">
                        Update Socials
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-layout>