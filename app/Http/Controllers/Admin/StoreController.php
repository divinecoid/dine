<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\v1\Brand;
use App\Models\v1\Store;
use App\Traits\HandlesUserAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StoreController extends Controller
{
    use HandlesUserAccess;

    public function index(Request $request)
    {
        $query = Store::accessibleBy(auth()->user());

        // Search with proper grouping
        $search = $request->input('search');
        if ($search && trim($search) !== '') {
            $searchTerm = trim($search);
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('slug', 'like', '%' . $searchTerm . '%');
            });
        }

        // Status filter
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

        $orderBy = $request->get('order_by', 'created_at');
        $orderDir = $request->get('order_dir', 'desc');
        $query->orderBy($orderBy, $orderDir);

        $perPage = $request->get('per_page', 15);
        $stores = $query->paginate($perPage)->withQueryString();
        $brands = Brand::accessibleBy(auth()->user())->active()->get();

        return view('admin.stores.index', compact('stores', 'brands'));
    }

    public function create()
    {
        $brands = Brand::accessibleBy(auth()->user())->active()->get();
        return view('admin.stores.create', compact('brands'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mdx_brand_id' => ['required', 'exists:mdx_brands,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:mdx_stores,slug'],
            'description' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'image' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if (empty($validated['slug']) && !empty($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name']);
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (Store::where('slug', $validated['slug'])->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        // Handle checkbox: if not checked, it will be 0, otherwise 1
        $validated['is_active'] = (bool)($request->input('is_active', 0));

        // Check if user can access the brand
        if (!$this->canAccessBrand($validated['mdx_brand_id'])) {
            abort(403, 'Unauthorized access to this brand.');
        }

        $store = Store::create($validated);

        // Assign store to user if store manager
        if (auth()->user()->isStoreManager()) {
            $store->update(['user_id' => auth()->id()]);
        }

        return redirect()->route('admin.stores.index')
            ->with('success', 'Store created successfully.');
    }

    public function edit(Store $store)
    {
        // Check access
        if (!$this->canAccessStore($store->id)) {
            abort(403, 'Unauthorized access to this store.');
        }

        $brands = Brand::accessibleBy(auth()->user())->active()->get();
        return view('admin.stores.edit', compact('store', 'brands'));
    }

    public function update(Request $request, Store $store)
    {
        // Check access
        if (!$this->canAccessStore($store->id)) {
            abort(403, 'Unauthorized access to this store.');
        }

        $validated = $request->validate([
            'mdx_brand_id' => ['required', 'exists:mdx_brands,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:mdx_stores,slug,' . $store->id],
            'description' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'image' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if (empty($validated['slug']) && $validated['name'] !== $store->name) {
            $validated['slug'] = Str::slug($validated['name']);
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (Store::where('slug', $validated['slug'])->where('id', '!=', $store->id)->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        // Handle checkbox: if not checked, it will be 0, otherwise 1
        $validated['is_active'] = (bool)($request->input('is_active', 0));

        // Check if user can access the brand
        if (!$this->canAccessBrand($validated['mdx_brand_id'])) {
            abort(403, 'Unauthorized access to this brand.');
        }

        $store->update($validated);

        return redirect()->route('admin.stores.index')
            ->with('success', 'Store updated successfully.');
    }

    public function destroy(Store $store)
    {
        // Check access
        if (!$this->canAccessStore($store->id)) {
            abort(403, 'Unauthorized access to this store.');
        }

        $store->delete();
        return redirect()->route('admin.stores.index')
            ->with('success', 'Store deleted successfully.');
    }
}
