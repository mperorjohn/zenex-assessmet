<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VerificationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $verificationTypes = [
            [
                'name' => 'National ID',
                'slug' => 'national-id',
                'description' => 'National Identity Card verification',
                'is_active' => true,
            ],
            [
                'name' => 'Driving Licence',
                'slug' => 'driving-licence',
                'description' => 'Driver\'s License verification',
                'is_active' => true,
            ],
            [
                'name' => 'Voter\'s Card',
                'slug' => 'voters-card',
                'description' => 'Voter\'s Card verification',
                'is_active' => true,
            ],
        ];

        foreach ($verificationTypes as $type) {
            DB::table('verification_types')->updateOrInsert(
                ['slug' => $type['slug']],
                array_merge($type, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
