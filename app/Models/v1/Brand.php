<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mdx_brands';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'logo',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the stores for the brand.
     */
    public function stores(): HasMany
    {
        return $this->hasMany(Store::class, 'mdx_brand_id');
    }

    /**
     * Get the categories for the brand.
     */
    public function categories(): HasMany
    {
        return $this->hasMany(Category::class, 'mdx_brand_id');
    }

    /**
     * Get the bank accounts for the brand.
     */
    public function bankAccounts(): HasMany
    {
        return $this->hasMany(BankAccount::class, 'mdx_brand_id');
    }

    /**
     * Get the users that own this brand.
     */
    public function owners()
    {
        return $this->belongsToMany(\App\Models\User::class, 'mdx_brand_user', 'mdx_brand_id', 'user_id')
            ->withTimestamps();
    }

    /**
     * Scope a query to only include active brands.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter brands accessible by a user.
     */
    public function scopeAccessibleBy($query, $user)
    {
        if ($user->isBrandOwner()) {
            $brandIds = $user->brands()->pluck('mdx_brands.id')->toArray();
            return $query->whereIn('id', $brandIds);
        } elseif ($user->isStoreManager() && $user->store) {
            return $query->where('id', $user->store->mdx_brand_id);
        }
        
        return $query->whereRaw('1 = 0'); // Return empty result if no access
    }
}

