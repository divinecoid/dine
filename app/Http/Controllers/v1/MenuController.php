<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\v1\Menu;
use App\Models\v1\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MenuController extends Controller
{
    /**
     * Display a listing of the menus.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Menu::query();

        // Filter by is_active
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Filter by is_available (global)
        if ($request->has('is_available')) {
            $query->where('is_available', $request->boolean('is_available'));
        }

        // Filter by store_id (menus available in store)
        if ($request->has('store_id')) {
            $query->availableInStore($request->store_id);
        }

        // Filter by category_id
        if ($request->has('category_id')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('mdx_categories.id', $request->category_id);
            });
        }

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Order by
        $orderBy = $request->get('order_by', 'sort_order');
        $orderDir = $request->get('order_dir', 'asc');
        $query->orderBy($orderBy, $orderDir);

        // Load relationships
        $with = $request->get('with', '');
        if ($with) {
            $query->with(explode(',', $with));
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $menus = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $menus->items(),
            'meta' => [
                'current_page' => $menus->currentPage(),
                'last_page' => $menus->lastPage(),
                'per_page' => $menus->perPage(),
                'total' => $menus->total(),
            ],
        ]);
    }

    /**
     * Store a newly created menu.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:mdx_menus,slug'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'image' => ['nullable', 'string', 'max:255'],
            'is_available' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['exists:mdx_categories,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $data = $validator->validated();
            $categoryIds = $data['category_ids'] ?? [];
            unset($data['category_ids']);

            $menu = Menu::create($data);

            // Attach categories if provided
            if (!empty($categoryIds)) {
                $menu->categories()->attach($categoryIds);
            }

            $menu->load('categories');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Menu created successfully',
                'data' => $menu,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to create menu',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified menu.
     */
    public function show(Menu $menu): JsonResponse
    {
        $menu->load(['categories', 'stores']);

        return response()->json([
            'success' => true,
            'data' => $menu,
        ]);
    }

    /**
     * Update the specified menu.
     */
    public function update(Request $request, Menu $menu): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('mdx_menus', 'slug')->ignore($menu->id),
            ],
            'description' => ['nullable', 'string'],
            'price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'image' => ['nullable', 'string', 'max:255'],
            'is_available' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['exists:mdx_categories,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $data = $validator->validated();
            $categoryIds = $data['category_ids'] ?? null;
            unset($data['category_ids']);

            $menu->update($data);

            // Sync categories if provided
            if ($categoryIds !== null) {
                $menu->categories()->sync($categoryIds);
            }

            $menu->load('categories');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Menu updated successfully',
                'data' => $menu->fresh(),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to update menu',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified menu.
     */
    public function destroy(Menu $menu): JsonResponse
    {
        $menu->delete();

        return response()->json([
            'success' => true,
            'message' => 'Menu deleted successfully',
        ]);
    }

    /**
     * Restore the specified menu.
     */
    public function restore(int $id): JsonResponse
    {
        $menu = Menu::withTrashed()->findOrFail($id);
        $menu->restore();

        return response()->json([
            'success' => true,
            'message' => 'Menu restored successfully',
            'data' => $menu,
        ]);
    }

    /**
     * Update menu availability in a store.
     */
    public function updateStoreAvailability(Request $request, Menu $menu, Store $store): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'is_available' => ['required', 'boolean'],
            'stock_quantity' => ['nullable', 'integer', 'min:0'],
            'out_of_stock_reason' => ['nullable', 'string'],
            'min_stock_threshold' => ['nullable', 'integer', 'min:0'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        // Auto-set is_available based on stock_quantity if min_stock_threshold is set
        if (isset($data['stock_quantity']) && isset($data['min_stock_threshold'])) {
            $data['is_available'] = $data['stock_quantity'] >= $data['min_stock_threshold'];
        }

        // Sync menu to store with pivot data
        $menu->stores()->syncWithoutDetaching([
            $store->id => $data,
        ]);

        $menu->load(['stores' => function ($query) use ($store) {
            $query->where('mdx_stores.id', $store->id);
        }]);

        return response()->json([
            'success' => true,
            'message' => 'Menu availability updated successfully',
            'data' => $menu->stores->first()->pivot,
        ]);
    }

    /**
     * Get stores where menu is available.
     */
    public function availableStores(Menu $menu): JsonResponse
    {
        $stores = $menu->availableStores()->get();

        return response()->json([
            'success' => true,
            'data' => $stores,
        ]);
    }

    /**
     * Check menu availability in a store.
     */
    public function checkAvailability(Menu $menu, Store $store): JsonResponse
    {
        $isAvailable = $menu->isAvailableInStore($store->id);
        $pivot = $menu->stores()->where('mdx_stores.id', $store->id)->first()?->pivot;

        return response()->json([
            'success' => true,
            'data' => [
                'is_available' => $isAvailable,
                'stock_quantity' => $pivot->stock_quantity ?? null,
                'out_of_stock_reason' => $pivot->out_of_stock_reason ?? null,
                'min_stock_threshold' => $pivot->min_stock_threshold ?? null,
            ],
        ]);
    }
}

