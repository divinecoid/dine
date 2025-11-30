<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mdx_stores';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'mdx_brand_id',
        'name',
        'slug',
        'description',
        'address',
        'phone',
        'email',
        'latitude',
        'longitude',
        'image',
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
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the brand that owns the store.
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'mdx_brand_id');
    }

    /**
     * The menus that belong to the store.
     */
    public function menus(): BelongsToMany
    {
        return $this->belongsToMany(
            Menu::class,
            'mdx_menu_store',
            'mdx_store_id',
            'mdx_menu_id'
        )
            ->withPivot(['is_available', 'stock_quantity', 'out_of_stock_reason', 'min_stock_threshold'])
            ->withTimestamps();
    }

    /**
     * Get only available menus for this store.
     */
    public function availableMenus(): BelongsToMany
    {
        return $this->menus()
            ->wherePivot('is_available', true)
            ->where('mdx_menus.is_active', true)
            ->where('mdx_menus.is_available', true);
    }

    /**
     * Get the bank accounts for the store.
     */
    public function bankAccounts(): HasMany
    {
        return $this->hasMany(BankAccount::class, 'mdx_store_id');
    }

    /**
     * Get the tables for the store.
     */
    public function tables(): HasMany
    {
        return $this->hasMany(Table::class, 'mdx_store_id');
    }

    /**
     * Get the orders for the store.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'mdx_store_id');
    }

    /**
     * Scope a query to only include active stores.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

