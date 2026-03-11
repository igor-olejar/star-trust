<x-layout>
    <div class="max-w-md mx-auto my-16 bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
        <h2 class="text-2xl font-bold mb-6">Create your account</h2>

        <div x-data="{ password: '', password_confirmation: '' }">
        <form action="{{ route('register') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700">Full Name</label>
                <input type="text" name="name" class="w-full mt-1 rounded-lg border-slate-300 shadow-sm" required value="{{ old('name') }}">
                <x-input-error :messages="$errors->get('name')" />
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700">Email Address</label>
                <input type="email" name="email" class="w-full mt-1 rounded-lg border-slate-300 shadow-sm" required value="{{ old('email') }}">
                <x-input-error :messages="$errors->get('email')" />
            </div>

            <input type="hidden" name="user_type_id" value="{{ 
                match($selectedType) {
                    'artist' => 1,
                    'venue' => 2,
                    'promoter' => 3,
                    default => 1
                }
            }}">

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700">Password</label>
                <input type="password" name="password" x-model.trim="password" class="w-full mt-1 rounded-lg border-slate-300 shadow-sm" required>
                <x-input-error :messages="$errors->get('password')" />
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700">Confirm Password</label>
                <input type="password" name="password_confirmation" x-model.trim="password_confirmation" class="w-full mt-1 rounded-lg border-slate-300 shadow-sm">
                
                <div class="mt-3 h-1.5 w-full bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full transition-all duration-500"
                         :class="password === password_confirmation && password.length > 0 ? 'w-full bg-emerald-500' : 'w-1/2 bg-amber-400'"
                         x-show="password_confirmation.length > 0">
                    </div>
                </div>
                <p class="text-xs mt-2" x-show="password_confirmation.length > 0" x-cloak>
                    <span x-show="password === password_confirmation" class="text-emerald-600 font-medium">Passwords match!</span>
                    <span x-show="password !== password_confirmation" class="text-amber-600">Passwords do not match yet...</span>
                </p>
            </div>

            <button type="submit" class="w-full bg-indigo-600 text-white py-3 rounded-lg font-semibold hover:bg-indigo-700 transition">
                Register as {{ ucfirst($selectedType ?? 'Member') }}
            </button>
        </form>
        </div>
    </div>
</x-layout>