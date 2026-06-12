<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            // Snapshot del producto en el momento de la venta
            $table->string('product_name', 191)
                  ->nullable()
                  ->after('product_service_name')
                  ->comment('Nombre exacto del producto en el momento de la venta');
            $table->decimal('product_sale_price', 10, 2)
                  ->nullable()
                  ->after('product_name')
                  ->comment('Precio de venta del producto en el momento de la venta');
            $table->decimal('product_store_price', 10, 2)
                  ->nullable()
                  ->after('product_sale_price')
                  ->comment('Precio del restaurante en el momento de la venta');

            // Snapshot de extras
            $table->json('extras_detail')
                  ->nullable()
                  ->after('product_store_price')
                  ->comment('JSON con extras seleccionados y sus precios en el momento de la venta');
            $table->decimal('extras_sale_total', 10, 2)
                  ->nullable()
                  ->default(0)
                  ->after('extras_detail')
                  ->comment('Suma de precios de venta de los extras');
            $table->decimal('extras_store_total', 10, 2)
                  ->nullable()
                  ->default(0)
                  ->after('extras_sale_total')
                  ->comment('Suma de precios del restaurante de los extras');

            // Observaciones del cliente
            $table->text('comments')
                  ->nullable()
                  ->after('extras_store_total')
                  ->comment('Observaciones del cliente. Ej: Sin cebolla por favor');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn([
                'product_name',
                'product_sale_price',
                'product_store_price',
                'extras_detail',
                'extras_sale_total',
                'extras_store_total',
                'comments',
            ]);
        });
    }
};
