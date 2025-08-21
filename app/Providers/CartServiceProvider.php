<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View; // Import View facade
use Darryldecode\Cart\Facades\CartFacade as Cart; // Import Cart facade

class CartServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share cart count with specific views or all views
        // Using '*' shares with all views, which is simple for now.
        // For optimization, you could specify layouts: ['layouts.app', 'layouts.guest']
        View::composer('*', function ($view) {
            $view->with('cartCountGlobal', Cart::getTotalQuantity());
        });
    }
}
