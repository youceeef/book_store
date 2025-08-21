<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'slug',
        'category_id',
        'author',
        'description',
        'isbn',
        'cover_image',
        'price',
        'stock'
    ];

    // A Book belongs to a Category
    public function category(): BelongsTo // Type hint
    {
        return $this->belongsTo(Category::class);
    }
    // public function getRouteKeyName(): string
    // {
    //     return 'slug';
    // }
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getInStockAttribute(): bool
    {
        return $this->stock > 0;
    }
}
