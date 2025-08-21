{{-- resources/views/partials/_book_card.blade.php --}}
{{-- Expects a $book variable to be passed --}}
<div
    class="group bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 flex flex-col justify-between">
    <div> {{-- Content wrapper --}}
        <div class="relative overflow-hidden">
            <a href="{{ route('books.show', $book->slug) }}" class="block aspect-w-3 aspect-h-4">
                <img src="{{ $book->cover_image ?: asset('images/covers/default.png') }}" alt="{{ $book->title }} Cover"
                    class="w-full h-64 object-cover transform group-hover:scale-105 transition-transform duration-300">
            </a>
            {{-- Category Badge --}}
            <a href="{{ route('categories.show', $book->category->slug) }}"
                class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm rounded-full px-3 py-1 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition duration-150">
                {{ $book->category->name }}
            </a>
        </div>

        <div class="p-5">
            <a href="{{ route('books.show', $book->slug) }}"
                class="block group-hover:text-indigo-600 transition-colors duration-150">
                <h3 class="font-semibold text-lg leading-snug mb-2 line-clamp-2">{{ $book->title }}</h3>
            </a>
            <div class="flex items-center justify-between mb-4">
                <p class="text-gray-600 text-sm">by <span class="font-medium">{{ $book->author }}</span></p>
                <p class="text-lg font-bold text-gray-900">${{ number_format($book->price, 2) }}</p>
            </div>

            {{-- Rating & Stock Info --}}
            <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                <div class="flex items-center">
                    <div class="flex items-center">
                        @for ($i = 1; $i <= 5; $i++)
                            <svg class="w-4 h-4 {{ $i <= ($book->rating ?? 4) ? 'text-yellow-400' : 'text-gray-300' }}"
                                fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        @endfor
                    </div>
                </div>
                <span class="{{ $book->in_stock ? 'text-green-600' : 'text-red-600' }} font-medium">
                    {{ $book->in_stock ? 'In Stock' : 'Out of Stock' }}
                </span>
            </div>
        </div>
    </div>

    {{-- Add to Cart Button Area --}}
    <div class="px-5 pb-5 mt-auto"> {{-- Added mt-auto to push button down --}}
        <form action="{{ route('cart.store') }}" method="POST" class="w-full">
            @csrf
            <input type="hidden" name="book_id" value="{{ $book->id }}">
            <input type="hidden" name="quantity" value="1">
            <button type="submit"
                class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-300 disabled:opacity-50 disabled:cursor-not-allowed"
                {{ !$book->in_stock ? 'disabled' : '' }}>
                {{ !$book->in_stock ? 'Out of Stock' : 'Add to Cart' }}
            </button>
        </form>
    </div>
</div>
