<?php

namespace App\Models;

use App\Enums\CustomerLeadSource;
use App\Enums\CustomerLeadStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'store_id',
    'customer_phone',
    'customer_name',
    'first_contact_at',
    'last_contact_at',
    'first_source',
    'first_product_id',
    'last_product_id',
    'status',
    'total_orders',
    'customer_lifetime_value',
    'last_order_at',
    'conversion_date',
    'marketing_opt_in',
    'last_campaign_contact_at',
    'campaign_id',
    'adset_id',
    'ad_id',
    'notes',
])]
class CustomerLead extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'first_contact_at'        => 'datetime',
            'last_contact_at'         => 'datetime',
            'last_order_at'           => 'datetime',
            'conversion_date'         => 'datetime',
            'last_campaign_contact_at'=> 'datetime',
            'marketing_opt_in'        => 'boolean',
            'customer_lifetime_value' => 'decimal:2',
            'status'                  => CustomerLeadStatus::class,
            'first_source'            => CustomerLeadSource::class,
        ];
    }

    // =========================================================
    // Relaciones
    // =========================================================

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function firstProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'first_product_id');
    }

    public function lastProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'last_product_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Lead::class, 'customer_lead_id');
    }

    // =========================================================
    // Helpers
    // =========================================================

    public function isCustomer(): bool
    {
        return in_array($this->status, [
            CustomerLeadStatus::CUSTOMER,
            CustomerLeadStatus::REPEAT_CUSTOMER,
        ]);
    }

    public function hasConverted(): bool
    {
        return $this->total_orders > 0;
    }

    /**
     * Calcula la tasa de conversión de leads a clientes para un store.
     */
    public static function conversionRate(int $storeId): float
    {
        $total = static::where('store_id', $storeId)->count();
        if ($total === 0) return 0;

        $converted = static::where('store_id', $storeId)
            ->whereIn('status', [
                CustomerLeadStatus::CUSTOMER->value,
                CustomerLeadStatus::REPEAT_CUSTOMER->value,
            ])
            ->count();

        return round(($converted / $total) * 100, 1);
    }
}
