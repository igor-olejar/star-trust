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

                    <hr class="my-8 border-gray-100">
                </div>
            </div>
        </div>
    </div>
</x-layout>