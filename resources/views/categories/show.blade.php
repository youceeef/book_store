<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Category:') }} {{ $category->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-2xl font-semibold mb-6">Books in {{ $category->name }}</h3>

                    @if ($books->isEmpty())
                        <p class="text-center text-gray-500">There are currently no books listed in this category.</p>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                            {{-- Loop through books and use the partial --}}
                            @foreach ($books as $book)
                                @include('partials._book_card', ['book' => $book])
                            @endforeach
                        </div>

                        {{-- Pagination Links --}}
                        <div class="mt-8">
                            {{ $books->links() }}
                        </div>
                    @endif

                    <div class="mt-8">
                        <a href="{{ route('books.index') }}" class="text-blue-500 hover:underline">← Back to All
                            Books</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
