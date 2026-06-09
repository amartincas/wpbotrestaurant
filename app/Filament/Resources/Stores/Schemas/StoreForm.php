<?php

namespace App\Filament\Resources\Stores\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;

class StoreForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('name')
                    ->required(),
                Select::make('personality_type')
                    ->options(['vendedor' => 'Vendedor', 'soporte' => 'Soporte', 'asesor' => 'Asesor'])
                    ->required(),
                Textarea::make('system_prompt')
                    ->required()
                    ->columnSpanFull(),
                Select::make('ai_provider')
                    ->options(['openai' => 'Openai', 'grok' => 'Grok', 'gemini' => 'Gemini'])
                    ->required()
                    ->rule('in:openai,grok,gemini')
                    ->reactive(),
                Select::make('ai_model')
                    ->label('AI Model')
                    ->required()
                    ->rule('string')
                    ->options(function (Get $get) {
                        $provider = $get('ai_provider');
                        if (!$provider) {
                            return [];
                        }

                        $models = config("ai.models.{$provider}", []);
                        return array_combine($models, $models);
                    })
                    ->reactive(),
                TextInput::make('ai_api_key')
                    ->label('AI API Key')
                    ->password()
                    ->revealable()
                    ->required()
                    ->rule('string')
                    ->rule('min:20')
                    ->columnSpanFull()
                    ->helperText('API key for the selected AI provider (encrypted). Must be at least 20 characters'),
                TextInput::make('wa_phone_number_id')
                    ->label('Phone Number ID')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('wa_business_account_id')
                    ->label('WABA ID (Business Account)')
                    ->columnSpanFull(),
                TextInput::make('wa_access_token')
                    ->label('Access Token')
                    ->password()
                    ->revealable()
                    ->required()
                    ->columnSpanFull()
                    ->helperText('WhatsApp Business API access token from Meta'),
                TextInput::make('wa_verify_token')
                    ->label('Verify Token')
                    ->required()
                    ->columnSpanFull()
                    ->helperText('Verify token for webhook setup'),

                // =====================================================
                // SECCIÓN: Notificación de pedidos al restaurante
                // Sprint 1 - wpbotrestaurant
                // =====================================================
                Section::make('Notificación de Pedidos al Restaurante')
                    ->description('Configura el envío automático de pedidos al WhatsApp del restaurante cuando se confirme un lead.')
                    ->schema([
                        TextInput::make('store_whatsapp')
                            ->label('WhatsApp del Restaurante')
                            ->placeholder('573001234567')
                            ->helperText('Número con código de país, sin espacios ni símbolos. Ej: 573001234567')
                            ->rule('nullable')
                            ->rule('regex:/^[0-9]{10,15}$/')
                            ->columnSpanFull(),
                        TextInput::make('store_order_template')
                            ->label('Plantilla Meta para Pedidos')
                            ->placeholder('nuevo_pedido')
                            ->helperText('Nombre exacto de la plantilla aprobada en Meta Business Manager')
                            ->rule('nullable')
                            ->columnSpanFull(),
                        TextInput::make('store_order_template_lang')
                            ->label('Idioma de la Plantilla')
                            ->placeholder('es_CO')
                            ->default('es_CO')
                            ->helperText('Código de idioma BCP-47. Ej: es_CO, en_US, es_MX')
                            ->rule('nullable'),
                    ])
                    ->columnSpanFull()
                    ->collapsible(),
            ]);
    }
}
