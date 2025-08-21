<?php

use Illuminate\Support\Facades\Route;

// --- Public Controllers ---
use App\Http\Controllers\BookController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WebhookController; // <-- Ensure this import points to your controller
// --- Admin Controllers ---
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\BookController as AdminBookController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController; // Import
use App\Http\Controllers\CouponController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// --- Public Routes ---

Route::get('/', function () {
    return redirect()->route('books.index');
})->name('home');

// Public Book Listing & Detail (Using Slug for Detail)
Route::get('/books', [BookController::class, 'index'])->name('books.index');
Route::get('/books/{book:slug}', [BookController::class, 'show'])->name('books.show');
// --- Public Category Browsing ---
Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show'); // <-- Add this route

// Shopping Cart Routes (Generally accessible)
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');         // Show Cart
    Route::post('/add', [CartController::class, 'store'])->name('store');       // Add item to cart
    Route::patch('/update/{rowId}', [CartController::class, 'update'])->name('update');   // Update item quantity
    Route::delete('/remove/{rowId}', [CartController::class, 'destroy'])->name('destroy'); // Remove item
    Route::post('/clear', [CartController::class, 'clear'])->name('clear');       // Clear entire cart


});

Route::post('/coupon', [CouponController::class, 'store'])->name('coupon.store');
Route::delete('/coupon', [CouponController::class, 'destroy'])->name('coupon.destroy');
// --- Authenticated User Routes ---

Route::middleware(['auth', 'verified'])->group(function () { // 'verified' is optional, depends if you require email verification

    // User Dashboard (Default Breeze)
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // User Profile (Default Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Checkout Process
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel'); // Optional cancel page

    // User Order History
    Route::get('/my-orders', [OrderController::class, 'index'])->name('orders.index');       // List user's orders
    Route::get('/my-orders/{order}', [OrderController::class, 'show'])->name('orders.show'); // Show specific order details

});


// --- Admin Routes ---

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Admin Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Admin Book Management
    Route::resource('books', AdminBookController::class);

    // Admin Category Management (Excluding show route)
    Route::resource('categories', AdminCategoryController::class)->except(['show']);

    // --- Admin Order Management ---
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index'); // List all orders
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show'); // Show specific order

    // coupon management
    Route::resource('coupons', AdminCouponController::class)->except(['show']); // Exclude show

    // Future: Add routes for updating status if needed
    // Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');


});


// --- Stripe Webhook ---

// Note: Needs CSRF exemption in app/Http/Middleware/VerifyCsrfToken.php -> $except array
Route::post('/stripe/webhook', [App\Http\Controllers\WebhookController::class, 'handleWebhook'])->name('cashier.webhook');

// --- Breeze Authentication Routes ---

require __DIR__ . '/auth.php';
