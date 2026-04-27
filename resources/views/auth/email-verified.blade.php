<x-layout>
    <div class="max-w-md mx-auto my-20 p-8 bg-white border border-slate-200 rounded-2xl shadow-sm text-center">
        <div class="mb-6 text-emerald-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>

        <h2 class="text-2xl font-bold text-slate-900 mb-2">Account Verified!</h2>
        <p class="text-slate-600 mb-8">
            Thank you for verifying your email address. Your account is now active and ready to use.
        </p>

        <div class="space-y-4">
            <a href="{{ route('login') }}" 
               class="block w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-xl transition duration-200 shadow-lg shadow-indigo-100">
                Sign In to Your Account
            </a>

            <p class="text-xs text-slate-400">
                You can now access your full dashboard and features.
            </p>
        </div>
    </div>
</x-layout>