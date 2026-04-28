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
        
        <div class="mb-10 text-center md:text-left">
            <h1 class="text-3xl font-extrabold text-black tracking-tight">Member Search</h1>
            <p class="text-slate-400 mt-2">Search StarTrust members by name, city, or type</p>
        </div>

        <div class="relative bg-slate-900 rounded-2xl shadow-2xl border border-slate-800 p-2 mb-12 focus-within:ring-2 focus-within:ring-indigo-500 transition-all">
            <form action="{{ route('search') }}" method="GET" class="flex items-center">
                <div class="pl-4 text-slate-500">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input 
                    type="text" 
                    name="q" 
                    x-model="query"
                    @input.debounce.300ms="fetchSuggestions()"
                    @keydown.escape="suggestions = []"
                    placeholder="Search name, city, country, or 'Venue'..." 
                    class="flex-1 border-none bg-transparent focus:ring-0 text-lg px-4 py-3 text-white placeholder-slate-500"
                    autocomplete="off"
                    autofocus
                >
                <button type="submit" class="bg-white text-slate-950 px-8 py-3 rounded-xl font-bold hover:bg-indigo-500 hover:text-white transition shadow-lg shadow-indigo-500/20 mr-1">
                    Search
                </button>
            </form>

            <div 
                x-show="suggestions.length > 0" 
                x-transition 
                @click.away="suggestions = []"
                class="absolute left-0 right-0 top-full mt-2 bg-slate-900 border border-slate-800 shadow-2xl rounded-xl z-50 overflow-hidden"
                x-cloak
            >
                <template x-for="item in suggestions" :key="item.id">
                    <a :href="'/search?q=' + item.name" class="block px-6 py-4 hover:bg-slate-800 border-b border-slate-800 last:border-none transition">
                        <div class="flex justify-between items-center">
                            <div>
                                <span class="block font-bold text-white" x-text="item.name"></span>
                                <span class="text-xs text-slate-500" x-text="`${item.city}, ${item.country_name}`"></span>
                            </div>
                            <span 
                                :class="{
                                    'bg-indigo-500/10 text-indigo-400': item.user_type_id == 1, 
                                    'bg-emerald-500/10 text-emerald-400': item.user_type_id == 2,
                                    'bg-amber-500/10 text-amber-400': item.user_type_id == 3
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
            @if(!request()->filled('q'))
                <div class="text-center py-24 border-2 border-dashed border-slate-800 rounded-3xl">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-slate-900 rounded-full mb-6 border border-slate-800">
                        <svg class="w-10 h-10 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-white">Find your next connection</h2>
                    <p class="text-slate-500 mt-2 max-w-sm mx-auto">Please enter a search term above to browse the StarTrust global database.</p>
                </div>
            @else
                @forelse($results as $result)
                    <div class="group bg-slate-900 p-6 rounded-2xl border border-slate-800 flex flex-col md:flex-row justify-between items-center gap-6 hover:border-indigo-500/50 transition-all duration-300">
                        <div class="flex items-center gap-5 w-full md:w-auto">
                            <div class="h-16 w-16 bg-slate-800 text-indigo-400 rounded-2xl flex items-center justify-center text-xl font-black uppercase border border-slate-700 group-hover:border-indigo-500/50 transition-colors">
                                {{ substr($result->name, 0, 2) }}
                            </div>
                            <div>
                                <div class="flex items-center gap-3 mb-1">
                                    <h3 class="text-xl font-bold text-white group-hover:text-indigo-400 transition-colors">{{ $result->name }}</h3>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-tighter
                                        {{ $result->user_type_id->value == 1 ? 'bg-indigo-500/10 text-indigo-400' : '' }}
                                        {{ $result->user_type_id->value == 2 ? 'bg-emerald-500/10 text-emerald-400' : '' }}
                                        {{ $result->user_type_id->value == 3 ? 'bg-amber-500/10 text-amber-400' : '' }}
                                    ">
                                        {{ $result->user_type_id->label() }}
                                    </span>
                                </div>
                                <p class="text-slate-400 flex items-center gap-1.5 text-sm">
                                    <svg class="w-4 h-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    {{ $result->city }}, {{ $result->country_code }}
                                </p>
                            </div>
                        </div>

                        <div class="w-full md:w-auto">
                            <a href="/vote/{{ $result->id }}" class="block text-center bg-white text-slate-950 px-8 py-3 rounded-xl font-extrabold text-sm hover:bg-indigo-500 hover:text-white transition-all shadow-xl shadow-white/5 active:scale-95">
                                Vote
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-20">
                        <svg class="w-16 h-16 mx-auto text-slate-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="text-xl font-bold text-white">No matches found</h3>
                        <p class="text-slate-500 mt-1">We couldn't find anything for "{{ request('q') }}".</p>
                    </div>
                @endforelse
            @endif
        </div>

        <div class="mt-12">
            {{ $results->appends(request()->query())->links() }}
        </div>
    </div>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</x-layout>