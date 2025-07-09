<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Server extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'ip_address',
        'provider',
        'location',
    ];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = (string) \Illuminate\Support\Str::uuid();
        });
    }

    public function domains(): HasMany
    {
        return $this->hasMany(Domain::class);
    }

    public function hostings(): HasMany
    {
        return $this->hasMany(Hosting::class);
    }
}
