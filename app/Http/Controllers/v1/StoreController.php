<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\v1\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class StoreController extends Controller
{
    /**
     * Display a listing of the stores.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Store::query()->with('brand');

        // Filter by user access (brand owners see stores under their brands, store managers see only their store)
        $query->accessibleBy($request->user());

        // Filter by brand_id
        if ($request->has('brand_id')) {
            $query->where('mdx_brand_id', $request->brand_id);
        }

        // Filter by is_active
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Order by
        $orderBy = $request->get('order_by', 'created_at');
        $orderDir = $request->get('order_dir', 'desc');
        $query->orderBy($orderBy, $orderDir);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $stores = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $stores->items(),
            'meta' => [
                'current_page' => $stores->currentPage(),
                'last_page' => $stores->lastPage(),
                'per_page' => $stores->perPage(),
                'total' => $stores->total(),
            ],
        ]);
    }

    /**
     * Store a newly created store.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'mdx_brand_id' => ['required', 'exists:mdx_brands,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:mdx_stores,slug'],
            'description' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'image' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $store = Store::create($validator->validated());
        $store->load('brand');

        return response()->json([
            'success' => true,
            'message' => 'Store created successfully',
            'data' => $store,
        ], 201);
    }

    /**
     * Display the specified store.
     */
    public function show(Store $store): JsonResponse
    {
        $store->load(['brand', 'menus']);

        return response()->json([
            'success' => true,
            'data' => $store,
        ]);
    }

    /**
     * Update the specified store.
     */
    public function update(Request $request, Store $store): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'mdx_brand_id' => ['sometimes', 'required', 'exists:mdx_brands,id'],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('mdx_stores', 'slug')->ignore($store->id),
            ],
            'description' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'image' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $store->update($validator->validated());
        $store->load('brand');

        return response()->json([
            'success' => true,
            'message' => 'Store updated successfully',
            'data' => $store->fresh(),
        ]);
    }

    /**
     * Remove the specified store.
     */
    public function destroy(Store $store): JsonResponse
    {
        $store->delete();

        return response()->json([
            'success' => true,
            'message' => 'Store deleted successfully',
        ]);
    }

    /**
     * Restore the specified store.
     */
    public function restore(int $id): JsonResponse
    {
        $store = Store::withTrashed()->findOrFail($id);
        $store->restore();

        return response()->json([
            'success' => true,
            'message' => 'Store restored successfully',
            'data' => $store,
        ]);
    }

    /**
     * Get available menus for a store.
     */
    public function availableMenus(Store $store): JsonResponse
    {
        $menus = $store->availableMenus()
            ->with(['categories'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $menus,
        ]);
    }
}

