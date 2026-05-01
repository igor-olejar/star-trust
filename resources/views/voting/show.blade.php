<x-layout>
    {{-- Main Container with Alpine data --}}
    <div x-data="{ 
        weightedScore: {{ $targetUser->averageScore() }},
        ratings: {
            @foreach($categories as $category)
                {{ $category->id }}: {{ $existingScores[$category->id] ?? 0 }},
            @endforeach
        },
        voteCounts: {
            @foreach($categories as $category)
                {{ $category->id }}: {{ $existingVotes[$category->id] ?? 0 }},
            @endforeach
        },
        hoverStates: {},

        {{-- Comment State --}}
        newComment: '',
        currentCommentCount: {{ $comments->where('reviewer_hash', hash_hmac('sha256', auth()->id(), config('app.key')))->count() }},
        profanityError: false,

        async saveRating(categoryId, stars) {
            if (this.voteCounts[categoryId] >= 3) return;

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
        },

        async submitComment() {
            if (this.currentCommentCount >= 3 || !this.newComment.trim()) return;
            this.profanityError = false;

            try {
                const response = await fetch('{{ route('vote.comment') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        target_user_id: {{ $targetUser->id }},
                        comment: this.newComment
                    })
                });

                if (response.ok) {
                    window.location.reload(); 
                } else if (response.status === 422) {
                    this.profanityError = true;
                } else {
                    const data = await response.json();
                    alert(data.error || 'Submission failed');
                }
            } catch (error) {
                console.error('Comment submission failed', error);
            }
        }
    }">
        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                    <div class="p-8 text-center">
                        {{-- User Avatar & Name --}}
                        <div class="w-24 h-24 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl font-bold">
                            {{ substr($targetUser->name, 0, 1) }}
                        </div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $targetUser->name }}</h1>
                        <span class="inline-flex items-center px-3 py-1 mt-2 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                            {{ $targetUser->user_type_id->name }}
                        </span>

                        {{-- Dynamic Global Average Score --}}
                        <div class="mt-6 flex flex-col items-center justify-center border-t border-gray-50 pt-6">
                            <p class="font-semibold text-gray-400 uppercase text-xs mb-2">Overall Community Rating</p>
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

                        {{-- Voting Categories Section --}}
                        <div class="max-w-md mx-auto space-y-6 mb-10">
                            @foreach($categories as $category)
                                <div class="group">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="font-bold text-sm tracking-tight text-gray-700 uppercase">
                                            {{ $category->name }}
                                        </span>
                                        <span class="text-[10px] font-black uppercase tracking-tighter" 
                                              :class="voteCounts[{{ $category->id }}] >= 3 ? 'text-red-500' : 'text-gray-300'">
                                            <span x-text="Math.max(0, 3 - voteCounts[{{ $category->id }}])"></span> Votes Left
                                        </span>
                                    </div>
                                    
                                    <div class="flex items-center justify-between bg-gray-50 p-3 rounded-xl border border-transparent transition-all hover:bg-white hover:border-indigo-100">
                                        <div class="flex items-center space-x-1">
                                            <template x-for="i in 5">
                                                <button type="button"
                                                    @mouseenter="if(voteCounts[{{ $category->id }}] < 3) hoverStates[{{ $category->id }}] = i"
                                                    @mouseleave="hoverStates[{{ $category->id }}] = 0"
                                                    @click="saveRating({{ $category->id }}, i)"
                                                    :disabled="voteCounts[{{ $category->id }}] >= 3"
                                                    class="focus:outline-none transition-transform"
                                                    :class="voteCounts[{{ $category->id }}] < 3 ? 'hover:scale-125' : 'cursor-not-allowed'">
                                                    
                                                    <svg class="w-7 h-7 transition-colors duration-150"
                                                        :class="{
                                                            'text-yellow-400': voteCounts[{{ $category->id }}] < 3 && (hoverStates[{{ $category->id }}] || ratings[{{ $category->id }}]) >= i,
                                                            'text-red-200': voteCounts[{{ $category->id }}] >= 3 && ratings[{{ $category->id }}] >= i,
                                                            'text-gray-200': (voteCounts[{{ $category->id }}] < 3 && (hoverStates[{{ $category->id }}] || ratings[{{ $category->id }}]) < i) || (voteCounts[{{ $category->id }}] >= 3 && ratings[{{ $category->id }}] < i)
                                                        }"
                                                        fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                </button>
                                            </template>
                                        </div>
                                        <div class="text-xs font-bold px-2 py-1 rounded shadow-sm"
                                             :class="voteCounts[{{ $category->id }}] >= 3 ? 'bg-red-50 text-red-400' : 'bg-white text-gray-400'">
                                            <span x-text="ratings[{{ $category->id }}] > 0 ? ratings[{{ $category->id }}] : '—'"></span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <hr class="my-10 border-gray-100">

                        {{-- Comment Form Section --}}
                        <div class="max-w-md mx-auto mb-12">
                            <div class="text-left mb-6">
                                <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-2">Leave Anonymous Feedback</h3>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-tight">
                                    Limit: <span x-text="currentCommentCount"></span> / 3 Comments
                                </p>
                            </div>

                            {{-- Profanity Alert Box --}}
                            <template x-if="profanityError">
                                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4 text-left rounded-r-xl transition-all">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        <p class="text-xs text-red-700 font-black uppercase">Profanity Detected</p>
                                    </div>
                                    <p class="text-xs text-red-600 mt-1">Your comment contains prohibited language. Please keep feedback professional.</p>
                                </div>
                            </template>

                            <div class="relative text-left">
                                <textarea 
                                    x-model="newComment"
                                    @input="profanityError = false"
                                    :disabled="currentCommentCount >= 3"
                                    maxlength="255"
                                    class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl p-4 text-sm focus:border-indigo-500 focus:ring-0 transition-all disabled:opacity-50"
                                    placeholder="Share your experience..."
                                    rows="3"></textarea>
                                
                                <div class="flex justify-between mt-2 px-1 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                    <span><span x-text="newComment.length"></span> / 255 Characters</span>
                                    <span :class="currentCommentCount >= 3 ? 'text-red-500' : ''">Used: <span x-text="currentCommentCount"></span>/3</span>
                                </div>

                                <button 
                                    @click="submitComment"
                                    :disabled="currentCommentCount >= 3 || !newComment.trim()"
                                    class="mt-4 w-full bg-indigo-600 text-white font-bold py-4 rounded-xl hover:bg-indigo-700 disabled:bg-slate-200 transition-all shadow-md active:scale-95 uppercase text-xs tracking-widest">
                                    Post Anonymous Comment
                                </button>
                            </div>
                        </div>

                        {{-- Paginated Comments Feed --}}
                        <div class="max-w-xl mx-auto text-left border-t border-gray-100 pt-10">
                            <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-6">User Feedback</h3>
                            
                            @forelse($comments as $comment)
                                <div class="bg-gray-50 border border-gray-100 p-5 rounded-2xl mb-4 relative transition-hover hover:bg-gray-100">
                                    <p class="text-sm text-gray-700 leading-relaxed italic">"{{ $comment->comment }}"</p>
                                    <div class="mt-3 flex justify-between items-center">
                                        <span class="text-[10px] font-bold text-indigo-400 uppercase tracking-tighter">Verified Review</span>
                                        <span class="text-[10px] font-bold text-gray-300">{{ $comment->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-10 text-gray-400 italic text-sm">
                                    No comments yet.
                                </div>
                            @endforelse

                            <div class="mt-8">
                                {{ $comments->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>