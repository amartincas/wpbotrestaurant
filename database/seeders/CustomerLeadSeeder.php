<?php

namespace Database\Seeders;

use App\Enums\CustomerLeadSource;
use App\Enums\CustomerLeadStatus;
use App\Models\CustomerLead;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Database\Seeder;

class CustomerLeadSeeder extends Seeder
{
    public function run(): void
    {
        $store = Store::first();
        if (!$store) {
            $this->command->warn('No stores found. Skipping CustomerLeadSeeder.');
            return;
        }

        $product = Product::where('store_id', $store->id)->first();

        $leads = [
            [
                'customer_phone'          => '573100000001',
                'customer_name'           => 'María García',
                'first_source'            => CustomerLeadSource::META_ADS->value,
                'status'                  => CustomerLeadStatus::REPEAT_CUSTOMER->value,
                'total_orders'            => 3,
                'customer_lifetime_value' => 119700,
                'first_contact_at'        => now()->subDays(30),
                'last_contact_at'         => now()->subDays(2),
                'last_order_at'           => now()->subDays(2),
                'conversion_date'         => now()->subDays(28),
            ],
            [
                'customer_phone'          => '573100000002',
                'customer_name'           => 'Carlos López',
                'first_source'            => CustomerLeadSource::META_ADS->value,
                'status'                  => CustomerLeadStatus::CUSTOMER->value,
                'total_orders'            => 1,
                'customer_lifetime_value' => 43900,
                'first_contact_at'        => now()->subDays(10),
                'last_contact_at'         => now()->subDays(5),
                'last_order_at'           => now()->subDays(5),
                'conversion_date'         => now()->subDays(5),
            ],
            [
                'customer_phone'          => '573100000003',
                'customer_name'           => 'Ana Martínez',
                'first_source'            => CustomerLeadSource::WHATSAPP_DIRECT->value,
                'status'                  => CustomerLeadStatus::INTERESTED->value,
                'total_orders'            => 0,
                'customer_lifetime_value' => 0,
                'first_contact_at'        => now()->subDays(5),
                'last_contact_at'         => now()->subDays(1),
            ],
            [
                'customer_phone'          => '573100000004',
                'customer_name'           => null,
                'first_source'            => CustomerLeadSource::INSTAGRAM->value,
                'status'                  => CustomerLeadStatus::NEW->value,
                'total_orders'            => 0,
                'customer_lifetime_value' => 0,
                'first_contact_at'        => now()->subHours(2),
                'last_contact_at'         => now()->subHours(2),
            ],
            [
                'customer_phone'          => '573100000005',
                'customer_name'           => 'Pedro Ruiz',
                'first_source'            => CustomerLeadSource::META_ADS->value,
                'status'                  => CustomerLeadStatus::INACTIVE->value,
                'total_orders'            => 1,
                'customer_lifetime_value' => 39900,
                'first_contact_at'        => now()->subDays(60),
                'last_contact_at'         => now()->subDays(45),
                'last_order_at'           => now()->subDays(55),
                'conversion_date'         => now()->subDays(55),
            ],
        ];

        foreach ($leads as $data) {
            $data['store_id'] = $store->id;
            if ($product) {
                $data['first_product_id'] = $product->id;
            }

            CustomerLead::updateOrCreate(
                ['store_id' => $store->id, 'customer_phone' => $data['customer_phone']],
                $data
            );
        }

        $this->command->info('CustomerLeadSeeder: 5 leads de prueba creados.');
    }
}
