<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->string('store_whatsapp', 20)
                  ->nullable()
                  ->after('name')
                  ->comment('Número WhatsApp del restaurante (con código de país, ej: 573001234567)');

            $table->string('store_order_template', 100)
                  ->nullable()
                  ->after('store_whatsapp')
                  ->comment('Nombre de la plantilla Meta aprobada para notificar pedidos al restaurante');

            $table->string('store_order_template_lang', 10)
                  ->nullable()
                  ->default('es_CO')
                  ->after('store_order_template')
                  ->comment('Código de idioma de la plantilla Meta, ej: es_CO, en_US');
        });
    }

    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn([
                'store_whatsapp',
                'store_order_template',
                'store_order_template_lang',
            ]);
        });
    }
};
