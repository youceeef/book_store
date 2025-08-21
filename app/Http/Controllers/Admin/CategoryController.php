<?php

namespace App\Http\Controllers\Admin; // Correct namespace

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str; // For slug generation
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $categories = Category::latest()->paginate(15); // Paginate categories
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'active' => 'sometimes|nullable', // Checkbox might not be sent if unchecked
        ]);

        Category::create([
            'name' => $request->name,
            'active' => $request->has('active'), // Will be true if checkbox was checked, false if not
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully.');
    }

    // We excluded show route, so this method is not needed by default if using ->except(['show'])
    // public function show(Category $category): View { ... }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category): View // Route model binding using ID
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'active' => 'sometimes|nullable', // Checkbox might not be sent if unchecked
        ]);

        $category->update([
            'name' => $request->name,
            'active' => $request->has('active'), // Will be true if checkbox was checked, false if not
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category): RedirectResponse // Route model binding using ID
    {
        // Optional: Check if category has associated books before deleting
        if ($category->books()->count() > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Cannot delete category: It has associated books.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully.');
    }
}
