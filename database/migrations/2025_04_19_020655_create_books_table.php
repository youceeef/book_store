<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('author')->nullable(); // Simplified: Author name directly on book
            $table->text('description')->nullable();
            $table->string('isbn')->unique()->nullable();
            $table->string('cover_image')->nullable(); // Path to the image file
            $table->decimal('price', 8, 2); // Example: 999999.99
            $table->integer('stock')->default(0); // Optional: track stock
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
