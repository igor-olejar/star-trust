<x-layout>
    <div class="max-w-4xl mx-auto my-12 px-4">
        <h1 class="text-3xl font-bold text-slate-900">Dashboard</h1>

        <div
            class="mt-6 flex flex-wrap gap-4 items-center justify-between bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
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

        <div
            class="mt-6 p-4 rounded-lg border {{ $user->status === \App\UserStatus::ACTIVE ? 'bg-emerald-50 border-emerald-200' : 'bg-slate-50 border-slate-200' }}">
            <h4 class="font-bold flex items-center gap-2">
                @if($user->status === \App\UserStatus::ACTIVE)
                    <span class="text-emerald-600">●</span> You have full voting rights.<br />
                        <a href="{{ route('search') }}" class="...">
                            Launch Search
                        </a>
                @else
                    <span class="text-amber-500">●</span> Access Restricted
                    <p class="text-sm text-slate-600 mt-1">
                        Note: Only {{ strtoupper(\App\UserStatus::ACTIVE->label()) }} users can vote. To reach
                        <strong>{{ strtoupper(\App\UserStatus::ACTIVE->label()) }}</strong> status, you must wait for an
                        administrator to review and approve your credentials.
                    </p>
                @endif
            </h4>
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
                <h3 class="font-bold text-slate-800 mb-4">Profile</h3>
                <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label class="text-xs font-bold text-slate-500">City</label>
                        <input type="text" name="city" value="{{ $user->city }}" placeholder="Enter your city"
                            class="w-full mt-1 rounded-md border-slate-300">
                    </div>
                    <div>
                        <label for="country_code" class="text-xs font-bold text-slate-500">Country</label>
                        <select name="country_code" id="country_code"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select a country...</option>
                            <option value="GB" {{ auth()->user()->country_code == 'GB' ? 'selected' : '' }}>
                                United Kingdom
                            </option>
                        </select>
                        @error('country_code')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500">Instagram Handle</label>
                        <input type="text" name="instagram" value="{{ $user->socials['instagram'] ?? null }}"
                            placeholder="@username" class="w-full mt-1 rounded-md border-slate-300">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500">Facebook URL</label>
                        <input type="text" name="facebook" value="{{ $user->socials['facebook'] ?? null }}"
                            placeholder="https://facebook.com/..." class="w-full mt-1 rounded-md border-slate-300">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500">Website / Portfolio</label>
                        <input type="url" name="website" value="{{ $user->website }}" placeholder="https://..."
                            class="w-full mt-1 rounded-md border-slate-300">
                    </div>
                    <button
                        class="w-full bg-slate-800 text-black py-2 rounded-lg text-sm font-semibold hover:bg-slate-700 transition">
                        Update Profile
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-layout>