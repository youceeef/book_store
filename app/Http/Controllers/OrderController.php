<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\Book;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Exception;

class OrderController extends Controller
{
    /**
     * Display a listing of the authenticated user's orders.
     */
    public function index(): View
    {
        $user = Auth::user();

        // Fetch orders for the logged-in user, newest first, with pagination
        $orders = $user->orders() // Use the relationship defined in User model
            ->withCount('items') // Eager load count of items per order
            ->latest() // Order by creation date descending
            ->paginate(10); // Show 10 orders per page

        return view('orders.index', compact('orders'));
    }

    /**
     * Display the specified order details for the authenticated user.
     */
    public function show(Order $order): View | RedirectResponse
    {
        $user = Auth::user();

        if ($order->user_id !== $user->id) {
            return redirect()->route('orders.index')->with('error', 'You are not authorized to view this order.');
        }

        $order->load(['items', 'items.book']);

        return view('orders.show', compact('order'));
    }

    /**
     * Reduce stock quantities for items in an order.
     * Returns true if successful, false if there's insufficient stock.
     */
    protected function reduceStockQuantities(Order $order): bool
    {
        try {
            DB::beginTransaction();

            foreach ($order->items as $item) {
                $book = Book::find($item->book_id);

                if (!$book || $book->stock < $item->quantity) {
                    DB::rollBack();
                    return false;
                }

                $book->stock -= $item->quantity;
                $book->save();
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Process a new order and reduce stock.
     * This method should be called when an order is being placed.
     */
    public function processOrder(Order $order): RedirectResponse
    {
        try {
            DB::beginTransaction();

            // Check if stock reduction is successful
            if (!$this->reduceStockQuantities($order)) {
                throw new Exception('Insufficient stock for one or more items in your order.');
            }

            // Mark the order as processed/confirmed
            $order->status = 'confirmed';
            $order->save();

            DB::commit();
            return redirect()->route('orders.show', $order)
                ->with('success', 'Order processed successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }
}
