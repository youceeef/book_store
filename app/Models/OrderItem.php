<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'book_id',
        'quantity',
        'price'
    ];

    // An OrderItem belongs to an Order
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // An OrderItem belongs to a Book
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
