<?php

namespace App\Filament\Resources\Stores\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class StoreForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('name')
                    ->required(),

                Select::make('status')
                    ->label('Estado del Store')
                    ->options([
                        'active'   => '✅ Activo',
                        'inactive' => '⏸️ Inactivo',
                        'demo'     => '🎯 Demo',
                    ])
                    ->default('active')
                    ->required()
                    ->helperText('Demo: simulación completa sin persistir pedidos en BD'),
                Select::make('personality_type')
                    ->options(['vendedor' => 'Vendedor', 'soporte' => 'Soporte', 'asesor' => 'Asesor'])
                    ->required(),
                Textarea::make('system_prompt')
                    ->required()
                    ->columnSpanFull(),

                // =====================================================
                // SPRINT 1: Notificación de pedidos al restaurante
                // =====================================================
                TextInput::make('store_whatsapp')
                    ->label('WhatsApp del Restaurante')
                    ->placeholder('573001234567')
                    ->helperText('Número con código de país, sin espacios ni símbolos. Ej: 573001234567')
                    ->columnSpanFull(),
                TextInput::make('store_order_template')
                    ->label('Plantilla Meta para Pedidos')
                    ->placeholder('nuevo_pedido')
                    ->helperText('Nombre exacto de la plantilla aprobada en Meta Business Manager')
                    ->columnSpanFull(),
                TextInput::make('store_order_template_lang')
                    ->label('Idioma de la Plantilla')
                    ->placeholder('es_CO')
                    ->default('es_CO')
                    ->helperText('Código de idioma BCP-47. Ej: es_CO, en_US, es_MX'),

                // =====================================================
                // ZONA DE COBERTURA — Bounding Box
                // =====================================================
                TextInput::make('store_bound_north')
                    ->label('Norte (Latitud máxima)')
                    ->placeholder('3.4800')
                    ->helperText('Punto más al norte de la zona de cobertura')
                    ->numeric(),

                TextInput::make('store_bound_south')
                    ->label('Sur (Latitud mínima)')
                    ->placeholder('3.4600')
                    ->helperText('Punto más al sur de la zona de cobertura')
                    ->numeric(),

                TextInput::make('store_bound_east')
                    ->label('Este (Longitud máxima)')
                    ->placeholder('-76.510')
                    ->helperText('Punto más al este de la zona de cobertura')
                    ->numeric(),

                TextInput::make('store_bound_west')
                    ->label('Oeste (Longitud mínima)')
                    ->placeholder('-76.540')
                    ->helperText('Punto más al oeste de la zona de cobertura')
                    ->numeric(),
            ]);
    }
}