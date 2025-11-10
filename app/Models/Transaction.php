<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'code', 'user_id', 'transaction_date',
        'subtotal', 'total_amount',
        'payment_method', 'payment_amount', 'change_amount',
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
        'subtotal' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'payment_amount' => 'decimal:2',
        'change_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public static function generateCode(): string
    {
        $datePart = now()->format('Ymd');
        $prefix = 'TRX' . $datePart;

        $lastCode = static::where('code', 'like', $prefix . '%')
            ->orderByDesc('code')
            ->lockForUpdate()
            ->value('code');

        $lastNumber = $lastCode ? (int) substr($lastCode, -4) : 0;
        $nextNumber = $lastNumber + 1;

        return $prefix . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
