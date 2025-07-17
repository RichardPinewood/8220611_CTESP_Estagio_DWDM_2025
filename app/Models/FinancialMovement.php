<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialMovement extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'client_id',
        'invoice_id',
        'type',
        'amount',
        'description',
        'payment_method',
        'reference_number',
        'balance_after',
        'created_by',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePayments($query)
    {
        return $query->where('type', 'payment');
    }

    public function scopeAdjustments($query)
    {
        return $query->where('type', 'adjustment');
    }

    public function scopeCredits($query)
    {
        return $query->where('type', 'credit');
    }

    public function scopeRefunds($query)
    {
        return $query->where('type', 'refund');
    }

    public function getTypeColorAttribute()
    {
        return match ($this->type) {
            'payment' => 'success',
            'credit' => 'info',
            'adjustment' => 'warning',
            'refund' => 'danger',
            default => 'gray',
        };
    }

    public function getFormattedAmountAttribute()
    {
        $amount = (float) $this->amount;
        $prefix = $amount >= 0 ? '+' : '';
        return $prefix . '€' . number_format($amount, 2);
    }
}