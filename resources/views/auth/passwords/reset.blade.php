<x-layout>
    <div class="max-w-md mx-auto my-16 bg-white p-8 rounded-2xl shadow-sm border border-slate-200"
        x-data="{ password: '', password_confirmation: '' }">

        <h2 class="text-2xl font-bold mb-2">Reset Password</h2>
        <p class="text-slate-500 text-sm mb-6">Please enter your new secure password below.</p>

        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-4 mb-4 rounded">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('password.reset') }}" method="POST">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700">Email Address</label>
                <input type="email" name="email" value="{{ old('email', $request->email) }}"
                    class="w-full mt-1 rounded-lg border-slate-300 bg-slate-50 text-slate-500 shadow-sm">
                <x-input-error :messages="$errors->get('email')" />
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700">New Password</label>
                <input type="password" name="password" x-model.trim="password"
                    class="w-full mt-1 rounded-lg border-slate-300 shadow-sm">
                <x-input-error :messages="$errors->get('password')" />
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700">Confirm New Password</label>
                <input type="password" name="password_confirmation" x-model.trim="password_confirmation"
                    class="w-full mt-1 rounded-lg border-slate-300 shadow-sm">

                <div class="mt-3 h-1.5 w-full bg-slate-100 rounded-full overflow-hidden" x-cloak>
                    <div class="h-full transition-all duration-500"
                        :class="password === password_confirmation && password.length > 0 ? 'w-full bg-emerald-500' : 'w-1/2 bg-amber-400'"
                        x-show="password_confirmation.length > 0">
                    </div>
                </div>

                <p class="text-xs mt-2" x-show="password_confirmation.length > 0" x-cloak>
                    <span x-show="password === password_confirmation" class="text-emerald-600 font-medium">Passwords
                        match!</span>
                    <span x-show="password !== password_confirmation" class="text-amber-600">Passwords do not match
                        yet...</span>
                </p>
            </div>

            <button type="submit"
                class="w-full bg-indigo-600 text-white py-3 rounded-lg font-semibold hover:bg-indigo-700 transition">
                Reset Password
            </button>
        </form>
    </div>
</x-layout>