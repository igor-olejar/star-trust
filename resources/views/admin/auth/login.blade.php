<x-admin-layout>
    <div class="max-w-md mx-auto my-16 bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
        <div class="mb-6">
            <h2 class="text-2xl font-bold">Admin sign in</h2>
            <p class="text-sm text-slate-500 mt-1">Use your administrator credentials to continue.</p>
        </div>

        <form action="{{ route('admin.login.submit') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700">Email Address</label>
                <input
                    type="email"
                    name="email"
                    class="w-full mt-1 rounded-lg border-slate-300 shadow-sm"
                    required
                    value="{{ old('email') }}"
                    autocomplete="email"
                >
                <x-input-error :messages="$errors->get('email')" />
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700">Password</label>
                <input
                    type="password"
                    name="password"
                    class="w-full mt-1 rounded-lg border-slate-300 shadow-sm"
                    required
                    autocomplete="current-password"
                >
                <x-input-error :messages="$errors->get('password')" />
            </div>

            <div class="flex items-center justify-between mb-6">
                <label class="inline-flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" name="remember" class="rounded border-slate-300">
                    Remember me
                </label>
                <a href="{{ route('landing') }}" class="text-sm font-semibold text-indigo-600 hover:underline">Back to site</a>
            </div>

            <button
                type="submit"
                class="w-full bg-indigo-600 text-white py-3 rounded-lg font-semibold hover:bg-indigo-700 transition"
            >
                Sign in
            </button>
        </form>
    </div>
</x-admin-layout>

