<?php

namespace App\Filament\Resources\CustomerLeads\Schemas;

use App\Enums\CustomerLeadSource;
use App\Enums\CustomerLeadStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CustomerLeadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('customer_name')
                    ->label('Nombre')
                    ->maxLength(191),

                TextInput::make('customer_phone')
                    ->label('Teléfono')
                    ->required()
                    ->disabled()
                    ->dehydrated(false),

                Select::make('status')
                    ->label('Estado')
                    ->options(CustomerLeadStatus::options())
                    ->required(),

                Select::make('first_source')
                    ->label('Origen')
                    ->options(CustomerLeadSource::options())
                    ->nullable(),

                TextInput::make('total_orders')
                    ->label('Total Pedidos')
                    ->numeric()
                    ->disabled()
                    ->dehydrated(false),

                TextInput::make('customer_lifetime_value')
                    ->label('Customer Lifetime Value')
                    ->numeric()
                    ->prefix('$')
                    ->disabled()
                    ->dehydrated(false),

                DateTimePicker::make('first_contact_at')
                    ->label('Primer Contacto')
                    ->disabled()
                    ->dehydrated(false)
                    ->timezone('America/Bogota'),

                DateTimePicker::make('conversion_date')
                    ->label('Fecha de Conversión')
                    ->disabled()
                    ->dehydrated(false)
                    ->timezone('America/Bogota'),

                DateTimePicker::make('last_order_at')
                    ->label('Última Compra')
                    ->disabled()
                    ->dehydrated(false)
                    ->timezone('America/Bogota'),

                Toggle::make('marketing_opt_in')
                    ->label('Acepta Marketing')
                    ->helperText('Preparado para futuras campañas — sin lógica activa aún'),

                Textarea::make('notes')
                    ->label('Notas')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
