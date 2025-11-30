<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\v1\Category;
use App\Models\v1\Menu;
use App\Models\v1\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $query = Menu::query();

        // Search with proper grouping
        $search = $request->input('search');
        if ($search && trim($search) !== '') {
            $searchTerm = trim($search);
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('slug', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%');
            });
        }

        // Active/Inactive status filter
        $status = $request->input('status');
        if ($status && $status !== '') {
            $query->where('is_active', $status === 'active');
        }

        // Available filter
        $available = $request->input('available');
        if ($available && $available !== '') {
            $query->where('is_available', $available === 'yes');
        }

        // Category filter
        $categoryId = $request->input('category_id');
        if ($categoryId && $categoryId !== '') {
            $query->whereHas('categories', function($q) use ($categoryId) {
                $q->where('mdx_categories.id', (int)$categoryId);
            });
        }

        // Eager load relationships
        $query->with('categories.brand');

        $orderBy = $request->get('order_by', 'sort_order');
        $orderDir = $request->get('order_dir', 'asc');
        $query->orderBy($orderBy, $orderDir);

        $perPage = $request->get('per_page', 15);
        $menus = $query->paginate($perPage)->withQueryString();
        
        $categories = Category::active()->with('brand')->orderBy('mdx_brand_id')->orderBy('sort_order')->get();

        return view('admin.menus.index', compact('menus', 'categories'));
    }

    public function create()
    {
        $categories = Category::active()->with('brand')->orderBy('mdx_brand_id')->orderBy('sort_order')->get();
        return view('admin.menus.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:mdx_menus,slug'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'image' => ['nullable', 'string', 'max:255'],
            'is_available' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['exists:mdx_categories,id'],
        ]);

        if (empty($validated['slug']) && !empty($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name']);
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (Menu::where('slug', $validated['slug'])->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        // Handle checkboxes
        $validated['is_active'] = (bool)($request->input('is_active', 0));
        $validated['is_available'] = (bool)($request->input('is_available', 0));

        $categories = $validated['categories'] ?? [];
        unset($validated['categories']);

        $menu = Menu::create($validated);

        if (!empty($categories)) {
            $menu->categories()->attach($categories);
        }

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu created successfully.');
    }

    public function edit(Menu $menu)
    {
        $menu->load('categories');
        $categories = Category::active()->with('brand')->orderBy('mdx_brand_id')->orderBy('sort_order')->get();
        return view('admin.menus.edit', compact('menu', 'categories'));
    }

    public function update(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:mdx_menus,slug,' . $menu->id],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'image' => ['nullable', 'string', 'max:255'],
            'is_available' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['exists:mdx_categories,id'],
        ]);

        if (empty($validated['slug']) && $validated['name'] !== $menu->name) {
            $validated['slug'] = Str::slug($validated['name']);
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (Menu::where('slug', $validated['slug'])->where('id', '!=', $menu->id)->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        // Handle checkboxes
        $validated['is_active'] = (bool)($request->input('is_active', 0));
        $validated['is_available'] = (bool)($request->input('is_available', 0));

        $categories = $validated['categories'] ?? [];
        unset($validated['categories']);

        $menu->update($validated);

        $menu->categories()->sync($categories);

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu updated successfully.');
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();
        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu deleted successfully.');
    }
}

