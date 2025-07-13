<?php

namespace App\Observers;

use App\Models\Notification;
use App\Models\SupportTicket;

class SupportTicketObserver
{
    public function updated(SupportTicket $supportTicket): void
    {
        
        if ($supportTicket->isDirty('status')) {
            $type = 'general';
            if ($supportTicket->status === 'resolved') {
                $type = 'support_ticket_resolved';
            }
            
            $this->createNotification(
                $supportTicket->client_id,
                $type,
                'Support Ticket Status Updated',
                'Your support ticket #' . $supportTicket->id . ' status has been changed to ' . $supportTicket->status . '.'
            );
        }

        if ($supportTicket->isDirty('priority')) {
            $this->createNotification(
                $supportTicket->client_id,
                'general',
                'Support Ticket Priority Updated',
                'Your support ticket #' . $supportTicket->id . ' priority has been changed to ' . $supportTicket->priority . '.'
            );
        }

        if ($supportTicket->isDirty('admin_notes') && !empty($supportTicket->admin_notes)) {
            $this->createNotification(
                $supportTicket->client_id,
                'general',
                'Support Ticket Updated',
                'Your support ticket #' . $supportTicket->id . ' has been updated. Please check for new information.'
            );
        }
    }

    protected function createNotification(string $clientId, string $type, string $title, string $message): void
    {
        Notification::create([
            'client_id' => $clientId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
        ]);
    }
}
