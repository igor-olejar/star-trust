<x-layout>
    <div class="max-w-md mx-auto my-20 p-8 bg-white border border-slate-200 rounded-2xl shadow-sm text-center">
        <div class="mb-6 text-indigo-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
        </div>

        <h2 class="text-2xl font-bold text-slate-900 mb-2">Check your email</h2>
        <p class="text-slate-600 mb-8">
            We've sent a verification link to your email address. Please click it to activate your account.
        </p>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-6 p-3 bg-green-50 text-green-700 text-sm rounded-lg border border-green-100">
                A new verification link has been sent to your email address.
            </div>
        @endif

        <div class="space-y-4">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="text-sm font-semibold text-indigo-600 hover:text-indigo-500 transition">
                    Didn't receive the email? Resend it
                </button>
            </form>

            <hr class="border-slate-100">
        </div>
    </div>
</x-layout>