<?php

namespace App\Http\Controllers\Admin; // Correct namespace

use App\Http\Controllers\Controller; // Base Controller
use Illuminate\Http\Request;
use Illuminate\View\View; // For return type hinting

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index(): View
    {
        // You can fetch some basic stats here later
        // $userCount = \App\Models\User::count();
        // $orderCount = \App\Models\Order::count();
        // $bookCount = \App\Models\Book::count();

        return view('admin.dashboard'); // We'll create this view next
        // ->with(compact('userCount', 'orderCount', 'bookCount'));
    }
}
