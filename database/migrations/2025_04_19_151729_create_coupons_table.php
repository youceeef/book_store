<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // The coupon code (e.g., SUMMER10)
            $table->enum('type', ['fixed', 'percent'])->comment('Discount type'); // Fixed amount or percentage
            $table->decimal('value', 8, 2); // Discount value (e.g., 10.00 for fixed, 10 for percent)
            $table->decimal('min_amount', 8, 2)->nullable(); // Minimum cart amount to use coupon
            $table->timestamp('expires_at')->nullable(); // Expiration date (null = never expires)
            $table->integer('usage_limit')->nullable(); // Max times coupon can be used overall (null = unlimited)
            $table->integer('usage_count')->default(0); // How many times it has been used
            // Optional: Add usage_limit_per_user if needed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
