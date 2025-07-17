<?php

namespace App\Filament\Client\Resources\DomainResource\Pages;

use App\Filament\Client\Resources\DomainResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDomains extends ListRecords
{
    protected static string $resource = DomainResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}