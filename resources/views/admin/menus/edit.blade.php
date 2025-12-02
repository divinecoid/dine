@extends('layouts.admin')

@section('title', 'Edit Menu - DINE.CO.ID')

@section('page-title', 'Edit Menu')

@section('content')
<div class="max-w-4xl">
    <div class="bg-[#161615] border border-[#3E3E3A] rounded-lg shadow p-6">
        <form action="{{ route('admin.menus.update', $menu) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-medium text-[#EDEDEC] mb-2">
                        Name <span class="text-[#F53003]">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name', $menu->name) }}" required
                        class="w-full px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] placeholder-[#A1A09A] rounded-lg focus:ring-[#F53003]/50 focus:border-[#F53003]/50 transition-colors @error('name') border-[#F53003] @enderror"
                        placeholder="e.g., Nasi Goreng Spesial">
                    @error('name')
                        <p class="mt-1 text-sm text-[#F53003]">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Slug -->
                <div class="md:col-span-2">
                    <label for="slug" class="block text-sm font-medium text-[#EDEDEC] mb-2">
                        Slug <span class="text-[#A1A09A] text-xs">(auto-generated from name if changed)</span>
                    </label>
                    <input type="text" id="slug" name="slug" value="{{ old('slug', $menu->slug) }}"
                        class="w-full px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] placeholder-[#A1A09A] rounded-lg focus:ring-[#F53003]/50 focus:border-[#F53003]/50 transition-colors @error('slug') border-[#F53003] @enderror">
                    @error('slug')
                        <p class="mt-1 text-sm text-[#F53003]">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-[#EDEDEC] mb-2">Description</label>
                    <textarea id="description" name="description" rows="3"
                        class="w-full px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] placeholder-[#A1A09A] rounded-lg focus:ring-[#F53003]/50 focus:border-[#F53003]/50 transition-colors @error('description') border-[#F53003] @enderror"
                        placeholder="Menu description...">{{ old('description', $menu->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-[#F53003]">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Price -->
                <div>
                    <label for="price" class="block text-sm font-medium text-[#EDEDEC] mb-2">
                        Price <span class="text-[#F53003]">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-[#A1A09A]">Rp</span>
                        <input type="number" id="price" name="price" value="{{ old('price', $menu->price) }}" step="0.01" min="0" required
                            class="w-full pl-12 pr-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] placeholder-[#A1A09A] rounded-lg focus:ring-[#F53003]/50 focus:border-[#F53003]/50 transition-colors @error('price') border-[#F53003] @enderror">
                    </div>
                    @error('price')
                        <p class="mt-1 text-sm text-[#F53003]">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Sort Order -->
                <div>
                    <label for="sort_order" class="block text-sm font-medium text-[#EDEDEC] mb-2">Sort Order</label>
                    <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $menu->sort_order) }}"
                        class="w-full px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] placeholder-[#A1A09A] rounded-lg focus:ring-[#F53003]/50 focus:border-[#F53003]/50 transition-colors">
                    <p class="mt-1 text-xs text-[#A1A09A]">Lower number appears first</p>
                </div>

                <!-- Image URL -->
                <div class="md:col-span-2">
                    <label for="image" class="block text-sm font-medium text-[#EDEDEC] mb-2">Image URL</label>
                    <input type="text" id="image" name="image" value="{{ old('image', $menu->image) }}"
                        class="w-full px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] placeholder-[#A1A09A] rounded-lg focus:ring-[#F53003]/50 focus:border-[#F53003]/50 transition-colors @error('image') border-[#F53003] @enderror"
                        placeholder="https://example.com/image.jpg">
                    @error('image')
                        <p class="mt-1 text-sm text-[#F53003]">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Categories -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-[#EDEDEC] mb-2">Categories</label>
                    <div class="border border-[#3E3E3A] bg-[#0a0a0a] rounded-lg p-4 max-h-64 overflow-y-auto">
                        @php
                            $selectedCategories = old('categories', $menu->categories->pluck('id')->toArray());
                            $categoriesByBrand = $categories->groupBy('mdx_brand_id');
                        @endphp
                        @foreach($categoriesByBrand as $brandId => $brandCategories)
                            <div class="mb-4 last:mb-0">
                                <div class="text-sm font-semibold text-[#EDEDEC] mb-2">
                                    {{ $brandCategories->first()->brand->name ?? 'Unknown Brand' }}
                                </div>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-2 ml-4">
                                    @foreach($brandCategories as $category)
                                        <label class="flex items-center">
                                            <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                                                {{ in_array($category->id, $selectedCategories) ? 'checked' : '' }}
                                                class="rounded border-[#3E3E3A] bg-[#0a0a0a] text-[#F53003] focus:ring-[#F53003]/50 focus:border-[#F53003]/50">
                                            <span class="ml-2 text-sm text-[#EDEDEC]">{{ $category->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                        @if($categories->isEmpty())
                            <p class="text-sm text-[#A1A09A]">No categories available. Please create categories first.</p>
                        @endif
                    </div>
                    @error('categories.*')
                        <p class="mt-1 text-sm text-[#F53003]">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status Checkboxes -->
                <div class="md:col-span-2">
                    <div class="flex space-x-6">
                        <label class="flex items-center">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $menu->is_active ? '1' : '0') == '1' ? 'checked' : '' }}
                                class="rounded border-[#3E3E3A] bg-[#0a0a0a] text-[#F53003] focus:ring-[#F53003]/50 focus:border-[#F53003]/50">
                            <span class="ml-2 text-sm font-medium text-[#EDEDEC]">Active</span>
                        </label>
                        <label class="flex items-center">
                            <input type="hidden" name="is_available" value="0">
                            <input type="checkbox" name="is_available" value="1" {{ old('is_available', $menu->is_available ? '1' : '0') == '1' ? 'checked' : '' }}
                                class="rounded border-[#3E3E3A] bg-[#0a0a0a] text-[#F53003] focus:ring-[#F53003]/50 focus:border-[#F53003]/50">
                            <span class="ml-2 text-sm font-medium text-[#EDEDEC]">Available</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-4 mt-6">
                <a href="{{ route('admin.menus.index') }}" class="px-6 py-2 border border-[#3E3E3A] text-[#A1A09A] rounded-lg hover:bg-[#0a0a0a] transition-colors">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-[#F53003] text-white rounded-lg hover:bg-[#d42800] transition-colors">Update Menu</button>
            </div>
        </form>
    </div>
</div>
@endsection









