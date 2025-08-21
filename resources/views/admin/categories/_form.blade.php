{{-- resources/views/admin/categories/_form.blade.php --}}

@csrf {{-- CSRF token --}}

{{-- Name --}}
<div class="mb-4">
    <label for="name" class="block text-sm font-medium text-gray-700">Category Name <span
            class="text-red-600">*</span></label>
    <input type="text" name="name" id="name" value="{{ old('name', $category->name ?? '') }}" required autofocus
        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('name') border-red-500 @enderror">
    @error('name')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

{{-- Active Status --}}
<div class="mb-4">
    <div class="flex items-start">
        <div class="flex items-center h-5">
            <input type="checkbox" name="active" id="active"
                {{ old('active', $category->active ?? true) ? 'checked' : '' }}
                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
        </div>
        <div class="ml-3 text-sm">
            <label for="active" class="font-medium text-gray-700">Active</label>
            <p class="text-gray-500">Category will be visible to customers</p>
        </div>
    </div>
    @error('active')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

{{-- Submit Button --}}
<div class="flex justify-end mt-6">
    <a href="{{ route('admin.categories.index') }}"
        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-3">
        Cancel
    </a>
    <button type="submit" class="bg-indigo-600 hover:bg-indigo-800 text-white font-bold py-2 px-4 rounded">
        {{ isset($category) ? 'Update Category' : 'Create Category' }}
    </button>
</div>
