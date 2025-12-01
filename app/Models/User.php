<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'first_name',
        'last_name',
        'date_of_birth',
        'email',
        'email_verified_at',
        'phone_number',
        'phone_verified_at',
        'bvn_hash',
        'nin_hash',
        'verification_type_id',
        'verification_number',
        'country_id',
        'state_id',
        'city_id',
        'address',
        'address_verification_document',
        'address_verified_at',
        'face_biometric',
        'primary_device_id',
        'password',
        'transaction_pin',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'transaction_pin',
        'bvn_hash',
        'nin_hash',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'address_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'date_of_birth' => 'date',
        ];
    }

    /**
     * Set the transaction PIN attribute.
     */
    public function setTransactionPinAttribute($value)
    {
        if ($value) {
            $this->attributes['transaction_pin'] = \Hash::make($value);
        }
    }

    /**
     * Verify transaction PIN.
     */
    public function verifyTransactionPin($pin)
    {
        return \Hash::check($pin, $this->transaction_pin);
    }

    /**
     * Boot method to auto-generate UUID.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Override the newUniqueId method to auto-generate UUID
     */
    public function newUniqueId()
    {
        return (string) Str::uuid();
    }

    /**
     * Get the columns that should receive a unique identifier
     */
    public function uniqueIds()
    {
        return ['uuid'];
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Relationship: User has one primary wallet.
     */
    public function wallet()
    {
        return $this->hasOne(Wallet::class)->where('wallet_type', 'primary');
    }

    /**
     * Relationship: User has many wallets.
     */
    public function wallets()
    {
        return $this->hasMany(Wallet::class);
    }

    /**
     * Relationship: User has many audit logs.
     */
    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }
}
