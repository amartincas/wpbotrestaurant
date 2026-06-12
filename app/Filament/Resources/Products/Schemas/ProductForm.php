<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Facades\Auth;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('store_id')
                    ->relationship(
                        'store',
                        'name',
                        fn ($query) => $query
                            ->when(
                                !Auth::user()?->is_super_admin,
                                fn ($q) => $q->where('id', Auth::user()?->store_id)
                            )
                    )
                    ->required()
                    ->default(Auth::user()?->store_id),

                TextInput::make('id')
                    ->label('Product ID (for AI image references)')
                    ->disabled()
                    ->dehydrated(false)
                    ->formatStateUsing(fn ($record) => $record?->id ? "Use [IMG:{$record->id}] to show this product" : '(ID will be assigned after creation)')
                    ->helperText('Reference this ID in system prompts or AI responses to display product images')
                    ->columnSpanFull(),

                TextInput::make('name')
                    ->required(),

                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),

                TextInput::make('price')
                    ->label('Precio de Venta')
                    ->required()
                    ->numeric()
                    ->prefix('$')
                    ->helperText('Precio que se cobra al cliente'),

                TextInput::make('store_price')
                    ->label('Precio Restaurante')
                    ->numeric()
                    ->prefix('$')
                    ->helperText('Precio que se paga al restaurante por este producto'),

                ToggleButtons::make('type')
                    ->options([
                        'product' => 'Product',
                        'service' => 'Service',
                    ])
                    ->default('product')
                    ->required()
                    ->inline(),

                TextInput::make('stock')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->label(function (Get $get): string {
                        return $get('type') === 'service'
                            ? 'Availability (1 = Accepting Clients, 0 = Fully Booked)'
                            : 'Stock Quantity';
                    })
                    ->helperText(function (Get $get): string {
                        return $get('type') === 'service'
                            ? 'Enter 1 if accepting new clients, 0 if fully booked'
                            : 'Enter the number of units in stock';
                    }),

                Textarea::make('ai_sales_strategy')
                    ->label('AI Sales Strategy')
                    ->placeholder('How should the AI sell this product?...')
                    ->columnSpanFull(),

                Textarea::make('faq_context')
                    ->label('FAQ & Operational Context')
                    ->placeholder('Rules, cities served, employees, FAQs specific to this product...')
                    ->columnSpanFull(),

                TextInput::make('required_customer_info')
                    ->label('Required Lead Data')
                    ->placeholder('E.g., Full name, phone, delivery address...')
                    ->columnSpanFull(),

                // =====================================================
                // EXTRAS / ADICIONALES DEL PRODUCTO
                // =====================================================
                Repeater::make('extras')
                    ->label('Extras / Adicionales')
                    ->relationship('extras')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre')
                            ->placeholder('Ej: Guacamole extra')
                            ->required()
                            ->columnSpan(2),

                        Textarea::make('description')
                            ->label('Descripción')
                            ->placeholder('Ej: Porción adicional de guacamole fresco')
                            ->rows(2)
                            ->columnSpanFull(),

                        TextInput::make('sale_price')
                            ->label('Precio de Venta')
                            ->numeric()
                            ->prefix('$')
                            ->required()
                            ->helperText('Precio que se cobra al cliente'),

                        TextInput::make('restaurant_price')
                            ->label('Precio Restaurante')
                            ->numeric()
                            ->prefix('$')
                            ->required()
                            ->helperText('Precio que se paga al restaurante'),

                        Toggle::make('is_available')
                            ->label('Disponible')
                            ->default(true)
                            ->columnSpanFull(),

                        TextInput::make('sort_order')
                            ->label('Orden')
                            ->numeric()
                            ->default(0)
                            ->helperText('Orden de presentación (menor número = primero)'),
                    ])
                    ->columns(2)
                    ->addActionLabel('+ Agregar extra')
                    ->reorderable('sort_order')
                    ->collapsible()
                    ->columnSpanFull(),

                // === PRODUCT GALLERY ===
                FileUpload::make('images')
                    ->label('Product Images')
                    ->multiple()
                    ->reorderable()
                    ->directory('products')
                    ->disk('public')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->maxSize(5120)
                    ->helperText('Upload multiple images (JPG, PNG, WebP). The first image will be used as primary.')
                    ->columnSpanFull()
                    ->formatStateUsing(fn ($record) => $record?->images()->pluck('image_path')->toArray() ?? [])
                    ->dehydrated(false),
            ]);
    }
}
