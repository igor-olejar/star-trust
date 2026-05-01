<x-layout>
    {{-- Main Container with Alpine data --}}
    <div x-data="{ 
        weightedScore: {{ $targetUser->averageScore() }},
        ratings: {
            @foreach($categories as $category)
                {{ $category->id }}: {{ $targetUser->ratingsReceived()
                    ->where('reviewer_id', auth()->id())
                    ->first()?->ratingItems()
                    ->where('voting_category_id', $category->id)
                    ->first()?->score ?? 0 }},
            @endforeach
        },
        hoverStates: {},

        async saveRating(categoryId, stars) {
            this.ratings[categoryId] = stars;
            try {
                const response = await fetch('{{ route('vote.save') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        target_user_id: {{ $targetUser->id }},
                        category_id: categoryId,
                        stars: stars
                    })
                });

                if (response.ok) {
                    const data = await response.json();
                    this.weightedScore = data.new_average;
                }
            } catch (error) {
                console.error('Failed to save rating', error);
            }
        }
    }">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight p-6">
                {{ __('Vote for :name', ['name' => $targetUser->name]) }}
            </h2>
        </div>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                    <div class="p-8 text-center">
                        <div class="w-24 h-24 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl font-bold">
                            {{ substr($targetUser->name, 0, 1) }}
                        </div>

                        <h1 class="text-2xl font-bold text-gray-900">{{ $targetUser->name }}</h1>

                        <span class="inline-flex items-center px-3 py-1 mt-2 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
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

                        {{-- NEW: Dynamic Average Score Section --}}
                        <div class="mt-6 flex flex-col items-center justify-center border-t border-gray-50 pt-6">
                            <p class="font-semibold text-gray-400 uppercase text-xs mb-2">Average Rating</p>
                            <div class="flex items-center space-x-1">
                                <template x-for="i in 5">
                                    <svg class="w-6 h-6 transition-colors duration-300"
                                        :class="i <= Math.round(weightedScore) ? 'text-yellow-400' : 'text-gray-200'"
                                        fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                </template>
                                <span class="ml-2 text-xl font-bold text-gray-700" x-text="parseFloat(weightedScore).toFixed(1)"></span>
                            </div>
                        </div>

                        <hr class="my-8 border-gray-100">

                        {{-- Category Voting Stars --}}
                        <div class="space-y-4 max-w-md mx-auto">
                            @foreach($categories as $category)
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-700 font-medium">{{ $category->name }}</span>
                                    <div class="flex items-center space-x-1">
                                        <template x-for="i in 5">
                                            <button type="button"
                                                @mouseenter="hoverStates[{{ $category->id }}] = i"
                                                @mouseleave="hoverStates[{{ $category->id }}] = 0"
                                                @click="saveRating({{ $category->id }}, i)"
                                                class="focus:outline-none transform transition hover:scale-110">
                                                <svg class="w-6 h-6 transition-colors duration-150"
                                                    :class="(hoverStates[{{ $category->id }}] || ratings[{{ $category->id }}]) >= i ? 'text-yellow-400' : 'text-gray-200'"
                                                    fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>