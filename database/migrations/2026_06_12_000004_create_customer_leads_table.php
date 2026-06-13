<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_leads', function (Blueprint $table) {
            $table->id();

            $table->foreignId('store_id')
                  ->constrained('stores')
                  ->onDelete('cascade');

            $table->string('customer_phone', 20);
            $table->string('customer_name', 191)->nullable();

            // Timestamps de contacto
            $table->timestamp('first_contact_at')->nullable();
            $table->timestamp('last_contact_at')->nullable();

            // Origen del lead
            $table->enum('first_source', [
                'META_ADS',
                'WHATSAPP_DIRECT',
                'QR',
                'ORGANIC',
                'FACEBOOK_PAGE',
                'INSTAGRAM',
            ])->nullable()->comment('Canal de adquisición del lead');

            // Producto de interés
            $table->foreignId('first_product_id')
                  ->nullable()
                  ->constrained('products')
                  ->nullOnDelete()
                  ->comment('Primer producto consultado — producto del anuncio o primera consulta');

            $table->foreignId('last_product_id')
                  ->nullable()
                  ->constrained('products')
                  ->nullOnDelete()
                  ->comment('Último producto comprado — útil para remarketing');

            // Estado CRM
            $table->enum('status', [
                'NEW',
                'CONTACTED',
                'INTERESTED',
                'CUSTOMER',
                'REPEAT_CUSTOMER',
                'INACTIVE',
                'BLOCKED',
            ])->default('NEW');

            // Métricas de compra
            $table->unsignedInteger('total_orders')->default(0);
            $table->decimal('customer_lifetime_value', 14, 2)->default(0);
            $table->timestamp('last_order_at')->nullable();
            $table->timestamp('conversion_date')
                  ->nullable()
                  ->comment('Fecha del primer pedido — cuando el lead pasó a CUSTOMER');

            // Marketing (preparado, sin lógica aún)
            $table->boolean('marketing_opt_in')->default(false);
            $table->timestamp('last_campaign_contact_at')->nullable();

            // Meta Ads (preparado para integración futura)
            $table->string('campaign_id', 100)->nullable();
            $table->string('adset_id', 100)->nullable();
            $table->string('ad_id', 100)->nullable();

            // Notas manuales del operador
            $table->text('notes')->nullable();

            $table->timestamps();

            // Restricción de unicidad
            $table->unique(['store_id', 'customer_phone']);

            // Índices para consultas frecuentes
            $table->index('customer_phone');
            $table->index('status');
            $table->index(['store_id', 'status']);
            $table->index(['store_id', 'first_contact_at']);
            $table->index(['store_id', 'last_order_at']);
        });

        // FK en tabla leads (pedidos) — nullable para no romper registros existentes
        Schema::table('leads', function (Blueprint $table) {
            $table->foreignId('customer_lead_id')
                  ->nullable()
                  ->after('store_id')
                  ->constrained('customer_leads')
                  ->nullOnDelete()
                  ->comment('Referencia al lead CRM — FK real, nullable para compatibilidad');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['customer_lead_id']);
            $table->dropColumn('customer_lead_id');
        });

        Schema::dropIfExists('customer_leads');
    }
};
