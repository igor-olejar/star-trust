<x-layout>
    <div class="max-w-4xl mx-auto my-12 px-4">
        <h1>HERE</h1>
        @php
            var_dump($results);
        @endphp
        @foreach ($results as $result)
            <p>{{ $result->name }}</p>
        @endforeach
        
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Member Search</h1>
                <p class="text-slate-500 mt-1">Search the global StarTrust database</p>
            </div>
            <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Dashboard
            </a>
        </div>

        <form action="{{ route('search') }}" method="GET" class="mb-8">
            <div class="relative group">
                <input type="text" 
                       name="q" 
                       value="{{ request('q') }}"
                       placeholder="Search by name, city, or profile type..." 
                       autofocus
                       class="w-full pl-12 pr-4 py-4 rounded-2xl border-slate-200 shadow-sm transition-all focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                
                <div class="absolute left-4 top-4 text-slate-400 group-focus-within:text-indigo-500 transition-colors">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                @if(request('q'))
                    <a href="{{ route('search') }}" class="absolute right-4 top-4 text-slate-300 hover:text-slate-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </a>
                @endif
            </div>
        </form>

        @if(request('q'))
            <div class="mb-6">
                <p class="text-sm text-slate-500">
                    Showing results for <span class="font-bold text-slate-800">"{{ request('q') }}"</span>
                </p>
            </div>
        @endif

        <div class="space-y-4">
            @forelse($results as $result)
                <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm hover:border-indigo-300 transition-colors group">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="font-bold text-slate-900 text-lg group-hover:text-indigo-600 transition-colors">
                                {{ $result->name }}
                            </h4>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-sm text-slate-500 flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    {{ $result->city ?? 'Location not set' }}
                                </span>
                                <span class="text-slate-300">•</span>
                                <span class="text-xs font-bold uppercase tracking-wider text-indigo-500">
                                    {{ $result->user_type_id->label() }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="text-right">
                             <a href="#" class="inline-flex items-center px-4 py-2 bg-slate-50 hover:bg-indigo-50 text-slate-700 hover:text-indigo-700 rounded-lg text-xs font-bold transition">
                                View Profile
                             </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-20 bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200">
                    <div class="text-slate-400 mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-slate-500 font-medium">No members found matching your search.</p>
                    <p class="text-sm text-slate-400">Try a different keyword or location.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $results->appends(request()->query())->links() }}
        </div>
    </div>
</x-layout>