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
}
