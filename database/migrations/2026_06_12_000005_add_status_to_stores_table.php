<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->enum('status', ['active', 'inactive', 'demo'])
                  ->default('active')
                  ->after('name')
                  ->comment('active: operación normal | inactive: ignorado en consultas | demo: simulación sin persistir datos');
        });
    }

    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
