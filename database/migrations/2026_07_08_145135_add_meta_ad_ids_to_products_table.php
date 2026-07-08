<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->json('meta_ad_ids')
                  ->nullable()
                  ->after('required_customer_info')
                  ->comment('IDs de anuncios de Meta (Click-to-WhatsApp) que promocionan este producto — permite resolver la tienda por el anuncio en vez de solo por texto libre');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('meta_ad_ids');
        });
    }
};
