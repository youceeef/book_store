<?php

// database/migrations/YYYY_MM_DD_HHMMSS_create_order_items_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade'); // Link to the parent order
            $table->foreignId('book_id')->constrained()->onDelete('cascade'); // Link to the specific book
            $table->integer('quantity');
            $table->decimal('price', 8, 2); // Price of the book AT THE TIME OF PURCHASE
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
