<?php

namespace Database\Seeders;

use App\Models\TransactionLimit;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first country, state, city for testing
        $country = \DB::table('countries')->first();
        $state = \DB::table('states')->first();
        $city = \DB::table('cities')->first();
        $verificationType = \DB::table('verification_types')->first();

        if (! $country || ! $state || ! $city || ! $verificationType) {
            $this->command->error('Please run countries, states, cities, and verification_types seeders first!');

            return;
        }

        // Create test users
        $users = [
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john@example.com',
                'phone_number' => '+1234567890',
                'date_of_birth' => '1990-01-15',
                'bvn_hash' => hash('sha256', '12345678901'),
                'nin_hash' => hash('sha256', 'NIN12345678'),
                'verification_type_id' => $verificationType->id,
                'verification_number' => 'NAT-'.rand(10000000, 99999999),
                'country_id' => $country->id,
                'state_id' => $state->id,
                'city_id' => $city->id,
                'address' => '123 Main Street',
                'password' => Hash::make('password'),
                'transaction_pin' => '1234', // Will be auto-hashed by model
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
                'is_active' => true,
            ],
            [
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'email' => 'jane@example.com',
                'phone_number' => '+1234567891',
                'date_of_birth' => '1992-05-20',
                'bvn_hash' => hash('sha256', '12345678902'),
                'nin_hash' => hash('sha256', 'NIN12345679'),
                'verification_type_id' => $verificationType->id,
                'verification_number' => 'NAT-'.rand(10000000, 99999999),
                'country_id' => $country->id,
                'state_id' => $state->id,
                'city_id' => $city->id,
                'address' => '456 Oak Avenue',
                'password' => Hash::make('password'),
                'transaction_pin' => '5678',
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
                'is_active' => true,
            ],
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'email' => 'admin@example.com',
                'phone_number' => '+1234567892',
                'date_of_birth' => '1985-08-10',
                'bvn_hash' => hash('sha256', '12345678903'),
                'nin_hash' => hash('sha256', 'NIN12345680'),
                'verification_type_id' => $verificationType->id,
                'verification_number' => 'NAT-'.rand(10000000, 99999999),
                'country_id' => $country->id,
                'state_id' => $state->id,
                'city_id' => $city->id,
                'address' => '789 Admin Boulevard',
                'password' => Hash::make('password'),
                'transaction_pin' => '9999',
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
                'is_active' => true,
            ],
        ];

        foreach ($users as $userData) {
            // Check if user exists
            $existingUser = User::where('email', $userData['email'])->first();

            if ($existingUser) {
                $user = $existingUser;
                $user->update($userData);
                $this->command->info("Updated user: {$user->first_name} {$user->last_name} ({$user->email})");
            } else {
                // Add UUID before creating (boot method backup)
                $userData['uuid'] = (string) \Illuminate\Support\Str::uuid();

                // Create new user
                $user = User::create($userData);
                $this->command->info("Created user: {$user->first_name} {$user->last_name} ({$user->email})");
            }

            // Create primary wallet for user
            $wallet = Wallet::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'wallet_type' => 'primary',
                ],
                [
                    'balance' => rand(1000000, 10000000), // Random balance between 10k-100k kobo (100-1000 USD)
                    'currency' => 'USD',
                    'is_active' => true,
                ]
            );

            // Set wallet PIN (will be auto-hashed)
            $wallet->setPin('1234');

            $displayBalance = number_format($wallet->balance / 100, 2);
            $this->command->info("  ✓ Created primary wallet with balance: \${$displayBalance} {$wallet->currency} ({$wallet->balance} kobo)");

            // Create savings wallet
            $savingsWallet = Wallet::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'wallet_type' => 'savings',
                ],
                [
                    'balance' => rand(500000, 5000000), // Random balance between 5k-50k kobo (50-500 USD)
                    'currency' => 'USD',
                    'is_active' => true,
                ]
            );

            $savingsWallet->setPin('1234');

            $displaySavingsBalance = number_format($savingsWallet->balance / 100, 2);
            $this->command->info("  ✓ Created savings wallet with balance: \${$displaySavingsBalance} {$savingsWallet->currency} ({$savingsWallet->balance} kobo)");

            // Create transaction limits
            TransactionLimit::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'limit_date' => today(),
                ],
                [
                    'daily_limit' => 100000000, // 1,000,000 USD = 100,000,000 kobo
                    'daily_spent' => 0,
                    'single_transaction_limit' => 10000000, // 100,000 USD = 10,000,000 kobo
                    'daily_transaction_count' => 0,
                    'max_daily_transactions' => 50,
                ]
            );

            $this->command->info('  ✓ Created transaction limits');
            $this->command->info('');
        }

        $this->command->info('========================================');
        $this->command->info('Test User Credentials:');
        $this->command->info('========================================');
        $this->command->info('Email: john@example.com');
        $this->command->info('Email: jane@example.com');
        $this->command->info('Email: admin@example.com');
        $this->command->info('Password: password');
        $this->command->info('Transaction PIN: 1234 (John), 5678 (Jane), 9999 (Admin)');
        $this->command->info('Wallet PIN: 1234 (All)');
        $this->command->info('========================================');
    }
}
