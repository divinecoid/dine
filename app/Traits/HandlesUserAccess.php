<?php

namespace App\Traits;

trait HandlesUserAccess
{
    /**
     * Get accessible brand IDs for the current user.
     */
    protected function getAccessibleBrandIds(): array
    {
        $user = auth()->user();
        
        if ($user->isBrandOwner()) {
            return $user->brands()->pluck('mdx_brands.id')->toArray();
        } elseif ($user->isStoreManager() && $user->store) {
            return [$user->store->mdx_brand_id];
        }
        
        return [];
    }

    /**
     * Get accessible store IDs for the current user.
     */
    protected function getAccessibleStoreIds(): array
    {
        $user = auth()->user();
        
        if ($user->isBrandOwner()) {
            $brandIds = $this->getAccessibleBrandIds();
            return \App\Models\v1\Store::whereIn('mdx_brand_id', $brandIds)->pluck('id')->toArray();
        } elseif ($user->isStoreManager() && $user->store) {
            return [$user->store->id];
        }
        
        return [];
    }

    /**
     * Check if user can access a brand.
     */
    protected function canAccessBrand($brandId): bool
    {
        return in_array($brandId, $this->getAccessibleBrandIds());
    }

    /**
     * Check if user can access a store.
     */
    protected function canAccessStore($storeId): bool
    {
        return in_array($storeId, $this->getAccessibleStoreIds());
    }
}

