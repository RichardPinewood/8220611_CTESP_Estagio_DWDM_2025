<?php

namespace App\Observers;

use App\Models\Domain;
use App\Models\Notification;

class DomainObserver
{
    public function created(Domain $domain): void
    {
        $this->createNotification(
            $domain->client_id,
            'domain_created',
            'Domain Registered',
            'Your new domain ' . $domain->name . ' has been successfully registered.'
        );
    }

    public function updated(Domain $domain): void
    {
        if ($domain->isDirty('status')) {
            $this->createNotification(
                $domain->client_id,
                'domain_' . $domain->status,
                'Domain Status Updated',
                'The status of your domain ' . $domain->name . ' has been updated to ' . $domain->status . '.'
            );
        }

        if ($domain->isDirty('expires_at')) {
            $this->createNotification(
                $domain->client_id,
                'domain_renewed',
                'Domain Renewed',
                'Your domain ' . $domain->name . ' has been successfully renewed until ' . $domain->expires_at->format('Y-m-d') . '.'
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
