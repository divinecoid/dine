<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\v1\Brand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BrandController extends Controller
{
    /**
     * Display a listing of the brands.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Brand::query();

        // Filter by is_active if provided
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
        $brands = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $brands->items(),
            'meta' => [
                'current_page' => $brands->currentPage(),
                'last_page' => $brands->lastPage(),
                'per_page' => $brands->perPage(),
                'total' => $brands->total(),
            ],
        ]);
    }

    /**
     * Store a newly created brand.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:mdx_brands,slug'],
            'description' => ['nullable', 'string'],
            'logo' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $brand = Brand::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Brand created successfully',
            'data' => $brand,
        ], 201);
    }

    /**
     * Display the specified brand.
     */
    public function show(Brand $brand): JsonResponse
    {
        $brand->load(['stores', 'categories']);

        return response()->json([
            'success' => true,
            'data' => $brand,
        ]);
    }

    /**
     * Update the specified brand.
     */
    public function update(Request $request, Brand $brand): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('mdx_brands', 'slug')->ignore($brand->id),
            ],
            'description' => ['nullable', 'string'],
            'logo' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $brand->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Brand updated successfully',
            'data' => $brand->fresh(),
        ]);
    }

    /**
     * Remove the specified brand.
     */
    public function destroy(Brand $brand): JsonResponse
    {
        $brand->delete();

        return response()->json([
            'success' => true,
            'message' => 'Brand deleted successfully',
        ]);
    }

    /**
     * Restore the specified brand.
     */
    public function restore(int $id): JsonResponse
    {
        $brand = Brand::withTrashed()->findOrFail($id);
        $brand->restore();

        return response()->json([
            'success' => true,
            'message' => 'Brand restored successfully',
            'data' => $brand,
        ]);
    }
}

