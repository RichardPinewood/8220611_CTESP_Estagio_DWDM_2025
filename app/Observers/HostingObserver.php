<?php

namespace App\Observers;

use App\Models\Hosting;
use App\Models\Notification;

class HostingObserver
{
    public function updated(Hosting $hosting): void
    {
        if ($hosting->isDirty('status')) {
            $type = 'hosting_' . $hosting->status;
            $this->createNotification(
                $hosting->client_id,
                $type,
                'Hosting Status Updated',
                'The status of your hosting account ' . $hosting->account_name . ' has been updated to ' . $hosting->status . '.'
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
