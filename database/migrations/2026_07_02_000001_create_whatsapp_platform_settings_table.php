<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_platform_settings', function (Blueprint $table) {
            $table->id();
            $table->string('ai_provider')->nullable();
            $table->string('ai_model')->nullable();
            $table->text('ai_api_key')->nullable();
            $table->text('wa_access_token')->nullable();
            $table->string('wa_phone_number_id')->nullable();
            $table->string('wa_business_account_id')->nullable();
            $table->text('wa_verify_token')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_platform_settings');
    }
};
