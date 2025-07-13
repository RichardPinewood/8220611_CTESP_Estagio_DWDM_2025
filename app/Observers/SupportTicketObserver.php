<?php

namespace App\Observers;

use App\Models\Notification;
use App\Models\SupportTicket;

class SupportTicketObserver
{
    public function updated(SupportTicket $supportTicket): void
    {
        if ($supportTicket->isDirty('status')) {
            $type = 'support_ticket_' . $supportTicket->status;
            $this->createNotification(
                $supportTicket->client_id,
                $type,
                'Support Ticket Updated',
                'The status of your support ticket #' . $supportTicket->id . ' has been updated to ' . $supportTicket->status . '.'
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
