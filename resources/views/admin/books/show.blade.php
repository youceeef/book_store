<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Book Details: ') }} {{ $book->title }}
            </h2>
            <a href="{{ route('admin.books.index') }}"
                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to List
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-1">
                            <img src="{{ $book->cover_image ?: asset('images/covers/default.png') }}"
                                alt="{{ $book->title }}" class="w-full h-auto object-contain border rounded">
                        </div>
                        <div class="md:col-span-2 space-y-3">
                            <p><strong>Title:</strong> {{ $book->title }}</p>
                            <p><strong>Slug:</strong> {{ $book->slug }}</p>
                            <p><strong>Author:</strong> {{ $book->author ?: 'N/A' }}</p>
                            <p><strong>Category:</strong> {{ $book->category?->name ?? 'N/A' }}</p>
                            <p><strong>ISBN:</strong> {{ $book->isbn ?: 'N/A' }}</p>
                            <p><strong>Price:</strong> ${{ number_format($book->price, 2) }}</p>
                            <p><strong>Stock:</strong> {{ $book->stock }}</p>
                            <p><strong>Description:</strong></p>
                            <div class="prose max-w-none text-sm">
                                {{ $book->description ?: 'N/A' }}
                            </div>
                            <p class="text-xs text-gray-500 mt-2">Created: {{ $book->created_at->format('Y-m-d H:i') }}
                                | Updated: {{ $book->updated_at->format('Y-m-d H:i') }}</p>
                            <div class="mt-4">
                                <a href="{{ route('admin.books.edit', $book->id) }}"
                                    class="bg-indigo-600 hover:bg-indigo-800 text-white font-bold py-2 px-4 rounded">
                                    Edit Book
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
