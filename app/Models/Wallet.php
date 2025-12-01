<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class Wallet extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'balance',
        'currency',
        'wallet_type',
        'pin',
        'is_active',
    ];

    /**
     * The attributes that should be hidden.
     *
     * @var array
     */
    protected $hidden = [
        'pin',
        'balance_checksum',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'balance' => 'decimal:2',
        'is_active' => 'boolean',
        'is_locked' => 'boolean',
        'locked_at' => 'datetime',
        'pin_locked_until' => 'datetime',
        'last_failed_attempt_at' => 'datetime',
    ];

    /**
     * Relationship: Wallet belongs to a user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: Wallet has many transactions.
     */
    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class, 'sender_wallet_id')
            ->orWhere('receiver_wallet_id', $this->id);
    }

    /**
     * Relationship: Wallet has many ledger entries.
     */
    public function ledgerEntries()
    {
        return $this->hasMany(LedgerEntry::class);
    }

    /**
     * Set PIN with hashing.
     */
    public function setPin($pin)
    {
        $this->pin = Hash::make($pin);
        $this->save();
    }

    /**
     * Verify PIN.
     */
    public function verifyPin($pin)
    {
        if (! $this->canAttemptPin()) {
            throw new \Exception('Wallet is locked due to too many failed attempts.');
        }

        if (Hash::check($pin, $this->pin)) {
            $this->resetFailedAttempts();

            return true;
        }

        $this->incrementFailedAttempts();

        return false;
    }

    /**
     * Check if PIN attempt is allowed.
     */
    public function canAttemptPin()
    {
        if ($this->pin_locked_until && now() < $this->pin_locked_until) {
            return false;
        }

        if ($this->failed_pin_attempts >= $this->max_pin_attempts) {
            $this->lockPin();

            return false;
        }

        return true;
    }

    /**
     * Increment failed PIN attempts.
     */
    protected function incrementFailedAttempts()
    {
        $this->increment('failed_pin_attempts');
        $this->last_failed_attempt_at = now();
        $this->save();

        if ($this->failed_pin_attempts >= $this->max_pin_attempts) {
            $this->lockPin();
        }
    }

    /**
     * Lock PIN due to too many failed attempts.
     */
    protected function lockPin()
    {
        $lockoutMinutes = min($this->lockout_duration_minutes * pow(2, $this->failed_pin_attempts - $this->max_pin_attempts), 1440);
        $this->pin_locked_until = now()->addMinutes($lockoutMinutes);
        $this->save();
    }

    /**
     * Reset failed PIN attempts.
     */
    protected function resetFailedAttempts()
    {
        $this->failed_pin_attempts = 0;
        $this->pin_locked_until = null;
        $this->last_failed_attempt_at = null;
        $this->save();
    }

    /**
     * Calculate balance checksum.
     */
    public function calculateChecksum($balance = null)
    {
        $balance = $balance ?? $this->balance;

        return hash('sha256', $this->id.$balance.config('app.key'));
    }

    /**
     * Verify balance checksum.
     */
    public function verifyChecksum()
    {
        return $this->balance_checksum === $this->calculateChecksum();
    }

    /**
     * Credit wallet with double-entry bookkeeping.
     */
    public function credit($amount, $transactionId, $description = null)
    {
        return DB::transaction(function () use ($amount, $transactionId, $description) {
            // Lock wallet for update
            $wallet = static::where('id', $this->id)->lockForUpdate()->first();

            if (! $wallet->is_active) {
                throw new \Exception('Wallet is not active.');
            }

            if ($wallet->is_locked) {
                throw new \Exception('Wallet is locked.');
            }

            $balanceBefore = $wallet->balance;
            $wallet->balance += $amount;
            $wallet->balance_checksum = $wallet->calculateChecksum($wallet->balance);
            $wallet->save();

            // Create ledger entry
            LedgerEntry::create([
                'transaction_id' => $transactionId,
                'wallet_id' => $wallet->id,
                'entry_type' => 'credit',
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $wallet->balance,
                'description' => $description,
            ]);

            return [
                'balance_before' => $balanceBefore,
                'balance_after' => $wallet->balance,
                'amount' => $amount,
            ];
        });
    }

    /**
     * Debit wallet with double-entry bookkeeping.
     */
    public function debit($amount, $transactionId, $description = null)
    {
        return DB::transaction(function () use ($amount, $transactionId, $description) {
            // Lock wallet for update
            $wallet = static::where('id', $this->id)->lockForUpdate()->first();

            if (! $wallet->is_active) {
                throw new \Exception('Wallet is not active.');
            }

            if ($wallet->is_locked) {
                throw new \Exception('Wallet is locked.');
            }

            if ($wallet->balance < $amount) {
                throw new \Exception('Insufficient balance.');
            }

            $balanceBefore = $wallet->balance;
            $wallet->balance -= $amount;
            $wallet->balance_checksum = $wallet->calculateChecksum($wallet->balance);
            $wallet->save();

            // Create ledger entry
            LedgerEntry::create([
                'transaction_id' => $transactionId,
                'wallet_id' => $wallet->id,
                'entry_type' => 'debit',
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $wallet->balance,
                'description' => $description,
            ]);

            return [
                'balance_before' => $balanceBefore,
                'balance_after' => $wallet->balance,
                'amount' => $amount,
            ];
        });
    }
}
