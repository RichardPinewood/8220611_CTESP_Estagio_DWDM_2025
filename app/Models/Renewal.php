<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Renewal extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'renewable_id',
        'renewable_type',
        'renewed_at',
        'amount',
        'payment_method',
        'receipt_file',
        'internal_file',
    ];

    protected $casts = [
        'renewed_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = (string) \Illuminate\Support\Str::uuid();
        });
    }

    public function renewable(): MorphTo
    {
        return $this->morphTo();
    }
}