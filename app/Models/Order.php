<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

 protected $fillable = [
    'order_number',
    'customer_id',
    'product_id',
    'quantity',
    'total_amount',
    'status',
    'order_date',
    'note',
    'payment_method',
    'payment_notes',
];

    protected $casts = [
        'quantity' => 'integer',
        'total_amount' => 'decimal:2',
        'order_date' => 'datetime',
    ];

    // Relationships

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Accessors

    public function getStatusTextAttribute()
    {
        return match ($this->status) {
            'pending' => 'Pending',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            default => 'Unknown',
        };
    }

    public function getFormattedTotalAttribute()
    {
        $amount = $this->total_amount ?? 0;
        return '$' . number_format($amount, 2);
    }

    // Scopes (example: filter orders by status)

    public function scopeStatus($query, $status)
    {
        return $query->when($status, fn($q) => $q->where('status', $status));
    }
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }


}
