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
        
        {{-- Comment Logic State --}}
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
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ target_user_id: {{ $targetUser->id }}, category_id: categoryId, stars: stars })
                });

                if (response.ok) {
                    const data = await response.json();
                    this.weightedScore = data.new_average;
                    this.voteCounts[categoryId]++;
                } else { this.ratings[categoryId] = oldRating; }
            } catch (error) { this.ratings[categoryId] = oldRating; }
        },

        async submitComment() {
            if (this.currentCommentCount >= 3 || !this.newComment.trim()) return;
            this.profanityError = false;

            try {
                const response = await fetch('{{ route('vote.comment') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ target_user_id: {{ $targetUser->id }}, comment: this.newComment })
                });

                const data = await response.json();

                if (response.ok) {
                    window.location.reload(); 
                } else if (response.status === 422) {
                    this.profanityError = true;
                } else {
                    alert(data.error || 'Submission failed');
                }
            } catch (error) { console.error('Error:', error); }
        }
    }">
        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow-sm sm:rounded-lg border border-gray-200 overflow-hidden">
                    <div class="p-8 text-center">
                        {{-- Profile Header --}}
                        <div class="w-20 h-20 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold">
                            {{ substr($targetUser->name, 0, 1) }}
                        </div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $targetUser->name }}</h1>
                        <p class="text-sm text-gray-500 uppercase tracking-widest mt-1">{{ $targetUser->user_type_id->name }}</p>

                        {{-- Global Average --}}
                        <div class="mt-6 flex flex-col items-center border-t border-gray-50 pt-6">
                            <div class="flex items-center space-x-1">
                                <template x-for="i in 5">
                                    <svg class="w-6 h-6" :class="i <= Math.round(weightedScore) ? 'text-yellow-400' : 'text-gray-200'" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                                </template>
                                <span class="ml-2 text-xl font-black text-gray-800" x-text="parseFloat(weightedScore).toFixed(1)"></span>
                            </div>
                        </div>

                        <hr class="my-8 border-gray-100">

                        {{-- Anonymous Commenting Section --}}
                        <div class="max-w-md mx-auto mb-10">
                            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6 text-left rounded-r-xl">
                                <p class="text-xs text-blue-700 font-medium">
                                    <strong>Info:</strong> You can leave up to 3 anonymous comments for this user. Your identity is masked via a secure hash.
                                </p>
                            </div>

                            {{-- Profanity Alert --}}
                            <div x-show="profanityError" x-transition class="bg-red-50 border border-red-200 text-red-600 p-3 rounded-lg mb-4 text-xs font-bold text-left">
                                ⚠️ Profanity detected! Please keep your feedback professional.
                            </div>

                            <div class="relative text-left">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Write a Comment</label>
                                <textarea 
                                    x-model="newComment"
                                    :disabled="currentCommentCount >= 3"
                                    maxlength="255"
                                    class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl p-4 text-sm focus:border-indigo-500 focus:ring-0 transition-all disabled:opacity-50"
                                    placeholder="Your anonymous feedback..."
                                    rows="3"></textarea>
                                
                                <div class="flex justify-between mt-2 px-1 text-[10px] font-bold text-gray-400">
                                    <span :class="currentCommentCount >= 3 ? 'text-red-500' : ''">LIMIT: <span x-text="currentCommentCount"></span> / 3</span>
                                    <span><span x-text="newComment.length"></span> / 255</span>
                                </div>

                                <button 
                                    @click="submitComment"
                                    :disabled="currentCommentCount >= 3 || !newComment.trim()"
                                    class="mt-4 w-full bg-slate-900 text-white font-bold py-3 rounded-xl hover:bg-black disabled:bg-gray-200 transition-all shadow-lg active:scale-95">
                                    SUBMIT COMMENT
                                </button>
                            </div>
                        </div>

                        <hr class="my-8 border-gray-100">

                        {{-- Voting Categories --}}
                        <div class="max-w-md mx-auto space-y-6 mb-12">
                            @foreach($categories as $category)
                                <div class="group text-left">
                                    <div class="flex justify-between mb-1 px-1">
                                        <span class="font-bold text-xs text-gray-400 uppercase tracking-widest">{{ $category->name }}</span>
                                        <span class="text-[10px] font-black text-gray-300" :class="voteCounts[{{ $category->id }}] >= 3 ? 'text-red-400' : ''">
                                            <span x-text="Math.max(0, 3 - voteCounts[{{ $category->id }}])"></span> VOTES LEFT
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between bg-gray-50 p-4 rounded-2xl border-2 border-transparent hover:border-indigo-100 hover:bg-white transition-all">
                                        <div class="flex space-x-1">
                                            <template x-for="i in 5">
                                                <button type="button" @click="saveRating({{ $category->id }}, i)" :disabled="voteCounts[{{ $category->id }}] >= 3" class="focus:outline-none transition-transform hover:scale-125">
                                                    <svg class="w-7 h-7" :class="{
                                                        'text-yellow-400': voteCounts[{{ $category->id }}] < 3 && (hoverStates[{{ $category->id }}] || ratings[{{ $category->id }}]) >= i,
                                                        'text-red-200': voteCounts[{{ $category->id }}] >= 3 && ratings[{{ $category->id }}] >= i,
                                                        'text-gray-200': (voteCounts[{{ $category->id }}] < 3 && (hoverStates[{{ $category->id }}] || ratings[{{ $category->id }}]) < i) || (voteCounts[{{ $category->id }}] >= 3 && ratings[{{ $category->id }}] < i)
                                                    }" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                                                </button>
                                            </template>
                                        </div>
                                        <div class="text-xs font-black px-2 py-1 bg-white rounded shadow-sm text-gray-400" :class="voteCounts[{{ $category->id }}] >= 3 ? 'text-red-500 bg-red-50' : ''">
                                            <span x-text="ratings[{{ $category->id }}] || '—'"></span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- All Anonymous Comments Feed --}}
                        <div class="max-w-xl mx-auto text-left border-t border-gray-100 pt-10">
                            <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-6">User Feedback</h3>
                            
                            @forelse($comments as $comment)
                                <div class="bg-gray-50 border border-gray-100 p-5 rounded-2xl mb-4 relative">
                                    <p class="text-sm text-gray-700 leading-relaxed italic">"{{ $comment->comment }}"</p>
                                    <div class="mt-3 flex justify-between items-center">
                                        <span class="text-[10px] font-bold text-indigo-400 uppercase tracking-tighter">Verified Anonymous Voter</span>
                                        <span class="text-[10px] font-bold text-gray-300">{{ $comment->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-10 text-gray-400 italic text-sm">
                                    No comments yet. Be the first to share your experience!
                                </div>
                            @endforelse

                            {{-- Pagination Links --}}
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