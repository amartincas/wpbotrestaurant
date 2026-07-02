<?php

namespace App\Filament\Resources\Stores\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class StoreWizardForm
{
    // Persona templates for system prompts
    private static array $personaTemplates = [
        'vendedor' => 'You are a friendly sales assistant for a WhatsApp Bot Store. Your role is to help customers find the perfect solution for their business needs. Be helpful, positive, and focused on selling products that solve their problems.',
        'soporte' => 'You are a professional customer support agent. Your role is to resolve customer issues quickly and efficiently. Be empathetic, thorough, and always aim to exceed expectations.',
        'asesor' => 'You are a helpful business advisor. Your role is to provide guidance and recommendations to help clients make informed decisions. Be knowledgeable, friendly, and focus on understanding their needs.',
    ];

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                // === AI PERSONA ===
                Select::make('personality_type')
                    ->label('Persona Type')
                    ->options([
                        'vendedor' => 'Sales Representative (Vendedor)',
                        'soporte' => 'Support Agent (Soporte)',
                        'asesor' => 'Business Advisor (Asesor)',
                    ])
                    ->required()
                    ->helperText('Select the personality that best matches your use case.')
                    ->reactive()
                    ->columnSpanFull(),

                Textarea::make('system_prompt')
                    ->label('System Prompt')
                    ->required()
                    ->rows(6)
                    ->helperText('This prompt defines how the AI behaves. It was auto-filled based on your persona selection.')
                    ->columnSpanFull()
                    ->default(function (Get $get) {
                        $personality = $get('personality_type');
                        return self::$personaTemplates[$personality] ?? '';
                    })
                    ->reactive(),

                // =====================================================
                // Estado del Store y Notificación al Restaurante
                // =====================================================
                \Filament\Forms\Components\Select::make('status')
                    ->label('Estado del Store')
                    ->options([
                        'active'   => '✅ Activo',
                        'inactive' => '⏸️ Inactivo',
                        'demo'     => '🎯 Demo',
                    ])
                    ->default('active')
                    ->required()
                    ->helperText('Demo: simulación completa sin persistir pedidos en BD')
                    ->columnSpanFull(),

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
                    ->helperText('Código de idioma BCP-47. Ej: es_CO, en_US, es_MX')
                    ->columnSpanFull(),

                // =====================================================
                // ZONA DE COBERTURA — Bounding Box
                // Los 4 puntos definen el rectángulo de entrega.
                // Obtener coordenadas desde Google Maps:
                // Clic derecho en el punto → "¿Qué hay aquí?"
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
