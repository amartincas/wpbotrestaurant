<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->migrateCredentialsToSingleton();

        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn([
                'ai_provider',
                'ai_model',
                'ai_api_key',
                'wa_access_token',
                'wa_phone_number_id',
                'wa_business_account_id',
                'wa_verify_token',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->string('ai_provider')->nullable();
            $table->string('ai_model')->nullable();
            $table->text('ai_api_key')->nullable();
            $table->text('wa_access_token')->nullable();
            $table->string('wa_phone_number_id')->nullable();
            $table->string('wa_business_account_id')->nullable();
            $table->text('wa_verify_token')->nullable();
        });

        Log::warning('Rollback of drop_ai_and_wa_fields_from_stores_table only restores schema, not data — original per-store credentials are not recoverable from whatsapp_platform_settings.');
    }

    /**
     * Copy the current best-candidate store's WhatsApp/AI credentials into the
     * new global singleton before the source columns are dropped. Idempotent —
     * a no-op if whatsapp_platform_settings already has a token.
     */
    private function migrateCredentialsToSingleton(): void
    {
        $settings = DB::table('whatsapp_platform_settings')->first();

        if ($settings && !empty($settings->wa_access_token)) {
            return;
        }

        $candidate = DB::table('stores')
            ->where('status', 'active')
            ->whereNotNull('wa_access_token')
            ->orderBy('id')
            ->first();

        $candidate ??= DB::table('stores')
            ->whereNotNull('wa_access_token')
            ->orderBy('id')
            ->first();

        if (!$candidate) {
            return;
        }

        DB::table('whatsapp_platform_settings')->updateOrInsert(['id' => 1], [
            'ai_provider' => $candidate->ai_provider,
            'ai_model' => $candidate->ai_model,
            'ai_api_key' => $candidate->ai_api_key,
            'wa_access_token' => $candidate->wa_access_token,
            'wa_phone_number_id' => $candidate->wa_phone_number_id,
            'wa_business_account_id' => $candidate->wa_business_account_id,
            'wa_verify_token' => $candidate->wa_verify_token,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $others = DB::table('stores')
            ->where('id', '!=', $candidate->id)
            ->whereNotNull('wa_access_token')
            ->pluck('name', 'id');

        if ($others->isNotEmpty()) {
            Log::warning('Store credential migration: multiple stores had WhatsApp/AI credentials; only one survives as the global setting. Re-point the others in Meta Business Manager.', [
                'kept_store_id' => $candidate->id,
                'kept_store_name' => $candidate->name,
                'discarded_stores' => $others->toArray(),
            ]);
        }
    }
};
