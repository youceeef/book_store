<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon; // Import Carbon for date handling

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type',
        'value',
        'min_amount',
        'expires_at',
        'usage_limit',
        // 'usage_count' // Usually not fillable, managed internally
    ];

    protected $casts = [
        'expires_at' => 'datetime', // Cast expires_at to Carbon instance
        'value' => 'decimal:2',      // Ensure value is treated as decimal
        'min_amount' => 'decimal:2', // Ensure min_amount is treated as decimal
    ];

    /**
     * Calculate the discount amount for a given total.
     *
     * @param float $total
     * @return float
     */
    public function calculateDiscount(float $total): float
    {
        if ($this->type === 'fixed') {
            // Ensure discount doesn't exceed total
            return min($this->value, $total);
        } elseif ($this->type === 'percent') {
            return round(($this->value / 100) * $total, 2);
        } else {
            return 0; // Should not happen
        }
    }

    /**
     * Check if the coupon is still valid (not expired, within usage limits).
     *
     * @return bool
     */
    public function isValid(): bool
    {
        $isExpired = $this->expires_at && $this->expires_at->isPast();
        $usageLimitReached = $this->usage_limit !== null && $this->usage_count >= $this->usage_limit;

        return !$isExpired && !$usageLimitReached;
    }
}
