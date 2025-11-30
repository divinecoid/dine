<?php

namespace App\Http\Controllers;

use App\Models\v1\Brand;
use App\Models\v1\Category;
use App\Models\v1\Menu;
use App\Models\v1\Store;
use App\Models\v1\Table;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicMenuController extends Controller
{
    /**
     * Display menu for a brand and table.
     */
    public function show(Request $request, string $brandSlug): View
    {
        // Get brand by slug
        $brand = Brand::where('slug', $brandSlug)
            ->where('is_active', true)
            ->firstOrFail();

        // Get table by unique identifier from query parameter (required from QR code)
        $tableIdentifier = $request->query('table');
        
        // If no table_id, show error page (must come from QR code)
        if (!$tableIdentifier) {
            return view('public.table-error', [
                'brand' => $brand,
                'brandSlug' => $brandSlug,
                'error' => 'QR code meja tidak ditemukan. Silakan scan QR code yang ada di meja restoran.',
            ]);
        }

        $table = Table::findByUniqueIdentifier($tableIdentifier);
        
        // If table not found, show error page
        if (!$table) {
            return view('public.table-error', [
                'brand' => $brand,
                'brandSlug' => $brandSlug,
                'error' => 'QR code meja tidak valid atau tidak terkait dengan restoran ini. Silakan scan QR code yang benar.',
            ]);
        }
        
        // Load store relationship
        $table->load('store');
        
        // If table not found or invalid, show error page
        if (!$table->store || $table->store->mdx_brand_id !== $brand->id) {
            return view('public.table-error', [
                'brand' => $brand,
                'brandSlug' => $brandSlug,
                'error' => 'QR code meja tidak valid atau tidak terkait dengan restoran ini. Silakan scan QR code yang benar.',
            ]);
        }

        // Get store from table
        $store = $table->store;
        
        if (!$store || !$store->is_active) {
            return view('public.table-error', [
                'brand' => $brand,
                'brandSlug' => $brandSlug,
                'error' => 'Store tidak aktif. Silakan hubungi staff restoran.',
            ]);
        }

        // Get categories for this brand
        $categories = Category::where('mdx_brand_id', $brand->id)
            ->where('is_active', true)
            ->ordered()
            ->get();

        // Get selected category or default to "All"
        $selectedCategoryId = $request->query('category');
        $selectedCategory = null;
        $showAll = false;

        if ($selectedCategoryId === 'all' || $selectedCategoryId === null) {
            // Show all menus from all categories
            $showAll = true;
        } elseif ($selectedCategoryId) {
            // Try to find the selected category
            $selectedCategory = $categories->firstWhere('id', $selectedCategoryId);
            // If category not found, default to "All"
            if (!$selectedCategory) {
                $showAll = true;
            }
        } else {
            // No category parameter, default to "All"
            $showAll = true;
        }

        // Get menus for selected category and store
        $menus = collect();
        if ($store) {
            if ($showAll) {
                // Show all menus from all categories
                $menus = Menu::whereHas('categories', function ($query) use ($brand) {
                    $query->where('mdx_categories.mdx_brand_id', $brand->id)
                          ->where('mdx_categories.is_active', true);
                })
                ->availableInStore($store->id)
                ->orderBy('sort_order', 'asc')
                ->get();
            } elseif ($selectedCategory) {
                // Show menus from selected category
                $menus = Menu::whereHas('categories', function ($query) use ($selectedCategory) {
                    $query->where('mdx_categories.id', $selectedCategory->id);
                })
                ->availableInStore($store->id)
                ->orderBy('sort_order', 'asc')
                ->get();
            }
        }

        return view('public.menu', [
            'brand' => $brand,
            'store' => $store,
            'table' => $table,
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
            'showAll' => $showAll,
            'menus' => $menus,
            'tableIdentifier' => $tableIdentifier,
        ]);
    }
}

