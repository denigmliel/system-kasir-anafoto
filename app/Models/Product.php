<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'code', 'name', 'description',
        'unit', 'price', 'stock', 'image', 'is_active',
        'is_stock_unlimited',
    ];

    protected $attributes = [
        'is_stock_unlimited' => false,
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_stock_unlimited' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function units()
    {
        return $this->hasMany(ProductUnit::class)->orderByDesc('is_default')->orderBy('name');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function getDisplayStockAttribute(): string
    {
        return (string) $this->stock;
    }
}
