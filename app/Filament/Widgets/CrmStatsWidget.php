<?php

namespace App\Filament\Widgets;

use App\Enums\CustomerLeadStatus;
use App\Models\CustomerLead;
use App\Services\CustomerLeadService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CrmStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $storeId = Auth::user()?->store_id;
        $isSuperAdmin = Auth::user()?->is_super_admin;

        $query = CustomerLead::query();
        if (!$isSuperAdmin && $storeId) {
            $query->where('store_id', $storeId);
        }

        $total      = (clone $query)->count();
        $newToday   = (clone $query)->whereDate('first_contact_at', today())->count();
        $newWeek    = (clone $query)->where('first_contact_at', '>=', now()->startOfWeek())->count();
        $customers  = (clone $query)->where('status', CustomerLeadStatus::CUSTOMER->value)->count();
        $repeat     = (clone $query)->where('status', CustomerLeadStatus::REPEAT_CUSTOMER->value)->count();

        $conversionRate = $total > 0
            ? round((($customers + $repeat) / $total) * 100, 1)
            : 0;

        $totalRevenue = (clone $query)->sum('customer_lifetime_value');

        return [
            Stat::make('Total Leads', number_format($total))
                ->description('Leads capturados')
                ->icon('heroicon-o-user-group')
                ->color('gray'),

            Stat::make('Nuevos Hoy', $newToday)
                ->description("Esta semana: {$newWeek}")
                ->icon('heroicon-o-user-plus')
                ->color('info'),

            Stat::make('Clientes', $customers)
                ->description("Recurrentes: {$repeat}")
                ->icon('heroicon-o-shopping-bag')
                ->color('success'),

            Stat::make('Conversión', $conversionRate . '%')
                ->description('Lead → Cliente')
                ->icon('heroicon-o-arrow-trending-up')
                ->color($conversionRate >= 20 ? 'success' : 'warning'),

            Stat::make('Ingresos Totales', '$' . number_format($totalRevenue, 0, ',', '.'))
                ->description('Customer Lifetime Value acumulado')
                ->icon('heroicon-o-banknotes')
                ->color('primary'),
        ];
    }
}
