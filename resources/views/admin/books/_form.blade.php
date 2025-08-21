{{-- resources/views/admin/books/_form.blade.php --}}

@csrf {{-- CSRF token included here --}}

{{-- Title --}}
<div class="mb-4">
    <label for="title" class="block text-sm font-medium text-gray-700">Title <span class="text-red-600">*</span></label>
    <input type="text" name="title" id="title" value="{{ old('title', $book->title ?? '') }}" required
        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('title') border-red-500 @enderror">
    @error('title')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

{{-- Category --}}
<div class="mb-4">
    <label for="category_id" class="block text-sm font-medium text-gray-700">Category <span
            class="text-red-600">*</span></label>
    <select name="category_id" id="category_id" required
        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('category_id') border-red-500 @enderror">
        <option value="">Select a Category</option>
        @foreach ($categories as $id => $name)
            <option value="{{ $id }}"
                {{ old('category_id', $book->category_id ?? '') == $id ? 'selected' : '' }}>
                {{ $name }}
            </option>
        @endforeach
    </select>
    @error('category_id')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

{{-- Author --}}
<div class="mb-4">
    <label for="author" class="block text-sm font-medium text-gray-700">Author</label>
    <input type="text" name="author" id="author" value="{{ old('author', $book->author ?? '') }}"
        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('author') border-red-500 @enderror">
    @error('author')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

{{-- Description --}}
<div class="mb-4">
    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
    <textarea name="description" id="description" rows="4"
        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('description') border-red-500 @enderror">{{ old('description', $book->description ?? '') }}</textarea>
    @error('description')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

{{-- ISBN --}}
<div class="mb-4">
    <label for="isbn" class="block text-sm font-medium text-gray-700">ISBN</label>
    <input type="text" name="isbn" id="isbn" value="{{ old('isbn', $book->isbn ?? '') }}"
        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('isbn') border-red-500 @enderror">
    @error('isbn')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
{{-- Price --}}
<div class="mb-4">
    <label for="price" class="block text-sm font-medium text-gray-700">Price <span
            class="text-red-600">*</span></label>
    <input type="number" name="price" id="price" value="{{ old('price', $book->price ?? '0.00') }}" required
        min="0" step="0.01"
        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('price') border-red-500 @enderror">
    @error('price')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

{{-- Stock --}}
<div class="mb-4">
    <label for="stock" class="block text-sm font-medium text-gray-700">Stock <span
            class="text-red-600">*</span></label>
    <input type="number" name="stock" id="stock" value="{{ old('stock', $book->stock ?? '0') }}" required
        min="0" step="1"
        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('stock') border-red-500 @enderror">
    @error('stock')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

{{-- Cover Image --}}
<div class="mb-4">
    <label for="cover_image" class="block text-sm font-medium text-gray-700">Cover Image</label>
    <input type="file" name="cover_image" id="cover_image" accept="image/*"
        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 @error('cover_image') border-red-500 @enderror">
    @error('cover_image')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror

    {{-- Show current image if editing --}}
    @isset($book)
        @if ($book->cover_image)
            <div class="mt-4">
                <p class="text-sm text-gray-500 mb-2">Current Image:</p>
                <img src="{{ $book->cover_image ?: asset('images/covers/default.png') }}" alt="Current Cover"
                    class="h-24 w-auto object-contain border rounded">
            </div>
        @endif
    @endisset
</div>

{{-- Submit Button --}}
<div class="flex justify-end mt-6">
    <a href="{{ route('admin.books.index') }}"
        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-3">
        Cancel
    </a>
    <button type="submit" class="bg-indigo-600 hover:bg-indigo-800 text-white font-bold py-2 px-4 rounded">
        {{ isset($book) ? 'Update Book' : 'Create Book' }}
    </button>
</div>
</form>
{{-- End of form --}}
