<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    public $timestamps = true;
    public const UPDATED_AT = null;

    protected $fillable = [
        'product_id', 'user_id', 'type', 'quantity',
        'reference_type', 'reference_id', 'notes', 'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
