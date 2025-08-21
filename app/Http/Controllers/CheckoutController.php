<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Darryldecode\Cart\Facades\CartFacade as Cart; // Use Cart facade
use Stripe\Stripe; // Stripe PHP library (Cashier uses it internally)
use Laravel\Cashier\Exceptions\IncompletePayment; // Cashier exception for SCA
use App\Models\Order; // Eloquent models
use App\Models\Book;
use App\Models\Coupon; // Import Coupon model
use Exception; // Base PHP exception
use Illuminate\Support\Facades\Log; // Logging facade
use Illuminate\Support\Facades\DB; // Database facade (optional, if needed elsewhere)
use Illuminate\View\View; // For return type hinting
use Illuminate\Http\RedirectResponse; // For return type hinting

class CheckoutController extends Controller
{
    /**
     * Display the checkout page.
     * Calculates totals including potential coupon discount for display.
     * Creates a Stripe SetupIntent.
     */
    public function index(): View | RedirectResponse // Added RedirectResponse type hint
    {
        $user = Auth::user();
        $cartItems = Cart::getContent();

        // Redirect if cart is empty
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $cartSubtotal = Cart::getSubTotal(false); // Use subtotal before discount
        $discount = 0;
        $finalTotal = $cartSubtotal; // Start with subtotal
        $appliedCouponCode = null; // For display

        // Calculate potential discount for display purposes on the checkout page
        if (session()->has('coupon')) {
            $couponSessionData = session('coupon');
            $couponModel = Coupon::where('code', $couponSessionData['code'])->first();

            // Perform a basic validation check before displaying discount
            if ($couponModel && $couponModel->isValid() && (!$couponModel->min_amount || $cartSubtotal >= $couponModel->min_amount)) {
                $discount = $couponModel->calculateDiscount($cartSubtotal);
                $finalTotal = max(0, $cartSubtotal - $discount); // Ensure total isn't negative
                $appliedCouponCode = $couponModel->code;
                Log::info("Checkout Index: Displaying discount {$discount} for coupon {$appliedCouponCode}. Subtotal: {$cartSubtotal}, Final: {$finalTotal}");
            } else {
                // If coupon in session is invalid for current cart state, remove it
                Log::warning("Checkout Index: Removing invalid session coupon '{$couponSessionData['code']}'.");
                session()->forget('coupon');
                // No need to redirect here, just won't show discount
            }
        }

        // Ensure user is Stripe customer
        $user->createOrGetStripeCustomer();

        // Create SetupIntent for securely collecting payment method
        try {
            $intent = $user->createSetupIntent();
        } catch (Exception $e) {
            Log::error("Stripe Setup Intent creation failed for user {$user->id}: " . $e->getMessage());
            // Redirect back to cart with error if SetupIntent fails
            return redirect()->route('cart.index')->with('error', 'Could not initiate the secure payment process. Please try again.');
        }

        // Pass calculated totals, intent secret, etc., to the view
        return view('checkout.index', [
            'intentClientSecret' => $intent->client_secret,
            'cartTotal' => $finalTotal, // Pass the potentially discounted total
            'cartSubtotal' => $cartSubtotal,
            'discount' => $discount,
            'appliedCouponCode' => $appliedCouponCode, // Pass coupon code if applied
            'cartItems' => $cartItems,
            'stripeKey' => config('cashier.key')
        ]);
    }

