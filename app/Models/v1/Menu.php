<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mdx_menus';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'image',
        'is_available',
        'is_active',
        'sort_order',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_available' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * The categories that belong to the menu.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            Category::class,
            'mdx_category_menu',
            'mdx_menu_id',
            'mdx_category_id'
        )
            ->withTimestamps();
    }

    /**
     * The stores that belong to the menu.
     */
    public function stores(): BelongsToMany
    {
        return $this->belongsToMany(
            Store::class,
            'mdx_menu_store',
            'mdx_menu_id',
            'mdx_store_id'
        )
            ->withPivot(['is_available', 'stock_quantity', 'out_of_stock_reason', 'min_stock_threshold'])
            ->withTimestamps();
    }

    /**
     * Get stores where this menu is available.
     */
    public function availableStores(): BelongsToMany
    {
        return $this->stores()
            ->wherePivot('is_available', true)
            ->where('mdx_stores.is_active', true);
    }

    /**
     * Check if menu is available in a specific store.
     */
    public function isAvailableInStore(int $storeId): bool
    {
        if (!$this->is_active || !$this->is_available) {
            return false;
        }

        $pivot = $this->stores()
            ->where('mdx_stores.id', $storeId)
            ->first()?->pivot;

        return $pivot && $pivot->is_available === true;
    }

    /**
     * Scope a query to only include active menus.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('is_available', true);
    }

    /**
     * Scope a query to only include menus available in a specific store.
     */
    public function scopeAvailableInStore($query, int $storeId)
    {
        return $query->active()
            ->whereHas('stores', function ($q) use ($storeId) {
                $q->where('mdx_stores.id', $storeId)
                  ->where('mdx_menu_store.is_available', true);
            });
    }
}

