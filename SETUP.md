# Zenex Backend - Money Ledger API

Laravel 12.0 API-only application with JWT authentication and secure money ledger system implementing double-entry bookkeeping.

---

## Setup Commands

### 1. Install Dependencies
```bash
composer install
```

### 2. Configure Environment
```bash
cp .env.example .env
# Update database credentials in .env
```

### 3. Generate Keys & Run Migrations
```bash
php artisan key:generate
php artisan migrate:fresh --seed
```

### 4. Start Server
```bash
php artisan serve
```
API available at: `http://localhost:8000`

---

## Test Credentials

| Email | Password | Transaction PIN | Wallet PIN |
|-------|----------|----------------|------------|
| john@example.com | password | 1234 | 1234 |
| jane@example.com | password | 5678 | 1234 |
| admin@example.com | password | 9999 | 1234 |

---

## Transaction Workflow

### 1. **User Authentication**
```
POST /api/auth/login → Returns JWT token
```

### 2. **Wallet Operations** (via Model Methods)
```php
// Credit wallet
$wallet->credit($amount, $description, $idempotencyKey);

// Debit wallet
$wallet->debit($amount, $description, $idempotencyKey);
```

### 3. **Transaction Flow**
1. **Validate transaction PIN** - User provides PIN for authorization
2. **Verify wallet PIN** - Wallet-level security check
3. **Check transaction limits** - Daily/single transaction limits
4. **Idempotency check** - Prevent duplicate transactions
5. **Pessimistic locking** - Lock wallet row (`lockForUpdate()`)
6. **Balance validation** - Ensure sufficient funds
7. **Create transaction** - Generate unique reference number
8. **Double-entry bookkeeping** - Create 2 ledger entries:
   - **Debit entry** - Reduce sender's balance
   - **Credit entry** - Increase receiver's balance
9. **Calculate checksum** - SHA-256 hash of balance for integrity
10. **Update limits** - Track daily spent and transaction count
11. **Audit logging** - Record all transaction details
12. **Fraud detection** - Monitor suspicious patterns

### 4. **Transaction States**
- `pending` - Transaction initiated
- `completed` - Successfully processed
- `failed` - Transaction rejected

---

## Security Features Covered

### ✅ **Critical Security (93% Implementation)**

#### 1. **Atomicity & Consistency**
- Database transactions wrap all operations
- Rollback on any failure
- Pessimistic locking (`lockForUpdate()`)

#### 2. **Idempotency**
- Unique `idempotency_key` prevents duplicate transactions
- Database constraint enforces uniqueness
- Returns existing transaction if key exists

#### 3. **PIN Security**
- Transaction PINs hashed with bcrypt
- Wallet PINs hashed separately
- PIN attempts tracked for brute-force protection
- Auto-lock after 5 failed attempts

#### 4. **Balance Validation**
- Real-time balance checks before debit
- Balance checksums (SHA-256) for integrity verification
- Double-entry ensures debits = credits

#### 5. **Double-Entry Bookkeeping**
- Every transaction creates 2 ledger entries
- Debit + Credit must balance
- Tracks `balance_before` and `balance_after`
- Immutable audit trail

#### 6. **Transaction Limits**
- Daily spending limits per user
- Single transaction maximum
- Daily transaction count limits
- Auto-reset at midnight

#### 7. **Fraud Detection**
- Suspicious activity tracking
- Unusual pattern detection
- Multiple failed PIN attempts
- Geographic anomalies (device tracking)

#### 8. **Audit Trail**
- Complete transaction history
- User action logging
- IP address and device tracking
- Timestamped entries (immutable)

#### 9. **Data Protection**
- Sensitive data hashed (BVN, NIN, PINs)
- UUID-based user identification
- No plain text PINs stored
- Biometric data encryption support

---

## Database Schema

### Core Tables
- `users` - User accounts with KYC verification
- `wallets` - Multi-wallet support (primary, savings)
- `wallet_transactions` - Transaction records
- `ledger_entries` - Double-entry bookkeeping
- `transaction_limits` - Spending limits
- `suspicious_activities` - Fraud logs
- `audit_logs` - Complete audit trail

### Supporting Tables
- `countries`, `states`, `cities` - Location data
- `verification_types` - KYC verification (NIN, BVN, Passport)
- `devices` - User device tracking

---

## Key Model Methods

### Wallet Model
```php
// Credit wallet (receives money)
$wallet->credit(
    amount: 1000.00,
    description: 'Payment received',
    idempotencyKey: 'unique-key-123'
);

// Debit wallet (sends money)
$wallet->debit(
    amount: 500.00,
    description: 'Payment sent',
    idempotencyKey: 'unique-key-456'
);

// Verify wallet PIN
$wallet->verifyPin('1234'); // Returns boolean
```

### User Model
```php
// Verify transaction PIN
$user->verifyTransactionPin('1234'); // Returns boolean

// Get wallets
$user->wallet; // Primary wallet
$user->wallets; // All wallets
```

---

## Testing Commands

### View Database Records
```bash
php artisan tinker

# Check users
User::all();

# Check wallets with balances
Wallet::with('user')->get();

# View recent transactions
WalletTransaction::latest()->take(10)->get();

# Check ledger entries (double-entry)
LedgerEntry::with('wallet', 'transaction')->get();
```

### Test Authentication
```bash
# Login
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email": "john@example.com", "password": "password"}'

# Get user details
curl -X GET http://localhost:8000/api/auth/me \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

---

## Notes

- JWT secret auto-generated during setup
- UUIDs auto-generated for users
- All timestamps in UTC
- Transaction PINs and Wallet PINs hashed with bcrypt
- Balance checksums calculated on every transaction
- Idempotency keys prevent duplicate transactions
- Ledger entries are immutable (no updates/deletes)
