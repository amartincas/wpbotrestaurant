<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'personality_type',
    'system_prompt',
    'ai_provider',
    'ai_model',
    'ai_api_key',
    'wa_access_token',
    'wa_phone_number_id',
    'wa_business_account_id',
    'wa_verify_token',
    'status',
    'store_whatsapp',
    'store_order_template',
    'store_order_template_lang',
])]
class Store extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'personality_type' => 'string',
            'ai_provider' => 'string',
            'ai_api_key' => 'encrypted',
            'wa_access_token' => 'encrypted',
            'wa_verify_token' => 'encrypted',
        ];
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }

    /**
     * Verifica si el store tiene configurada la notificación al restaurante.
     */
    public function hasRestaurantNotification(): bool
    {
        return !empty($this->store_whatsapp)
            && !empty($this->store_order_template);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isDemo(): bool
    {
        return $this->status === 'demo';
    }

    public function isInactive(): bool
    {
        return $this->status === 'inactive';
    }
}
