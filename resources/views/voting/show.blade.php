<x-layout>
    <div x-data="{ 
        weightedScore: {{ $targetUser->averageScore() }},
        ratings: {
            @foreach($categories as $cat)
                {{ $cat->id }}: {{ $existingScores[$cat->id] ?? 0 }},
            @endforeach
        },
        voteCounts: {
            @foreach($categories as $cat)
                {{ $cat->id }}: {{ $existingVotes[$cat->id] ?? 0 }},
            @endforeach
        },
        hoverStates: {},

        async saveRating(categoryId, stars) {
            if (this.voteCounts[categoryId] >= 3) return;

            // Optimistic update
            const oldRating = this.ratings[categoryId];
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
                    this.voteCounts[categoryId]++;
                } else {
                    this.ratings[categoryId] = oldRating;
                }
            } catch (error) {
                this.ratings[categoryId] = oldRating;
                console.error('Failed to save rating', error);
            }
        }
    }">
        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                    <div class="p-8 text-center">
                        {{-- Profile Header --}}
                        <div class="w-24 h-24 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl font-bold">
                            {{ substr($targetUser->name, 0, 1) }}
                        </div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $targetUser->name }}</h1>
                        
                        {{-- Dynamic Average Stars --}}
                        <div class="mt-6 flex flex-col items-center justify-center border-t border-gray-50 pt-6">
                            <p class="font-semibold text-gray-400 uppercase text-xs mb-2">Average Rating</p>
                            <div class="flex items-center space-x-1">
                                <template x-for="i in 5">
                                    <svg class="w-8 h-8 transition-colors duration-300"
                                        :class="i <= Math.round(weightedScore) ? 'text-yellow-400' : 'text-gray-200'"
                                        fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                </template>
                                <span class="ml-3 text-2xl font-black text-gray-800" x-text="parseFloat(weightedScore).toFixed(1)"></span>
                            </div>
                        </div>

                        <hr class="my-8 border-gray-100">

                        {{-- Voting List --}}
                        <div class="max-w-md mx-auto space-y-6">
                            @foreach($categories as $category)
                                <div class="group text-left">
                                    <div class="flex items-center justify-between mb-1 px-1">
                                        <span class="font-bold text-xs tracking-widest text-slate-400 uppercase" 
                                              :class="voteCounts[{{ $category->id }}] >= 3 ? 'text-red-300' : ''">
                                            {{ $category->name }}
                                        </span>
                                        <span class="text-[10px] font-black uppercase tracking-tighter" 
                                              :class="voteCounts[{{ $category->id }}] >= 3 ? 'text-red-500' : 'text-slate-300'">
                                            <span x-text="Math.max(0, 3 - voteCounts[{{ $category->id }}])"></span> Votes Left
                                        </span>
                                    </div>
                                    
                                    <div class="flex items-center justify-between bg-slate-50 p-4 rounded-2xl border-2 border-transparent transition-all"
                                         :class="voteCounts[{{ $category->id }}] >= 3 ? 'bg-red-50/30' : 'hover:border-indigo-100 hover:bg-white hover:shadow-sm'">
                                        
                                        <div class="flex items-center space-x-1">
                                            <template x-for="i in 5">
                                                <button type="button"
                                                    @mouseenter="if(voteCounts[{{ $category->id }}] < 3) hoverStates[{{ $category->id }}] = i"
                                                    @mouseleave="hoverStates[{{ $category->id }}] = 0"
                                                    @click="saveRating({{ $category->id }}, i)"
                                                    :disabled="voteCounts[{{ $category->id }}] >= 3"
                                                    class="focus:outline-none transition-transform"
                                                    :class="voteCounts[{{ $category->id }}] < 3 ? 'hover:scale-125 active:scale-95' : 'cursor-not-allowed'">
                                                    
                                                    <svg class="w-8 h-8 transition-colors duration-150"
                                                        :class="{
                                                            'text-yellow-400': voteCounts[{{ $category->id }}] < 3 && (hoverStates[{{ $category->id }}] || ratings[{{ $category->id }}]) >= i,
                                                            'text-red-200': voteCounts[{{ $category->id }}] >= 3 && ratings[{{ $category->id }}] >= i,
                                                            'text-slate-200': (voteCounts[{{ $category->id }}] < 3 && (hoverStates[{{ $category->id }}] || ratings[{{ $category->id }}]) < i) || (voteCounts[{{ $category->id }}] >= 3 && ratings[{{ $category->id }}] < i)
                                                        }"
                                                        fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                </button>
                                            </template>
                                        </div>

                                        <div class="text-sm font-black px-3 py-1 rounded-lg shadow-inner"
                                             :class="voteCounts[{{ $category->id }}] >= 3 ? 'bg-red-100 text-red-500' : 'bg-white text-slate-400'">
                                            <span x-text="ratings[{{ $category->id }}] > 0 ? ratings[{{ $category->id }}] : '—'"></span>
                                        </div>
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