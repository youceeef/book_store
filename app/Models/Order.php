<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import
use Illuminate\Database\Eloquent\Relations\HasMany; // Import

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'total_amount',
        'stripe_payment_intent_id',
        'billing_address',
        'shipping_address'
    ];

    // An Order belongs to a User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // An Order has many OrderItems
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
