<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'store_id',
    'customer_phone',
    'customer_name',
    'delivery_address_or_location',
    'location',
    'product_service_name',
    'product_name',
    'product_sale_price',
    'product_store_price',
    'extras_detail',
    'extras_sale_total',
    'extras_store_total',
    'comments',
    'total_amount',
    'status',
    'summary',
    'is_processed',
    'bot_active',
])]
class Lead extends Model
{
    use HasFactory;

    const STATUS_PENDIENTE   = 'pendiente';
    const STATUS_ACEPTADO    = 'aceptado';
    const STATUS_LISTO       = 'listo';
    const STATUS_DESPACHADO  = 'despachado';
    const STATUS_ENTREGADO   = 'entregado';
    const STATUS_CANCELADO   = 'cancelado';

    const STATUS_MAP = [
        'aceptado'   => self::STATUS_ACEPTADO,
        'accepted'   => self::STATUS_ACEPTADO,
        'listo'      => self::STATUS_LISTO,
        'ready'      => self::STATUS_LISTO,
        'despachado' => self::STATUS_DESPACHADO,
        'shipped'    => self::STATUS_DESPACHADO,
        'entregado'  => self::STATUS_ENTREGADO,
        'delivered'  => self::STATUS_ENTREGADO,
        'cancelado'  => self::STATUS_CANCELADO,
        'cancelled'  => self::STATUS_CANCELADO,
        'canceled'   => self::STATUS_CANCELADO,
    ];

    const STATUS_MESSAGES = [
        self::STATUS_ACEPTADO   => '✅ ¡Buenas noticias! El restaurante recibió tu pedido y ya inició la preparación. Te avisamos cuando esté listo.',
        self::STATUS_LISTO      => '📦 ¡Tu pedido está listo! Ya salió para entrega.',
        self::STATUS_DESPACHADO => '🚚 Tu pedido ha sido despachado y está en camino.',
        self::STATUS_ENTREGADO  => '🎉 ¡Tu pedido fue entregado! Gracias por tu compra. ¡Que lo disfrutes!',
        self::STATUS_CANCELADO  => '❌ Tu pedido fue cancelado. Si tienes dudas, escríbenos y te ayudamos.',
    ];

    protected function casts(): array
    {
        return [
            'is_processed'       => 'boolean',
            'bot_active'         => 'boolean',
            'extras_detail'      => 'array',
            'product_sale_price' => 'decimal:2',
            'product_store_price'=> 'decimal:2',
            'extras_sale_total'  => 'decimal:2',
            'extras_store_total' => 'decimal:2',
        ];
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function markAsProcessed(): void
    {
        $this->update(['is_processed' => true]);
    }

    public function isProcessed(): bool
    {
        return $this->is_processed === true;
    }

    public static function unprocessed($storeId)
    {
        return static::where('store_id', $storeId)
            ->where('is_processed', false)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public static function resolveStatus(string $text): ?string
    {
        $normalized = strtolower(trim($text));
        return self::STATUS_MAP[$normalized] ?? null;
    }

    public static function statusMessage(string $status): ?string
    {
        return self::STATUS_MESSAGES[$status] ?? null;
    }

    /**
     * Calcula el margen bruto de este lead.
     * Margen = total_amount - product_store_price - extras_store_total
     */
    public function getMargin(): float
    {
        $total       = (float) preg_replace('/[^0-9.]/', '', $this->total_amount ?? '0');
        $storeCost   = (float) ($this->product_store_price ?? 0);
        $extrasCost  = (float) ($this->extras_store_total ?? 0);
        return $total - $storeCost - $extrasCost;
    }
}
