<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\v1\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    /**
     * Display a listing of the brands.
     */
    public function index(Request $request)
    {
        $query = Brand::query();

        // Search
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('slug', 'like', '%' . $request->search . '%');
        }

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status === 'active');
        }

        // Order by
        $orderBy = $request->get('order_by', 'created_at');
        $orderDir = $request->get('order_dir', 'desc');
        $query->orderBy($orderBy, $orderDir);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $brands = $query->paginate($perPage)->withQueryString();

        return view('admin.brands.index', compact('brands'));
    }

    /**
     * Show the form for creating a new brand.
     */
    public function create()
    {
        return view('admin.brands.create');
    }

    /**
     * Store a newly created brand.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:mdx_brands,slug'],
            'description' => ['nullable', 'string'],
            'logo' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug']) && !empty($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name']);
            
            // Ensure slug is unique
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (Brand::where('slug', $validated['slug'])->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        // Set default is_active
        if (!isset($validated['is_active'])) {
            $validated['is_active'] = true;
        }

        Brand::create($validated);

        return redirect()->route('admin.brands.index')
            ->with('success', 'Brand created successfully.');
    }

    /**
     * Display the specified brand.
     */
    public function show(Brand $brand)
    {
        $brand->load(['stores', 'categories']);
        return view('admin.brands.show', compact('brand'));
    }

    /**
     * Show the form for editing the specified brand.
     */
    public function edit(Brand $brand)
    {
        return view('admin.brands.edit', compact('brand'));
    }

    /**
     * Update the specified brand.
     */
    public function update(Request $request, Brand $brand)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                'unique:mdx_brands,slug,' . $brand->id,
            ],
            'description' => ['nullable', 'string'],
            'logo' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        // Auto-generate slug if not provided and name changed
        if (empty($validated['slug']) && $validated['name'] !== $brand->name) {
            $validated['slug'] = Str::slug($validated['name']);
            
            // Ensure slug is unique
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (Brand::where('slug', $validated['slug'])->where('id', '!=', $brand->id)->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        // Set default is_active
        if (!isset($validated['is_active'])) {
            $validated['is_active'] = true;
        }

        $brand->update($validated);

        return redirect()->route('admin.brands.index')
            ->with('success', 'Brand updated successfully.');
    }

    /**
     * Remove the specified brand.
     */
    public function destroy(Brand $brand)
    {
        $brand->delete();

        return redirect()->route('admin.brands.index')
            ->with('success', 'Brand deleted successfully.');
    }
}

