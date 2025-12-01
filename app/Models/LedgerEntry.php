<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LedgerEntry extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'transaction_id',
        'wallet_id',
        'entry_type',
        'amount',
        'balance_before',
        'balance_after',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    /**
     * Relationship: Ledger entry belongs to a transaction.
     */
    public function transaction()
    {
        return $this->belongsTo(WalletTransaction::class, 'transaction_id');
    }

    /**
     * Relationship: Ledger entry belongs to a wallet.
     */
    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * Scope: Get debit entries.
     */
    public function scopeDebits($query)
    {
        return $query->where('entry_type', 'debit');
    }

    /**
     * Scope: Get credit entries.
     */
    public function scopeCredits($query)
    {
        return $query->where('entry_type', 'credit');
    }

    /**
     * Scope: Get entries for a specific wallet.
     */
    public function scopeForWallet($query, $walletId)
    {
        return $query->where('wallet_id', $walletId);
    }
}
