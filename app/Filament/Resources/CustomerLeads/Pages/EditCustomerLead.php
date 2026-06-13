<?php

namespace App\Filament\Resources\CustomerLeads\Pages;

use App\Filament\Resources\CustomerLeads\CustomerLeadResource;
use Filament\Resources\Pages\EditRecord;

class EditCustomerLead extends EditRecord
{
    protected static string $resource = CustomerLeadResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
