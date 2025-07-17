<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Hosting extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'client_id',
        'account_name',
        'domain_id',
        'plan_id',
        'server_id',
        'starts_at',
        'expires_at',
        'status',
        'payment_status',
        'next_renewal_price',
        'notes',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'next_renewal_price' => 'decimal:2',
    ];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = (string) \Illuminate\Support\Str::uuid();
        });
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(HostingPlan::class);
    }

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }

    public function renewals(): MorphMany
    {
        return $this->morphMany(Renewal::class, 'renewable');
    }
}