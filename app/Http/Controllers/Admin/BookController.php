<?php

namespace App\Http\Controllers\Admin; // Correct namespace

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category; // Import Category for dropdowns
use Illuminate\Http\Request;
use Illuminate\Support\Str; // For slug generation
use Illuminate\Support\Facades\Storage; // For image handling
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        // Fetch books with pagination and category relationship
        $books = Book::with('category')->latest()->paginate(15); // Show more per page in admin
        return view('admin.books.index', compact('books'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $categories = Category::orderBy('name')->pluck('name', 'id'); // Get categories for select dropdown
        return view('admin.books.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'author' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'isbn' => 'nullable|string|unique:books,isbn|max:20',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048' // Max 2MB image
        ]);

        $data = $request->except('cover_image'); // Get all data except the image for now
        $data['slug'] = Str::slug($request->title) . '-' . Str::random(5); // Create a unique slug

        // Handle File Upload
        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('public/covers'); // Store in storage/app/public/covers
            // Make sure storage is linked: php artisan storage:link
            // Use Storage facade to get URL path
            $data['cover_image'] = Storage::url($path);
            // OR store relative path if serving directly from public:
            // $filename = time() . '.' . $request->cover_image->extension();
            // $request->cover_image->move(public_path('images/covers'), $filename);
            // $data['cover_image'] = 'images/covers/' . $filename;
        } else {
            $data['cover_image'] = '/images/covers/default.png'; // Default image path if none uploaded
        }


        Book::create($data);

        return redirect()->route('admin.books.index')->with('success', 'Book created successfully.');
    }

    /**
     * Display the specified resource. (Optional for Admin)
     * Can redirect to edit or just show details.
     */
    public function show(Book $book): View // Route model binding
    {
        // For admin, often edit is more useful than show
        // return redirect()->route('admin.books.edit', $book->slug);
        // Or just show simple details:
        $book->load('category'); // Load category if needed
        return view('admin.books.show', compact('book'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Book $book): View // Route model binding
    {
        $categories = Category::orderBy('name')->pluck('name', 'id');
        return view('admin.books.edit', compact('book', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Book $book): RedirectResponse // Route model binding
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'author' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'isbn' => 'nullable|string|max:20|unique:books,isbn,' . $book->id, // Ignore current book's ISBN
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        $data = $request->except('cover_image', '_token', '_method'); // Exclude image and form tokens/methods

        // Only update slug if title changed
        if ($request->title !== $book->title) {
            $data['slug'] = Str::slug($request->title) . '-' . Str::random(5);
        }

        // Handle File Upload (Update/Replace)
        if ($request->hasFile('cover_image')) {
            // Optional: Delete old image if it exists and is not the default
            if ($book->cover_image && $book->cover_image != '/images/covers/default.png' && Storage::exists(str_replace('/storage/', 'public/', $book->cover_image))) {
                Storage::delete(str_replace('/storage/', 'public/', $book->cover_image));
            }

            $path = $request->file('cover_image')->store('public/covers');
            $data['cover_image'] = Storage::url($path);
        }
        // If no new image is uploaded, $data['cover_image'] won't be set,
        // so the existing image path in the database remains unchanged.

        $book->update($data);

        return redirect()->route('admin.books.index')->with('success', 'Book updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book): RedirectResponse // Route model binding
    {
        // Optional: Delete the associated image file before deleting the record
        if ($book->cover_image && $book->cover_image != '/images/covers/default.png' && Storage::exists(str_replace('/storage/', 'public/', $book->cover_image))) {
            Storage::delete(str_replace('/storage/', 'public/', $book->cover_image));
        }

        $book->delete();
        return redirect()->route('admin.books.index')->with('success', 'Book deleted successfully.');
    }
}
