<x-layout>
    <div class="max-w-4xl mx-auto my-12 px-6" x-data="{ 
        query: '{{ request('q') }}', 
        suggestions: [],
        fetchSuggestions() {
            if (this.query.length < 2) { this.suggestions = []; return; }
            fetch(`/api/search-suggestions?q=${this.query}`)
                .then(res => res.json())
                .then(data => { this.suggestions = data; });
        }
    }">
        
        <div class="relative bg-white rounded-2xl shadow-sm border border-slate-200 p-2 mb-10 focus-within:ring-2 focus-within:ring-indigo-500 transition-all">
            <form action="{{ route('search') }}" method="GET" class="flex items-center">
                <input 
                    type="text" 
                    name="q" 
                    x-model="query"
                    @input.debounce.300ms="fetchSuggestions()"
                    @keydown.escape="suggestions = []"
                    placeholder="Search name, city, country (GB), or 'Venue'..." 
                    class="flex-1 border-none focus:ring-0 text-lg px-4 py-3"
                    autocomplete="off"
                >
                <button type="submit" class="bg-indigo-600 text-white px-6 py-3 rounded-xl font-bold mr-1">
                    Search
                </button>
            </form>

            <div 
                x-show="suggestions.length > 0" 
                x-transition 
                @click.away="suggestions = []"
                class="absolute left-0 right-0 top-full mt-2 bg-white border border-slate-200 shadow-xl rounded-xl z-50 overflow-hidden"
                x-cloak
            >
            <template x-for="item in suggestions" :key="item.id">
                <a :href="'/search?q=' + item.name" class="block px-6 py-4 hover:bg-slate-50 border-b border-slate-100 last:border-none transition">
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="block font-bold text-slate-900" x-text="item.name"></span>
                            <span class="text-xs text-slate-500">
                                <span x-text="item.city"></span>, <span x-text="item.country_name"></span>
                            </span>
                        </div>
                        <span 
                            :class="{
                                'bg-indigo-100 text-indigo-700': item.user_type_id == 1, 
                                'bg-emerald-100 text-emerald-700': item.user_type_id == 2,
                                'bg-amber-100 text-amber-700': item.user_type_id == 3
                            }"
                            class="text-[10px] font-black uppercase tracking-widest px-2 py-1 rounded" 
                            x-text="item.user_type_label">
                        </span>
                    </div>
                </a>
            </template>
            </div>
        </div>

        <div class="space-y-4">
            @forelse($results as $result)
                <div class="bg-white p-6 rounded-xl border border-slate-200 flex justify-between items-center hover:border-indigo-300 transition shadow-sm">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <h3 class="text-xl font-bold text-slate-900">{{ $result->name }}</h3>
                            @if($result->user_type_id->value == 1)
                                <span class="bg-indigo-100 text-indigo-700 text-[10px] uppercase font-black px-2 py-0.5 rounded">Venue</span>
                            @endif
                        </div>
                        <p class="text-slate-500 text-sm">
                            {{ $result->city }}, {{ $result->country_code }}
                        </p>
                    </div>
                    <a href="#" class="bg-slate-50 hover:bg-indigo-600 hover:text-white text-slate-600 px-4 py-2 rounded-lg font-bold text-sm transition">
                        Profile &rarr;
                    </a>
                </div>
            @empty
                <div class="text-center py-12">
                    <p class="text-slate-400 italic">No matches found for "{{ request('q') }}"</p>
                </div>
            @endforelse
        </div>
        
        <div class="mt-8">
            {{ $results->links() }}
        </div>
    </div>
</x-layout>