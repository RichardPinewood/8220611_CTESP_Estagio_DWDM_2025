<?php

namespace App\Models;

use App\Enums\UserType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'type',
        'admin_access_granted',
        'granted_by',
        'granted_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'status' => 'boolean',
        'type' => UserType::class,
        'admin_access_granted' => 'boolean',
        'granted_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = (string) \Illuminate\Support\Str::uuid();
            
            if (!$model->type) {
                $model->type = UserType::ADMIN;
            }
        });
    }

    public function grantedBy()
    {
        return $this->belongsTo(User::class, 'granted_by');
    }

    public function client()
    {
        return $this->hasOne(\App\Models\Client::class);
    }

    public function canAccessAdminPanel(): bool
    {
        // Admins and employees always have access
        if (in_array($this->type, [UserType::ADMIN, UserType::EMPLOYEE])) {
            return true;
        }

        // Clients need explicit permission
        return $this->type === UserType::CLIENT && $this->admin_access_granted;
    }

    public function grantAdminAccess(User $grantedBy): void
    {
        $this->update([
            'admin_access_granted' => true,
            'granted_by' => $grantedBy->id,
            'granted_at' => now(),
        ]);
    }

    public function revokeAdminAccess(): void
    {
        $this->update([
            'admin_access_granted' => false,
            'granted_by' => null,
            'granted_at' => null,
        ]);
    }
}