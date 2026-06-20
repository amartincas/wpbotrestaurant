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
    'store_bound_north',
    'store_bound_south',
    'store_bound_east',
    'store_bound_west',
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

    /**
     * Verifica si el store tiene zona de cobertura configurada.
     */
    public function hasCoverage(): bool
    {
        return !empty($this->store_bound_north)
            && !empty($this->store_bound_south)
            && !empty($this->store_bound_east)
            && !empty($this->store_bound_west);
    }

    /**
     * Valida si unas coordenadas están dentro del bounding box.
     * Retorna true si está dentro, false si está fuera.
     * Retorna null si el store no tiene cobertura configurada.
     */
    public function isWithinCoverage(float $lat, float $lng): ?bool
    {
        if (!$this->hasCoverage()) {
            return null; // Sin configuración → no validar
        }

        return $lat >= (float) $this->store_bound_south
            && $lat <= (float) $this->store_bound_north
            && $lng >= (float) $this->store_bound_west
            && $lng <= (float) $this->store_bound_east;
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
