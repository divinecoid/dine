<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankAccount extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mdx_bank_accounts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'mdx_brand_id',
        'mdx_store_id',
        'account_name',
        'account_number',
        'bank_name',
        'bank_code',
        'branch_name',
        'currency',
        'balance',
        'minimum_balance',
        'is_active',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'balance' => 'decimal:2',
            'minimum_balance' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($bankAccount) {
            // Validate that either brand_id or store_id is set, but not both
            if (empty($bankAccount->mdx_brand_id) && empty($bankAccount->mdx_store_id)) {
                throw new \InvalidArgumentException('Either mdx_brand_id or mdx_store_id must be set.');
            }
            if (!empty($bankAccount->mdx_brand_id) && !empty($bankAccount->mdx_store_id)) {
                throw new \InvalidArgumentException('Cannot set both mdx_brand_id and mdx_store_id. Only one should be set.');
            }
        });

        static::updating(function ($bankAccount) {
            // Validate that either brand_id or store_id is set, but not both
            if (empty($bankAccount->mdx_brand_id) && empty($bankAccount->mdx_store_id)) {
                throw new \InvalidArgumentException('Either mdx_brand_id or mdx_store_id must be set.');
            }
            if (!empty($bankAccount->mdx_brand_id) && !empty($bankAccount->mdx_store_id)) {
                throw new \InvalidArgumentException('Cannot set both mdx_brand_id and mdx_store_id. Only one should be set.');
            }
        });
    }

    /**
     * Get the brand that owns the bank account.
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'mdx_brand_id');
    }

    /**
     * Get the store that owns the bank account.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'mdx_store_id');
    }

    /**
     * Get the owner (brand or store) of the bank account.
     */
    public function owner()
    {
        return $this->mdx_brand_id ? $this->brand : $this->store;
    }

    /**
     * Get the owner type.
     */
    public function getOwnerTypeAttribute(): string
    {
        return $this->mdx_brand_id ? 'brand' : 'store';
    }

    /**
     * Check if account belongs to a brand.
     */
    public function isBrandAccount(): bool
    {
        return !empty($this->mdx_brand_id);
    }

    /**
     * Check if account belongs to a store.
     */
    public function isStoreAccount(): bool
    {
        return !empty($this->mdx_store_id);
    }

    /**
     * Add balance to account.
     */
    public function addBalance(float $amount): void
    {
        $this->increment('balance', $amount);
    }

    /**
     * Subtract balance from account.
     */
    public function subtractBalance(float $amount): bool
    {
        $newBalance = $this->balance - $amount;
        
        // Check minimum balance
        if ($this->minimum_balance !== null && $newBalance < $this->minimum_balance) {
            return false;
        }

        $this->decrement('balance', $amount);
        return true;
    }

    /**
     * Check if account has sufficient balance.
     */
    public function hasSufficientBalance(float $amount): bool
    {
        $newBalance = $this->balance - $amount;
        
        if ($this->minimum_balance !== null) {
            return $newBalance >= $this->minimum_balance;
        }

        return $newBalance >= 0;
    }

    /**
     * Scope a query to only include active bank accounts.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by brand.
     */
    public function scopeForBrand($query, int $brandId)
    {
        return $query->where('mdx_brand_id', $brandId);
    }

    /**
     * Scope a query to filter by store.
     */
    public function scopeForStore($query, int $storeId)
    {
        return $query->where('mdx_store_id', $storeId);
    }
}

