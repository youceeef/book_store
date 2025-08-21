<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{-- Show dynamic title based on search --}}
            @if (isset($searchTerm) && $searchTerm)
                {{ __('Search Results for:') }} "{{ $searchTerm }}"
            @else
                {{ __('All Books') }}
            @endif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Search Form --}}
            <div class="mb-8">
                <form action="{{ route('books.index') }}" method="GET"
                    class="flex flex-col sm:flex-row gap-4 max-w-3xl mx-auto">
                    <div class="flex-1">
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Search by title, author, or category..."
                                class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <button type="submit"
                        class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:w-auto">
                        Search Books
                    </button>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Display different message if search yielded no results --}}
                    @if ($books->isEmpty())
                        @if (isset($searchTerm) && $searchTerm)
                            <p class="col-span-full text-center text-gray-500">No books found matching your search
                                "{{ $searchTerm }}".</p>
                            <div class="text-center mt-4">
                                <a href="{{ route('books.index') }}" class="text-blue-500 hover:underline">Clear
                                    Search</a>
                            </div>
                        @else
                            <p class="col-span-full text-center text-gray-500">No books available at the moment.</p>
                        @endif
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                            @foreach ($books as $book)
                                {{-- Changed from forelse --}}
                                @include('partials._book_card', ['book' => $book])
                            @endforeach
                        </div>

                        {{-- Pagination Links --}}
                        <div class="mt-8">
                            {{-- withQueryString() in controller handles adding search params --}}
                            {{ $books->links() }}
                        </div>

                        {{-- Add a 'Clear Search' link if searching --}}
                        @if (isset($searchTerm) && $searchTerm)
                            <div class="mt-4 text-center">
                                <a href="{{ route('books.index') }}" class="text-blue-500 hover:underline">View All
                                    Books</a>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
