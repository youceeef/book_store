<?php

namespace App\Http\Controllers\Admin; // Correct namespace

use App\Http\Controllers\Controller;
use App\Models\Order; // Import Order model
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Display a listing of all orders.
     */
    public function index(): View
    {
        // Fetch all orders, newest first, with user relationship and item count
        $orders = Order::with('user') // Eager load the user who placed the order
            ->withCount('items') // Eager load the count of items
            ->latest() // Order by creation date descending
            ->paginate(20); // Show more per page for admin

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Display the specified order details.
     */
    public function show(Order $order): View // Route model binding using ID
    {
        // Eager load relationships needed for the detail view
        $order->load([
            'user',          // Load the user details
            'items',         // Load the order items collection
            'items.book'     // For each item, load the associated book
        ]);

        return view('admin.orders.show', compact('order'));
    }

    // Add updateStatus() method later if needed
}
