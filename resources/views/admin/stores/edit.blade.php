@extends('layouts.admin')

@section('title', 'Edit Store - DINE.CO.ID')

@section('page-title', 'Edit Store')

@section('content')
<div class="max-w-3xl">
    <div class="bg-[#161615] border border-[#3E3E3A] rounded-lg shadow p-6">
        <form action="{{ route('admin.stores.update', $store) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Brand -->
                <div class="md:col-span-2">
                    <label for="mdx_brand_id" class="block text-sm font-medium text-[#EDEDEC] mb-2">Brand <span class="text-[#F53003]">*</span></label>
                    <select id="mdx_brand_id" name="mdx_brand_id" required
                        class="w-full px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] rounded-lg focus:ring-[#F53003]/50 focus:border-[#F53003]/50 transition-colors @error('mdx_brand_id') border-[#F53003] @enderror">
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ old('mdx_brand_id', $store->mdx_brand_id) == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                    @error('mdx_brand_id')<p class="mt-1 text-sm text-[#F53003]">{{ $message }}</p>@enderror
                </div>

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-[#EDEDEC] mb-2">Name <span class="text-[#F53003]">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name', $store->name) }}" required
                        class="w-full px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] rounded-lg focus:ring-[#F53003]/50 focus:border-[#F53003]/50 transition-colors @error('name') border-[#F53003] @enderror">
                    @error('name')<p class="mt-1 text-sm text-[#F53003]">{{ $message }}</p>@enderror
                </div>

                <!-- Slug -->
                <div>
                    <label for="slug" class="block text-sm font-medium text-[#EDEDEC] mb-2">Slug</label>
                    <input type="text" id="slug" name="slug" value="{{ old('slug', $store->slug) }}"
                        class="w-full px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] placeholder-[#A1A09A] rounded-lg focus:ring-[#F53003]/50 focus:border-[#F53003]/50 transition-colors">
                </div>

                <!-- Address -->
                <div class="md:col-span-2">
                    <label for="address" class="block text-sm font-medium text-[#EDEDEC] mb-2">Address</label>
                    <textarea id="address" name="address" rows="2"
                        class="w-full px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] placeholder-[#A1A09A] rounded-lg focus:ring-[#F53003]/50 focus:border-[#F53003]/50 transition-colors">{{ old('address', $store->address) }}</textarea>
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-[#EDEDEC] mb-2">Phone</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $store->phone) }}"
                        class="w-full px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] placeholder-[#A1A09A] rounded-lg focus:ring-[#F53003]/50 focus:border-[#F53003]/50 transition-colors">
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-[#EDEDEC] mb-2">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $store->email) }}"
                        class="w-full px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] placeholder-[#A1A09A] rounded-lg focus:ring-[#F53003]/50 focus:border-[#F53003]/50 transition-colors">
                </div>

                <!-- Latitude & Longitude -->
                <div>
                    <label for="latitude" class="block text-sm font-medium text-[#EDEDEC] mb-2">Latitude</label>
                    <input type="number" step="0.00000001" id="latitude" name="latitude" value="{{ old('latitude', $store->latitude) }}"
                        class="w-full px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] placeholder-[#A1A09A] rounded-lg focus:ring-[#F53003]/50 focus:border-[#F53003]/50 transition-colors">
                </div>

                <div>
                    <label for="longitude" class="block text-sm font-medium text-[#EDEDEC] mb-2">Longitude</label>
                    <input type="number" step="0.00000001" id="longitude" name="longitude" value="{{ old('longitude', $store->longitude) }}"
                        class="w-full px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] placeholder-[#A1A09A] rounded-lg focus:ring-[#F53003]/50 focus:border-[#F53003]/50 transition-colors">
                </div>

                <!-- Image -->
                <div class="md:col-span-2">
                    <label for="image" class="block text-sm font-medium text-[#EDEDEC] mb-2">Image URL</label>
                    <input type="text" id="image" name="image" value="{{ old('image', $store->image) }}"
                        class="w-full px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] placeholder-[#A1A09A] rounded-lg focus:ring-[#F53003]/50 focus:border-[#F53003]/50 transition-colors">
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-[#EDEDEC] mb-2">Description</label>
                    <textarea id="description" name="description" rows="3"
                        class="w-full px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] placeholder-[#A1A09A] rounded-lg focus:ring-[#F53003]/50 focus:border-[#F53003]/50 transition-colors">{{ old('description', $store->description) }}</textarea>
                </div>

                <!-- Status -->
                <div class="md:col-span-2">
                    <label class="flex items-center">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $store->is_active ? '1' : '0') == '1' ? 'checked' : '' }}
                            class="rounded border-[#3E3E3A] bg-[#0a0a0a] text-[#F53003] focus:ring-[#F53003]/50 focus:border-[#F53003]/50">
                        <span class="ml-2 text-sm font-medium text-[#EDEDEC]">Active</span>
                    </label>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-4 mt-6">
                <a href="{{ route('admin.stores.index') }}" class="px-6 py-2 border border-[#3E3E3A] text-[#A1A09A] rounded-lg hover:bg-[#0a0a0a] transition-colors">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-[#F53003] text-white rounded-lg hover:bg-[#d42800] transition-colors">Update Store</button>
            </div>
        </form>
    </div>
</div>
@endsection

