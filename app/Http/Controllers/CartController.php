<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Darryldecode\Cart\Facades\CartFacade as Cart; // Use the Facade alias
use App\Models\Book; // Import Book model
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CartController extends Controller
{
    /**
     * Display the shopping cart.
     */
    public function index(): View
    {
        $cartItems = Cart::getContent();
        $cartTotal = Cart::getTotal();
        $cartCount = Cart::getTotalQuantity(); // Get total number of items

        return view('cart.index', compact('cartItems', 'cartTotal', 'cartCount'));
    }

    /**
     * Add an item to the cart.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $book = Book::findOrFail($request->book_id);

        // Check if enough stock is available
        if ($book->stock < $request->quantity) {
            return back()->with('error', 'Not enough stock available for "' . $book->title . '". Only ' . $book->stock . ' left.');
        }

        Cart::add([
            'id' => $book->id, // unique row ID
            'name' => $book->title,
            'price' => $book->price,
            'quantity' => $request->quantity,
            'attributes' => [
                'image' => $book->cover_image ?: 'images/covers/default.png',
                'slug' => $book->slug,
                'stock' => $book->stock // Store original stock for reference if needed
            ]
            // 'associatedModel' => $book // Optionally associate the Eloquent model
        ]);

        // Optionally decrease stock here, or wait until checkout is complete
        // $book->decrement('stock', $request->quantity);

        return redirect()->route('cart.index')->with('success', 'Book added to cart successfully!');
    }

    /**
     * Update the specified item quantity in storage.
     */
    public function update(Request $request, string $rowId): RedirectResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        // Get item details before update to check stock
        $item = Cart::get($rowId);
        if (!$item) {
            return redirect()->route('cart.index')->with('error', 'Cart item not found.');
        }

        $book = Book::find($item->id); // Find the corresponding book
        if (!$book) {
            // If book deleted while in cart, remove it
            Cart::remove($rowId);
            return redirect()->route('cart.index')->with('error', 'The book for this cart item no longer exists and was removed.');
        }

        // Check stock before updating
        if ($book->stock < $request->quantity) {
            return redirect()->route('cart.index')
                ->with('error', 'Not enough stock for "' . $book->title . '". Only ' . $book->stock . ' available.');
        }

        Cart::update($rowId, [
            'quantity' => [
                'relative' => false, // set to false to replace current quantity
                'value' => $request->quantity
            ],
        ]);

        return redirect()->route('cart.index')->with('success', 'Cart updated successfully.');
    }

    /**
     * Remove the specified item from storage.
     */
    public function destroy(string $rowId): RedirectResponse
    {
        Cart::remove($rowId);
        return redirect()->route('cart.index')->with('success', 'Item removed from cart.');
    }

    /**
     * Clear the entire cart.
     */
    public function clear(): RedirectResponse
    {
        Cart::clear();
        return redirect()->route('cart.index')->with('success', 'Cart cleared successfully.');
    }
}
