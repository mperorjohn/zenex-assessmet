<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddressVerificationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $addressVerificationTypes = [
            [
                'name' => 'Utility bill',
                'slug' => 'utility-bill',
                'description' => 'Utility bill for address verification',
                'is_active' => true,
            ],
            [
                'name' => 'Rent receipt and agreement',
                'slug' => 'rent-receipt-and-agreement',
                'description' => 'Rent receipt and rental agreement',
                'is_active' => true,
            ],
            [
                'name' => 'Tax remittance',
                'slug' => 'tax-remittance',
                'description' => 'Tax remittance document',
                'is_active' => true,
            ],
            [
                'name' => 'Land use receipt',
                'slug' => 'land-use-receipt',
                'description' => 'Land use receipt for address verification',
                'is_active' => true,
            ],
            [
                'name' => 'Commercial Bank Statement',
                'slug' => 'commercial-bank-statement',
                'description' => 'Commercial bank statement for address verification',
                'is_active' => true,
            ],
        ];

        foreach ($addressVerificationTypes as $type) {
            DB::table('address_verification_types')->updateOrInsert(
                ['slug' => $type['slug']],
                array_merge($type, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