    /**
     * Process the payment after Stripe confirmation on the frontend.
     * Re-validates coupon, calculates final amount, initiates charge.
     */
    public function process(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $paymentMethod = $request->input('payment_method');

        // Basic validation for payment method presence
        if (!$paymentMethod) {
            return redirect()->route('checkout.index')->with('error', 'Payment method details are missing.');
        }

        // --- Recalculate totals and RE-VALIDATE coupon ---
        $subtotal = Cart::getSubTotal(false); // Get subtotal without conditions
        $discount = 0;
        $couponCode = null;
        $couponModel = null; // Initialize coupon model variable

        Log::info("Checkout Process: Starting for User ID {$user->id}. Subtotal: {$subtotal}");

        if (session()->has('coupon')) {
            $couponSessionData = session('coupon');
            Log::info("Checkout Process: Found coupon in session: " . json_encode($couponSessionData));

            $couponModel = Coupon::where('code', $couponSessionData['code'])->first();

            // --- Coupon Validation Block ---
            $couponIsValid = false; // Flag to track validity
            if ($couponModel) {
                if (!$couponModel->isValid()) {
                    Log::warning("Checkout Process: Coupon code '{$couponModel->code}' is invalid (expired/limit reached).");
                    session()->forget('coupon');
                    return redirect()->route('cart.index')->with('error', 'The applied coupon has expired or reached its usage limit.'); // Redirect to cart
                } elseif ($couponModel->min_amount && $subtotal < $couponModel->min_amount) {
                    Log::warning("Checkout Process: Cart subtotal {$subtotal} is less than minimum amount {$couponModel->min_amount} for coupon '{$couponModel->code}'.");
                    session()->forget('coupon');
                    return redirect()->route('cart.index')->with('error', "Cart total does not meet the minimum amount (\${$couponModel->min_amount}) for the applied coupon."); // Redirect to cart
                } else {
                    // Coupon is valid for this checkout attempt
                    $couponIsValid = true;
                    $discount = $couponModel->calculateDiscount($subtotal);
                    $couponCode = $couponModel->code; // Store code for metadata/order record
                    Log::info("Checkout Process: Coupon '{$couponCode}' validated. Discount calculated: {$discount}");
                }
            } else {
                // Coupon code from session doesn't exist in DB anymore
                Log::warning("Checkout Process: Coupon code '{$couponSessionData['code']}' from session not found in DB.");
                session()->forget('coupon');
                return redirect()->route('cart.index')->with('error', 'The applied coupon is no longer valid.'); // Redirect to cart
            }
            // --- End Coupon Validation Block ---

        } else {
            Log::info("Checkout Process: No coupon found in session.");
        }

        // Calculate final total and amount in cents for Stripe charge
        $finalTotal = max(0, $subtotal - $discount); // Ensure it's not negative
        $amountInCents = round($finalTotal * 100);

        Log::info("Checkout Process: Final total: {$finalTotal}. Amount for Stripe (cents): {$amountInCents}");
        // --- End Calculation ---


        // --- Database/Stripe Operations ---
        try {
            // Ensure Stripe customer exists & update default payment method
            $user->createOrGetStripeCustomer();
            $user->updateDefaultPaymentMethod($paymentMethod);

            // Prepare metadata for Stripe charge
            $stripeMetadata = [
                'user_id' => $user->id,
                'cart_items' => json_encode(Cart::getContent()->toArray()), // Send cart contents
                'cart_total_items' => Cart::getTotalQuantity(),
                'coupon_code' => $couponCode, // Send coupon code used (or null)
                'subtotal' => $subtotal,     // Optional: store original subtotal
                'discount_applied' => $discount, // Optional: store discount amount
            ];
            Log::info("Checkout Process: Preparing Stripe charge with metadata: ", $stripeMetadata);


            // --- Attempt charge using the FINAL calculated amount ---
            $charge = $user->charge($amountInCents, $user->defaultPaymentMethod()->id, [
                'return_url' => route('checkout.success'),
                'description' => 'Lara Bookstore Order Payment for ' . $user->email,
                'metadata' => $stripeMetadata, // Pass prepared metadata
                'confirm' => true, // Attempt to confirm payment immediately
            ]);
            Log::info("Checkout Process: Stripe charge initiated successfully for User ID {$user->id}. Charge/PI ID: " . ($charge->id ?? 'N/A'));
            // --- End Charge ---


            // --- Clear Cart & Coupon AFTER successful charge initiation ---
            Cart::clear(); // Use the default instance clear
            session()->forget('coupon'); // Forget coupon after successful charge attempt
            Log::info("Checkout Process: Cart and session coupon cleared for User ID: {$user->id}.");
            // --- End Clearing ---

            // Redirect to success page OPTIMISTICALLY.
            // Webhook will handle the actual order creation, stock update, and coupon usage count increment.
            return redirect()->route('checkout.success')->with('success', 'Payment initiated! Your order is being processed.');
        } catch (IncompletePayment $exception) {
            // Handle SCA (3D Secure) redirects
            Log::warning("Checkout Process: Incomplete Payment for user {$user->id} - Requires Action. PaymentIntent ID: {$exception->payment->id}. Message: " . $exception->getMessage());
            return redirect()->route(
                'cashier.payment',
                [$exception->payment->id, 'redirect' => route('checkout.process')] // Redirect to Cashier's payment page
            );
        } catch (Exception $e) {
            // Handle other exceptions (card declines, API errors, etc.)
            Log::error("Checkout Process Error for user {$user->id}: " . $e->getMessage());
            // Optionally log stack trace: Log::error($e);
            $errorMessage = 'An error occurred during payment processing. Please try again or contact support.';
            // Provide more specific feedback if possible (e.g., from Stripe CardException)
            if ($e instanceof \Stripe\Exception\CardException) {
                $errorMessage = $e->getError()->message ?? $errorMessage;
            }
            return redirect()->route('checkout.index')->with('error', $errorMessage);
        }
    }

    /**
     * Display the success page after payment initiation.
     */
    public function success(): View | RedirectResponse
    {
        // Prevent direct access without successful redirection
        if (!session('success')) {
            return redirect()->route('home');
        }
        return view('checkout.success');
    }

    /**
     * Display the cancel page (Optional).
     */
    public function cancel(): View
    {
        return view('checkout.cancel');
    }
}
