<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $book->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Back to Books Link --}}
            <div class="mb-6">
                <a href="{{ route('books.index') }}"
                    class="inline-flex items-center text-sm text-gray-600 hover:text-indigo-600">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Books
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl">
                <div class="p-8">
                    <div class="container mx-auto">
                        <div class="lg:grid lg:grid-cols-2 lg:gap-12">
                            {{-- Book Cover --}}
                            <div class="mb-8 lg:mb-0">
                                <div class="aspect-w-3 aspect-h-4 rounded-xl overflow-hidden shadow-lg">
                                    <img src="{{ asset($book->cover_image ?: 'images/covers/default.png') }}"
                                        alt="{{ $book->title }} Cover" class="w-full h-full object-cover">
                                </div>
                            </div>

                            {{-- Book Details --}}
                            <div class="flex flex-col">
                                <div class="flex-1">
                                    <h1 class="text-4xl font-bold text-gray-900 mb-2">{{ $book->title }}</h1>
                                    <p class="text-xl text-gray-600 mb-6">by <span
                                            class="font-medium">{{ $book->author }}</span></p>

                                    {{-- Rating --}}
                                    <div class="flex items-center mb-6">
                                        <div class="flex items-center">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <svg class="w-5 h-5 {{ $i <= ($book->rating ?? 4) ? 'text-yellow-400' : 'text-gray-300' }}"
                                                    fill="currentColor" viewBox="0 0 20 20">
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                            @endfor
                                        </div>
                                        <span class="ml-2 text-sm text-gray-600">({{ rand(10, 150) }} reviews)</span>
                                    </div>

                                    {{-- Category & Stock Status --}}
                                    <div class="flex items-center space-x-4 mb-6">
                                        <a href="{{ route('categories.show', $book->category->slug) }}"
                                            class="inline-flex items-center px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 text-sm font-medium hover:bg-indigo-100 transition duration-150">
                                            {{ $book->category->name }}
                                        </a>
                                        </span>
                                        @if ($book->isbn)
                                            <span
                                                class="inline-block bg-blue-100 rounded-full px-3 py-1 text-sm font-semibold text-blue-800 mr-2 mb-2">
                                                ISBN: {{ $book->isbn }}
                                            </span>
                                        @endif
                                    </div>

                                    <p class="text-2xl font-bold text-blue-600 mb-4">
                                        ${{ number_format($book->price, 2) }}
                                    </p>

                                    <div class="prose max-w-none mb-6"> {{-- Tailwind typography plugin class for nice text formatting --}}
                                        <h3 class="text-lg font-semibold mb-2 border-b pb-1">Description</h3>
                                        <p>{{ $book->description ?: 'No description available.' }}</p>
                                    </div>


                                    {{-- Add to Cart Button (Placeholder) --}}
                                    <div class="mt-6">
                                        {{-- Add to Cart Form --}}
                                        <form action="{{ route('cart.store') }}" method="POST" class="inline-block">
                                            @csrf
                                            <input type="hidden" name="book_id" value="{{ $book->id }}">
                                            <div class="flex items-center">
                                                <input type="number" name="quantity" value="1" min="1"
                                                    max="{{ $book->stock }}"
                                                    class="w-20 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 mr-3"
                                                    {{ $book->stock <= 0 ? 'disabled' : '' }}>
                                                <button type="submit"
                                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded transition duration-300 text-lg disabled:opacity-50 disabled:cursor-not-allowed"
                                                    {{ $book->stock <= 0 ? 'disabled' : '' }}>
                                                    {{ $book->stock <= 0 ? 'Out of Stock' : 'Add to Cart' }}
                                                </button>
                                            </div>
                                        </form>
                                        @if ($book->stock > 0 && $book->stock <= 10)
                                            <span class="ml-4 text-red-600 font-semibold">Only {{ $book->stock }} left
                                                in
                                                stock!</span>
                                        @elseif($book->stock <= 0 && $book->stock !== null)
                                            {{-- Added check for null stock --}}
                                            <span class="ml-4 text-gray-500 font-semibold">Out of stock</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</x-app-layout>
