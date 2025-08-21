<?php

namespace App\Http\Controllers;

use App\Models\Category; // Import Category if adding filtering later
use App\Models\Book;
use Illuminate\Http\Request; // Keep Request for potential future use
use Illuminate\Support\Facades\Log;
use Illuminate\View\View; // Import View

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View // Inject Request
    {
        // Start with the base query including category relationship
        $query = Book::query()->with('category');

        $searchTerm = $request->input('search'); // Get search term from request

        // Apply search filter if search term exists
        if ($searchTerm) {
            Log::info("Searching for: {$searchTerm}"); // Log search term
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('author', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('isbn', 'LIKE', "%{$searchTerm}%");
                // Optional: ->orWhere('description', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Continue with ordering and pagination
        $books = $query->latest()->paginate(12)
            ->withQueryString(); // IMPORTANT: Appends query string (like ?search=...) to pagination links

        // Pass books and the search term (for display/form value) to the view
        return view('books.index', compact('books', 'searchTerm'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book): View // Use Route Model Binding
    {
        // Laravel automatically finds the Book by its ID (or slug if configured)
        // Eager load category if needed (often useful on detail page too)
        $book->load('category');

        // Pass the single book to the view
        return view('books.show', compact('book'));
    }

    // Store, Create, Edit, Update, Destroy methods will be used for Admin later
}
