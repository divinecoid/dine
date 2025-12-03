<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'account_type',
        'mdx_store_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
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
            'password' => 'hashed',
        ];
    }

    /**
     * Check if user is brand owner.
     */
    public function isBrandOwner(): bool
    {
        return $this->role === 'brand_owner';
    }

    /**
     * Check if user is store manager.
     */
    public function isStoreManager(): bool
    {
        return $this->role === 'store_manager';
    }

    /**
     * Check if user has admin access.
     */
    public function hasAdminAccess(): bool
    {
        return in_array($this->role, ['brand_owner', 'store_manager']);
    }

    /**
     * Get the brands owned by the user (for brand_owner role).
     */
    public function brands()
    {
        return $this->belongsToMany(\App\Models\v1\Brand::class, 'mdx_brand_user', 'user_id', 'mdx_brand_id')
            ->withTimestamps();
    }

    /**
     * Get the store managed by the user (for store_manager role).
     */
    public function store()
    {
        return $this->hasOne(\App\Models\v1\Store::class, 'user_id');
    }

    /**
     * Get accessible brand IDs for the user.
     */
    public function getAccessibleBrandIds(): array
    {
        if ($this->isBrandOwner()) {
            return $this->brands()->pluck('mdx_brands.id')->toArray();
        } elseif ($this->isStoreManager() && $this->store) {
            return [$this->store->mdx_brand_id];
        }
        
        return [];
    }

    /**
     * Get accessible store IDs for the user.
     */
    public function getAccessibleStoreIds(): array
    {
        if ($this->isBrandOwner()) {
            $brandIds = $this->getAccessibleBrandIds();
            return \App\Models\v1\Store::whereIn('mdx_brand_id', $brandIds)->pluck('id')->toArray();
        } elseif ($this->isStoreManager() && $this->store) {
            return [$this->store->id];
        }
        
        return [];
    }

    /**
     * Get the store where the user works (for chef, waiter, kasir).
     */
    public function workStore()
    {
        return $this->belongsTo(\App\Models\v1\Store::class, 'mdx_store_id');
    }

    /**
     * Check if user is chef.
     */
    public function isChef(): bool
    {
        return $this->role === 'chef';
    }

    /**
     * Check if user is waiter.
     */
    public function isWaiter(): bool
    {
        return $this->role === 'waiter';
    }

    /**
     * Check if user is kasir.
     */
    public function isKasir(): bool
    {
        return $this->role === 'kasir';
    }

    /**
     * Check if user is staff (chef, waiter, or kasir).
     */
    public function isStaff(): bool
    {
        return in_array($this->role, ['chef', 'waiter', 'kasir']);
    }

    /**
     * Check if user has CORE account type.
     */
    public function isCoreAccount(): bool
    {
        return $this->account_type === 'CORE';
    }

    /**
     * Check if user has SCALE account type.
     */
    public function isScaleAccount(): bool
    {
        return $this->account_type === 'SCALE';
    }

    /**
     * Check if user has INFINITE account type.
     */
    public function isInfiniteAccount(): bool
    {
        return $this->account_type === 'INFINITE';
    }

    /**
     * Get account type label.
     */
    public function getAccountTypeLabel(): ?string
    {
        return match($this->account_type) {
            'CORE' => 'Core',
            'SCALE' => 'Scale',
            'INFINITE' => 'Infinite',
            default => null,
        };
    }
}
