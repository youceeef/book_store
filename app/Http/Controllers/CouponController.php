<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;
use Darryldecode\Cart\Facades\CartFacade as Cart; // Use Cart facade
use Illuminate\Http\RedirectResponse;

class CouponController extends Controller
{
    /**
     * Apply a coupon to the cart.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate(['coupon_code' => 'required|string']);
        $couponCode = $request->coupon_code;

        $coupon = Coupon::where('code', $couponCode)->first();

        // --- Validation ---
        if (!$coupon) {
            return back()->with('error', 'Invalid coupon code.');
        }

        if (!$coupon->isValid()) {
            return back()->with('error', 'Coupon is expired or has reached its usage limit.');
        }

        // Check minimum amount if set
        if ($coupon->min_amount && Cart::getSubTotal(false) < $coupon->min_amount) { // Use subtotal (excluding conditions like tax)
            return back()->with('error', "Cart total must be at least \${$coupon->min_amount} to use this coupon.");
        }
        // --- End Validation ---

        // Store coupon details in session
        session()->put('coupon', [
            'code' => $coupon->code,
            'type' => $coupon->type,
            'value' => $coupon->value,
            // Store calculated discount here for simplicity? Or recalculate each time?
            // 'discount' => $coupon->calculateDiscount(Cart::getSubTotal(false))
        ]);

        return back()->with('success', 'Coupon applied successfully!');
    }

    /**
     * Remove the applied coupon from the session.
     */
    public function destroy(): RedirectResponse
    {
        session()->forget('coupon');
        return back()->with('success', 'Coupon removed.');
    }
}
