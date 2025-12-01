<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed verification types first
        $this->call([
            VerificationTypeSeeder::class,
            AddressVerificationTypeSeeder::class,
            LocationSeeder::class,
            UserSeeder::class,
        ]);

        // Then seed users and wallets
        $this->call([
            UserSeeder::class,
        ]);
    }
}
