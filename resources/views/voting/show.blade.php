<x-layout>
    <div>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Vote for :name', ['name' => $targetUser->name]) }}
        </h2>
    </div>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-8 text-center">
                    <div
                        class="w-24 h-24 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl font-bold">
                        {{ substr($targetUser->name, 0, 1) }}
                    </div>

                    <h1 class="text-2xl font-bold text-gray-900">{{ $targetUser->name }}</h1>

                    <span
                        class="inline-flex items-center px-3 py-1 mt-2 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                        {{ $targetUser->user_type_id->name }}
                    </span>

                    <div class="mt-6 grid grid-cols-2 gap-4 text-sm text-gray-600">
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <p class="font-semibold text-gray-400 uppercase text-xs">Location</p>
                            <p class="text-gray-900">{{ $targetUser->city }}, {{ $targetUser->country_name }}</p>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <p class="font-semibold text-gray-400 uppercase text-xs">Member Since</p>
                            <p class="text-gray-900">{{ $targetUser->created_at->format('M Y') }}</p>
                        </div>
                    </div>

                    <hr class="my-8 border-gray-100">
                </div>
            </div>
        </div>
    </div>
    <div x-data="{ 
            weightedScore: {{ $targetUser->averageScore() }},
            totalVotes: {{ $targetUser->totalRatingsCount() }},
            hoverVote: 0,
            userVote: 0,
            comment: '',

            async submitRating() {
                if (this.userVote === 0) return alert('Please select a star rating');

                // Logic for POSTing to your Rating model will go here
                console.log('Target ID: {{ $targetUser->id }}', 'Stars:', this.userVote, 'Comment:', this.comment);
            }
        }" class="mt-8 space-y-8">

        <div class="text-center p-6 bg-slate-50 rounded-xl border border-slate-100">
            <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-3">Community Rating</h3>

            <div class="flex items-center justify-center space-x-1">
                <template x-for="i in 5">
                    <svg class="w-10 h-10 transition-colors duration-200"
                        :class="i <= Math.round(weightedScore) ? 'text-yellow-400' : 'text-slate-200'"
                        fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                </template>

                <span class="ml-3 text-3xl font-black text-slate-700" x-text="weightedScore.toFixed(1)"></span>
            </div>

            <p class="text-xs text-slate-400 mt-2">
                Based on <span x-text="totalVotes" class="font-bold"></span> verified reviews
            </p>
        </div>

        {{-- Placeholders for Step 2 (Voting), 3 (Comment Box), and 4 (Comments List) --}}

    </div>
</x-layout>