<x-layout>
    <div class="max-w-md mx-auto my-16 bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
        <h2 class="text-2xl font-bold mb-6">Create your account</h2>

        <form action="{{ route('register') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700">Full Name</label>
                <input type="text" name="name" class="w-full mt-1 rounded-lg border-slate-300 shadow-sm" required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700">Email Address</label>
                <input type="email" name="email" class="w-full mt-1 rounded-lg border-slate-300 shadow-sm" required>
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
                <input type="password" name="password" class="w-full mt-1 rounded-lg border-slate-300 shadow-sm" required>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700">Confirm Password</label>
                <input type="password" name="password_confirmation" class="w-full mt-1 rounded-lg border-slate-300 shadow-sm" required>
            </div>

            <button type="submit" class="w-full bg-indigo-600 text-white py-3 rounded-lg font-semibold hover:bg-indigo-700 transition">
                Register as {{ ucfirst($selectedType ?? 'Member') }}
            </button>
        </form>
    </div>
</x-layout>