<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class Client extends Authenticatable implements FilamentUser, \Illuminate\Contracts\Auth\Authenticatable, \Illuminate\Contracts\Auth\Access\Authorizable
{
    use HasFactory, Notifiable, HasUuids, \Illuminate\Foundation\Auth\Access\Authorizable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'billing_name',
        'billing_address',
        'vat_number',
        'is_active',
        'additional_contacts',
    ];
    
    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'additional_contacts' => 'array',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active && $panel->getId() === 'client';
    }
    
    public function getAuthPasswordName()
    {
        return 'password';
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
    
    public function getAuthIdentifierName()
    {
        return 'id';
    }
    
    public function getAuthIdentifier()
    {
        return $this->getKey();
    }
    
    public function getAuthPassword()
    {
        return $this->password;
    }
    
    public function getRememberToken()
    {
        return $this->remember_token;
    }
    
    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }
    
    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    public function domains()
    {
        return $this->hasMany(Domain::class);
    }

    public function hostings()
    {
        return $this->hasMany(Hosting::class);
    }

    public function renewals()
    {
        $domainRenewals = $this->hasManyThrough(
            Renewal::class,
            Domain::class,
            'client_id',
            'renewable_id'
        )->where('renewable_type', Domain::class);
        
        $hostingRenewals = $this->hasManyThrough(
            Renewal::class,
            Hosting::class,
            'client_id',
            'renewable_id'
        )->where('renewable_type', Hosting::class);
        
        
        return $domainRenewals->getQuery()
            ->union($hostingRenewals->getQuery())
            ->orderBy('created_at', 'desc');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function supportTickets()
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function timeEntries()
    {
        return $this->hasManyThrough(TimeEntry::class, SupportTicket::class, 'client_id', 'ticket_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function unreadNotifications()
    {
        return $this->hasMany(Notification::class)->unread();
    }
}