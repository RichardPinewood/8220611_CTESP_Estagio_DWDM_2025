<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeEntry extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'ticket_id',
        'admin_id',
        'hours_spent',
        'description',
        'work_date',
    ];

    protected $casts = [
        'hours_spent' => 'decimal:2',
        'work_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function ticket()
    {
        return $this->belongsTo(SupportTicket::class, 'ticket_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function getFormattedHoursAttribute()
    {
        return number_format((float)$this->hours_spent, 2) . 'h';
    }
}