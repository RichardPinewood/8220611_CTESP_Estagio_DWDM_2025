<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'client_id',
        'subject',
        'description',
        'status',
        'priority',
        'service_type',
        'service_id',
        'admin_notes',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function timeEntries()
    {
        return $this->hasMany(TimeEntry::class, 'ticket_id');
    }

    public function service()
    {
        return match ($this->service_type) {
            'domain' => $this->belongsTo(Domain::class, 'service_id'),
            'hosting' => $this->belongsTo(Hosting::class, 'service_id'),
            default => null,
        };
    }

    public function getServiceNameAttribute()
    {
        return match ($this->service_type) {
            'domain' => $this->service?->name ?? 'Unknown Domain',
            'hosting' => $this->service?->account_name ?? 'Unknown Hosting',
            default => 'General Support',
        };
    }

    public function getTotalTimeSpentAttribute()
    {
        return $this->timeEntries()->sum('hours_spent');
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'open' => 'danger',
            'in_progress' => 'warning',
            'resolved' => 'success',
            'closed' => 'gray',
        };
    }

    public function getPriorityColorAttribute()
    {
        return match ($this->priority) {
            'low' => 'success',
            'medium' => 'warning',
            'high' => 'danger',
            'urgent' => 'danger',
        };
    }
}