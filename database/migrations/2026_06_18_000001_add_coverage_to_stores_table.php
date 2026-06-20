<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            // Bounding box de cobertura del restaurante
            // Los 4 puntos definen el rectángulo de zona de entrega
            $table->decimal('store_bound_north', 10, 7)
                  ->nullable()
                  ->after('store_order_template_lang')
                  ->comment('Latitud máxima de cobertura (punto más al norte)');

            $table->decimal('store_bound_south', 10, 7)
                  ->nullable()
                  ->after('store_bound_north')
                  ->comment('Latitud mínima de cobertura (punto más al sur)');

            $table->decimal('store_bound_east', 10, 7)
                  ->nullable()
                  ->after('store_bound_south')
                  ->comment('Longitud máxima de cobertura (punto más al este)');

            $table->decimal('store_bound_west', 10, 7)
                  ->nullable()
                  ->after('store_bound_east')
                  ->comment('Longitud mínima de cobertura (punto más al oeste)');
        });
    }

    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn([
                'store_bound_north',
                'store_bound_south',
                'store_bound_east',
                'store_bound_west',
            ]);
        });
    }
};
