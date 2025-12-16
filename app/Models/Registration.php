<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Registration extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'account_type',
        'status',
        'phone_verified',
        'phone_verified_at',
        'payment_amount',
        'payment_method',
        'payment_reference',
        'payment_verified_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'phone_verified' => 'boolean',
            'phone_verified_at' => 'datetime',
            'payment_amount' => 'decimal:2',
            'payment_verified_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    /**
     * Generate payment reference for QRIS/VA.
     */
    public function generatePaymentReference(): string
    {
        $prefix = match($this->account_type) {
            'SCALE' => 'SCALE',
            'INFINITE' => 'INF',
            default => 'REG',
        };
        
        return $prefix . '-' . strtoupper(Str::random(8));
    }

    /**
     * Check if registration is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if payment is required.
     */
    public function requiresPayment(): bool
    {
        return in_array($this->account_type, ['CORE', 'SCALE', 'INFINITE']);
    }

    /**
     * Get payment amount based on account type.
     */
    public static function getPaymentAmount(string $accountType): float
    {
        return match($accountType) {
            'CORE' => 20000, // Rp 20.000
            'SCALE' => 99000, // Rp 99.000
            'INFINITE' => 499000, // Rp 499.000
            'FREE_TRIAL' => 0, // Gratis
            default => 0,
        };
    }
}
