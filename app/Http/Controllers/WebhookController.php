<?php

namespace App\Http\Controllers;

// --- Laravel & Core Imports ---
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // For logging
use Illuminate\Support\Facades\DB; // For database transactions
use Symfony\Component\HttpFoundation\Response; // For returning HTTP responses
use Exception; // Base exception class

// --- Cashier & Stripe Imports ---
use Laravel\Cashier\Http\Controllers\WebhookController as CashierController; // Extend Cashier's controller
use Stripe\PaymentIntent; // Type hint for payload object clarity

// --- Application Model Imports ---
use App\Models\User;
use App\Models\Order;
use App\Models\Book;
use App\Models\OrderItem;
use App\Models\Coupon; // <-- Import Coupon model

class WebhookController extends CashierController
{
    /**
     * Handle successful payment intents.
     * Creates the order, items, updates stock, and increments coupon usage count.
     *
     * @param  array  $payload The Stripe event payload.
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handlePaymentIntentSucceeded(array $payload): Response
    {
        // --- Initialize Core Variables ---
        $paymentIntentData = $payload['data']['object'] ?? null; // Get PI data array
        $paymentIntentId = $paymentIntentData['id'] ?? 'N/A_PAYLOAD_ERROR'; // Get PI ID early
        $cartItemsCollection = null; // Initialize collection variable
        $couponModel = null; // Initialize coupon model variable
        $couponCode = null; // Initialize coupon code variable

        Log::info("Webhook: handlePaymentIntentSucceeded - Method Entered. PI ID: {$paymentIntentId}");

        // Check if we even got payment intent data
        if (!$paymentIntentData || $paymentIntentId === 'N/A_PAYLOAD_ERROR') {
            Log::error("Webhook Error: Invalid payload structure or missing PaymentIntent ID.");
            return new Response('Webhook Error: Invalid payload', 400); // Bad request
        }

        // --- Database Transaction ---
        DB::beginTransaction();
        Log::info("Webhook: [{$paymentIntentId}] - DB Transaction Started.");

        try {
            // --- Step 1: Find User ---
            Log::info("Webhook: [{$paymentIntentId}] - Attempting to find user...");
            $stripeCustomerId = $paymentIntentData['customer'] ?? null; // Use array access
            if (!$stripeCustomerId) {
                throw new Exception("No Customer ID found on PaymentIntent."); // Fail early
            }

            $user = User::where('stripe_id', $stripeCustomerId)->first();
            if (!$user) {
                throw new Exception("User not found for Stripe Customer ID: {$stripeCustomerId}"); // Fail early
            }
            Log::info("Webhook: [{$paymentIntentId}] - Found User ID: {$user->id}");

            // --- Step 2: Check for Existing Order (Idempotency) ---
            Log::info("Webhook: [{$paymentIntentId}] - Checking for existing order...");
            $existingOrder = Order::where('stripe_payment_intent_id', $paymentIntentId)->first();
            if ($existingOrder) {
                Log::warning("Webhook Warning: [{$paymentIntentId}] - Order ID {$existingOrder->id} already exists. Skipping creation.");
                DB::commit(); // Commit empty transaction
                Log::info("Webhook: [{$paymentIntentId}] - DB Transaction Committed (Skipped Duplicate).");
                return $this->successMethod(); // Acknowledge successfully.
            }
            Log::info("Webhook: [{$paymentIntentId}] - No existing order found.");

            // --- Step 3: Retrieve and Validate Cart Items from Metadata ---
            Log::info("Webhook: [{$paymentIntentId}] - Attempting to retrieve cart items from metadata...");
            $metadata = $paymentIntentData['metadata'] ?? []; // Default to empty array if no metadata

            if (!isset($metadata['cart_items'])) {
                Log::error("Webhook Error: [{$paymentIntentId}] - Metadata key 'cart_items' is MISSING.");
                throw new Exception("Missing cart_items in metadata.");
            }

            $cartJson = $metadata['cart_items'];
            Log::info("Webhook: [{$paymentIntentId}] - Raw 'cart_items' metadata: " . $cartJson);

            // Decode JSON (will throw JsonException on failure)
            $decodedItems = json_decode($cartJson, true, 512, JSON_THROW_ON_ERROR);
            Log::info("Webhook: [{$paymentIntentId}] - Metadata decoded successfully.");

            if (!is_array($decodedItems) || empty($decodedItems)) {
                Log::error("Webhook Error: [{$paymentIntentId}] - Decoded cart_items is not a valid non-empty array.");
                throw new Exception("Decoded cart_items is not valid or empty.");
            }

            // Validate individual items and build the collection
            $validItems = [];
            foreach ($decodedItems as $rowId => $itemData) {
                if (isset($itemData['id']) && isset($itemData['quantity']) && isset($itemData['price']) && intval($itemData['quantity']) > 0) {
                    $validItems[$rowId] = $itemData;
                } else {
                    Log::warning("Webhook Warning: [{$paymentIntentId}] - Skipping item with invalid structure or zero quantity.", ['item_row_id' => $rowId, 'item_data' => $itemData]);
                }
            }

            if (empty($validItems)) {
                Log::error("Webhook Error: [{$paymentIntentId}] - No valid items found after structure validation.");
                throw new Exception("No valid items found in cart_items metadata.");
            }

            $cartItemsCollection = collect($validItems); // Assign ONLY if valid items exist
            Log::info("Webhook: [{$paymentIntentId}] - Validated cart items retrieved. Count: " . $cartItemsCollection->count());

            // --- Step 3.5: Find Coupon (if used) --- <<<< INSERTED BLOCK >>>>
            Log::info("Webhook: [{$paymentIntentId}] - Checking for coupon code in metadata...");
            $couponCode = $metadata['coupon_code'] ?? null; // Get code from metadata

            if ($couponCode) {
                Log::info("Webhook: [{$paymentIntentId}] - Coupon code '{$couponCode}' found in metadata. Attempting to find Coupon model...");
                $couponModel = Coupon::where('code', $couponCode)->first(); // Find the coupon
                if (!$couponModel) {
                    Log::warning("Webhook Warning: [{$paymentIntentId}] - Coupon code '{$couponCode}' from metadata not found in database. Proceeding without incrementing usage count.");
                    $couponModel = null; // Ensure it's null if not found
                    $couponCode = null; // Clear code if model not found
                } else {
                    Log::info("Webhook: [{$paymentIntentId}] - Found Coupon ID: {$couponModel->id} for code '{$couponCode}'.");
                    // Optional: Re-verify isValid() here?
                    // if (!$couponModel->isValid()) { ... log warning, set $couponModel = null ... }
                }
            } else {
                Log::info("Webhook: [{$paymentIntentId}] - No coupon code found in metadata.");
            }
            // --- End Find Coupon Block ---

            // --- Step 4: Create the Order Record ---
            Log::info("Webhook: [{$paymentIntentId}] - Attempting to create Order record...");
            $totalAmount = ($paymentIntentData['amount_received'] ?? 0) / 100;
            $discountAmount = $metadata['discount_applied'] ?? 0; // Get discount from metadata

            $order = Order::create([
                'user_id' => $user->id,
                'status' => 'paid',
                'total_amount' => $totalAmount, // This is the final amount charged by Stripe
                'stripe_payment_intent_id' => $paymentIntentId,
                // --- Store coupon info if available --- <<<< MODIFIED BLOCK >>>>
                'coupon_code' => $couponCode, // Store the validated code (or null)
                'discount_amount' => $discountAmount, // Store discount amount if you added the column
            ]);
            Log::info("Webhook: [{$paymentIntentId}] - Created Order ID: {$order->id} (Coupon: {$couponCode}, Discount: {$discountAmount})");

            // --- Step 5: Process Each Order Item ---
            Log::info("Webhook: [{$paymentIntentId}] - Processing order items loop (Order ID: {$order->id})...");
            foreach ($cartItemsCollection as $rowId => $cartItem) {
                $bookId = $cartItem['id'];
                $quantity = intval($cartItem['quantity']);
                $price = $cartItem['price'];

                Log::info("Webhook: [{$paymentIntentId}] - Processing Item - Book ID: {$bookId}, Qty: {$quantity}");

                $book = Book::find($bookId);
                if (!$book) {
                    throw new Exception("Book ID {$bookId} not found during order processing (Order ID: {$order->id}).");
                }
                Log::info("Webhook: [{$paymentIntentId}] - Found Book: {$book->title}, Current Stock: {$book->stock}");

                if ($book->stock < $quantity) {
                    throw new Exception("Insufficient stock for Book ID {$bookId} (Order ID: {$order->id}). Required: {$quantity}, Available: {$book->stock}");
                }
                Log::info("Webhook: [{$paymentIntentId}] - Stock check passed for Book ID: {$bookId}.");

                Log::info("Webhook: [{$paymentIntentId}] - Creating OrderItem for Book ID: {$bookId}...");
                $order->items()->create([
                    'book_id' => $bookId,
                    'quantity' => $quantity,
                    'price' => $price,
                ]);
                Log::info("Webhook: [{$paymentIntentId}] - Created OrderItem successfully.");

                Log::info("Webhook: [{$paymentIntentId}] - Updating stock for Book ID: {$bookId}...");
                $newStock = max(0, $book->stock - $quantity);
                $book->update(['stock' => $newStock]);
                Log::info("Webhook: [{$paymentIntentId}] - Updated stock for Book ID {$bookId} to {$newStock}.");
            }
            Log::info("Webhook: [{$paymentIntentId}] - Finished processing order items loop.");


            // --- Step 5.5: Increment Coupon Usage Count (If Applicable) --- <<<< INSERTED BLOCK >>>>
            // --- Do this AFTER order items are processed but BEFORE commit ---
            if ($couponModel) { // Check if we successfully found a valid coupon earlier ($couponModel from Step 3.5)
                Log::info("Webhook: [{$paymentIntentId}] - Attempting to increment usage count for Coupon ID: {$couponModel->id} (Code: {$couponCode})...");
                try {
                    $couponModel->increment('usage_count'); // Atomically increments the count
                    Log::info("Webhook: [{$paymentIntentId}] - Incremented usage count for Coupon ID: {$couponModel->id} successfully.");
                } catch (Exception $couponEx) {
                    Log::critical("Webhook CRITICAL: [{$paymentIntentId}] - FAILED to increment usage count for Coupon ID: {$couponModel->id}. Error: " . $couponEx->getMessage());
                    throw new Exception("Failed to increment coupon usage count."); // Cause rollback
                }
            } else {
                Log::info("Webhook: [{$paymentIntentId}] - No valid coupon model found, skipping usage count increment.");
            }
            // --- End Increment Coupon Block ---


            // --- Step 6: Commit Transaction ---
            Log::info("Webhook: [{$paymentIntentId}] - Attempting to commit DB transaction...");
            DB::commit();
            Log::info("Webhook: [{$paymentIntentId}] - DB Transaction Committed SUCCESSFULLY!");

            // --- Step 7 & 8 (Post-Commit Actions & Success Response) ---
            Log::info("Webhook: [{$paymentIntentId}] - Order creation process complete.");
            return $this->successMethod("Webhook Handled: Order {$order->id} Created");
        } catch (Exception $e) {
            // --- Exception Handling ---
            Log::error("Webhook Processing FAILED for PaymentIntent {$paymentIntentId}: " . $e->getMessage());
            Log::error("Webhook Trace: " . $e->getTraceAsString());

            Log::info("Webhook: [{$paymentIntentId}] - Exception caught, attempting to rollback DB transaction...");
            DB::rollBack();
            Log::warning("Webhook: [{$paymentIntentId}] - DB Transaction Rolled Back.");

            return new Response('Webhook Error: Failed to process payment_intent.succeeded', 500);
        }
    }

    // --- Other Handlers and Helper Methods ---

    /** Handle failed payment intents */
    public function handlePaymentIntentPaymentFailed(array $payload): Response
    {
        $paymentIntentData = $payload['data']['object'] ?? null;
        $paymentIntentId = $paymentIntentData['id'] ?? 'N/A_PAYLOAD_ERROR';
        Log::warning("Webhook: handlePaymentIntentPaymentFailed received for PI: {$paymentIntentId}");

        if ($paymentIntentData) {
            $order = Order::where('stripe_payment_intent_id', $paymentIntentId)->first();
            if ($order && $order->status !== 'failed') {
                $order->update(['status' => 'failed']);
                Log::info("Webhook: Marked Order ID {$order->id} as failed due to PI {$paymentIntentId} failure.");
            } else {
                Log::info("Webhook: Received PI failure for {$paymentIntentId}, but no corresponding order found or already marked failed.");
            }
        }
        return $this->successMethod('Webhook Handled Payment Intent Failure');
    }

    /** Return a successful response */
    protected function successMethod($message = 'Webhook Handled'): Response
    {
        Log::info("Webhook: Returning 200 OK Response. Message: '{$message}'");
        return new Response($message, 200);
    }

    /** Handle missing user */
    protected function missingUser(array $payload): Response
    {
        $stripeCustomerId = $payload['data']['object']['customer'] ?? 'N/A';
        Log::error('Webhook skipped: User not found.', ['stripe_customer' => $stripeCustomerId]);
        return new Response('Webhook Handled: User missing', 200);
    }
}
