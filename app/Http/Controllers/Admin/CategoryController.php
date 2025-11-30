<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\v1\Brand;
use App\Models\v1\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::query();

        // Search with proper grouping
        $search = $request->input('search');
        if ($search && trim($search) !== '') {
            $searchTerm = trim($search);
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('slug', 'like', '%' . $searchTerm . '%');
            });
        }

        // Active/Inactive status filter
        $status = $request->input('status');
        if ($status && $status !== '') {
            $query->where('is_active', $status === 'active');
        }

        // Brand filter
        $brandId = $request->input('brand_id');
        if ($brandId && $brandId !== '') {
            $query->where('mdx_brand_id', (int)$brandId);
        }

        // Eager load brand relationship after filters
        $query->with('brand');

        $orderBy = $request->get('order_by', 'sort_order');
        $orderDir = $request->get('order_dir', 'asc');
        $query->orderBy($orderBy, $orderDir);

        $perPage = $request->get('per_page', 15);
        $categories = $query->paginate($perPage)->withQueryString();
        $brands = Brand::active()->get();

        return view('admin.categories.index', compact('categories', 'brands'));
    }

    public function create()
    {
        $brands = Brand::active()->get();
        return view('admin.categories.create', compact('brands'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mdx_brand_id' => ['required', 'exists:mdx_brands,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if (empty($validated['slug']) && !empty($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Check unique slug per brand
        $originalSlug = $validated['slug'];
        $counter = 1;
        while (Category::where('mdx_brand_id', $validated['mdx_brand_id'])
            ->where('slug', $validated['slug'])
            ->exists()) {
            $validated['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Handle checkbox
        $validated['is_active'] = (bool)($request->input('is_active', 0));

        if (!isset($validated['sort_order'])) {
            $lastOrder = Category::where('mdx_brand_id', $validated['mdx_brand_id'])->max('sort_order') ?? 0;
            $validated['sort_order'] = $lastOrder + 1;
        }

        Category::create($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit(Category $category)
    {
        $brands = Brand::active()->get();
        return view('admin.categories.edit', compact('category', 'brands'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'mdx_brand_id' => ['required', 'exists:mdx_brands,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if (empty($validated['slug']) && $validated['name'] !== $category->name) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Check unique slug per brand (exclude current category)
        if (isset($validated['slug'])) {
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (Category::where('mdx_brand_id', $validated['mdx_brand_id'])
                ->where('slug', $validated['slug'])
                ->where('id', '!=', $category->id)
                ->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        // Handle checkbox
        $validated['is_active'] = (bool)($request->input('is_active', 0));

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}

