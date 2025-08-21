<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Book: ') }} {{ $book->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">
                    {{-- Display validation errors if any --}}
                    {{-- @if ($errors->any()) ... @endif --}}

                    <form method="POST" action="{{ route('admin.books.update', $book->id) }}"
                        enctype="multipart/form-data"> {{-- Use book ID for update route --}}
                        @method('PUT') {{-- Method spoofing for PUT request --}}
                        @include('admin.books._form', ['book' => $book]) {{-- Include partial, passing the $book variable --}}
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
