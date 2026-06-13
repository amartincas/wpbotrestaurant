<?php

namespace App\Filament\Resources\CustomerLeads\Pages;

use App\Filament\Resources\CustomerLeads\CustomerLeadResource;
use App\Services\CustomerLeadService;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListCustomerLeads extends ListRecords
{
    protected static string $resource = CustomerLeadResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\CrmStatsWidget::class,
        ];
    }
}
