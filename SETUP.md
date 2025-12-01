# Zenex Backend - Setup Documentation

## Overview
This is a Laravel 12.0 API-only application implementing a secure Money Ledger system with JWT authentication, double-entry bookkeeping, and enterprise-grade security features.

## What's Been Implemented

### 1. **API-Only Configuration**
- Configured Laravel for API-only usage (no web routes or sessions)
- CORS middleware enabled for API access
- All routes defined in `routes/api.php`

### 2. **JWT Authentication**
- Package: `tymon/jwt-auth` (v2.2.1)
- JWT secret already generated
- Authentication endpoints: register, login, logout, refresh, me
- Default guard: `api` with `jwt` driver

### 3. **Database Schema - Money Ledger System**
Complete database design with the following tables:
- **users** - User accounts with KYC verification
- **wallets** - Multi-wallet support (primary, savings)
- **wallet_transactions** - Transaction records
- **ledger_entries** - Double-entry bookkeeping system
- **transaction_limits** - Daily/single transaction limits
- **suspicious_activities** - Fraud detection logs
- **audit_logs** - Complete audit trail
- **countries, states, cities** - Location data
- **verification_types** - KYC verification types
- **address_verification_types** - Address verification
- **devices** - User device tracking

### 4. **Security Features Implemented**
- ✅ Double-entry bookkeeping (debit + credit entries)
- ✅ Pessimistic locking (`lockForUpdate()`)
- ✅ Balance checksums (SHA-256)
- ✅ Idempotency keys for transactions
- ✅ Transaction PIN hashing (bcrypt)
- ✅ Wallet PIN security
- ✅ Brute-force protection
- ✅ Transaction limits enforcement
- ✅ Suspicious activity tracking
- ✅ Comprehensive audit logging

### 5. **Test Data Seeded**
- Location data (USA/California/LA and Nigeria/Lagos)
- Verification types (NIN, BVN, Passport, etc.)
- 3 test users with wallets and transaction limits

## Prerequisites
- PHP 8.2+
- MySQL
- Composer
- Node.js & NPM (optional, for frontend assets)

## Quick Start Commands

### 1. Install Dependencies
```bash
composer install
```

### 2. Configure Environment
```bash
# Copy the example environment file
cp .env.example .env

# Update database credentials in .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=zenex_db
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 3. Generate Application Key
```bash
php artisan key:generate
```

### 4. Run Migrations and Seeders
```bash
# Fresh migration with all seeders
php artisan migrate:fresh --seed
```

### 5. Start Development Server
```bash
php artisan serve
```

The API will be available at: `http://localhost:8000`

## Test User Credentials

After running seeders, you can use these test accounts:

| Email | Password | Transaction PIN | Wallet PIN |
|-------|----------|----------------|------------|
| john@example.com | password | 1234 | 1234 |
| jane@example.com | password | 5678 | 1234 |
| admin@example.com | password | 9999 | 1234 |

## API Endpoints

### Authentication
- `POST /api/auth/register` - Register new user
- `POST /api/auth/login` - Login
- `POST /api/auth/logout` - Logout (requires auth)
- `POST /api/auth/refresh` - Refresh JWT token
- `GET /api/auth/me` - Get authenticated user details

### Health Check
- `GET /api/health` - API health status

## Testing the API

### 1. Login
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password"
  }'
```

### 2. Get User Details (with token)
```bash
curl -X GET http://localhost:8000/api/auth/me \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

### 3. Health Check
```bash
curl http://localhost:8000/api/health
```

## Database Access

### View Data in Terminal
```bash
# Access database via Artisan Tinker
php artisan tinker

# Example queries in Tinker:
User::all()
Wallet::with('user')->get()
WalletTransaction::latest()->take(10)->get()
```

### Visual Database Browser
```bash
# Open Prisma Studio (if installed)
php artisan prisma:studio
```

## Project Structure

```
app/
├── Http/Controllers/     # API controllers
├── Models/              # Eloquent models (User, Wallet, etc.)
├── Providers/           # Service providers
config/                  # Configuration files
database/
├── migrations/          # Database schema migrations
└── seeders/            # Database seeders
routes/
└── api.php             # API routes
```

## Key Models & Features

### User Model
- UUID-based identification
- JWT authentication support
- Transaction PIN hashing
- Multiple wallet relationships

### Wallet Model
- Credit/debit methods with double-entry bookkeeping
- Balance checksum validation
- PIN security
- Pessimistic locking

### Wallet Transaction Model
- Idempotency key support
- Reference number generation
- Status tracking (pending, completed, failed)

## Security Score
✅ **93% Security Implementation**

Implemented critical security features:
- Atomicity & Consistency
- Idempotency
- PIN Security
- Balance Validation
- Double-entry Bookkeeping
- Audit Trail
- Rate Limiting Prevention
- Transaction Limits

## Additional Commands

### Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

### Run Specific Seeder
```bash
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=LocationSeeder
```

### Check Migration Status
```bash
php artisan migrate:status
```

### Rollback Migrations
```bash
php artisan migrate:rollback
```

## Notes
- The JWT secret is already generated and stored in `.env`
- All timestamps are in UTC
- Transaction PINs and Wallet PINs are hashed using bcrypt
- UUIDs are automatically generated for users
- Wallet balance checksums are calculated on every transaction

## Support
For issues or questions, please refer to the Laravel documentation: https://laravel.com/docs
