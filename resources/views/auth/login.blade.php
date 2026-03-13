<x-layout>
    <div class="max-w-md mx-auto my-16 bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
        <h2 class="text-2xl font-bold mb-6">Welcome back!</h2>

        <form action="{{ route('login') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700">Email Address</label>
                <input type="email" name="email" class="w-full mt-1 rounded-lg border-slate-300 shadow-sm" required value="{{ old('email') }}">
                <x-input-error :messages="$errors->get('email')" />
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700">Password</label>
                <input type="password" name="password" class="w-full mt-1 rounded-lg border-slate-300 shadow-sm" required>
                <x-input-error :messages="$errors->get('password')" />
            </div>

            <button type="submit" class="w-full bg-indigo-600 text-white py-3 rounded-lg font-semibold hover:bg-indigo-700 transition">
                Log In
            </button>
            <div>
                Forgot your password? <a href="{{ route('password.request') }}" class="text-indigo-600 hover:underline">Reset it here</a>.
            </div>
        </form>
    </div>
</x-layout>