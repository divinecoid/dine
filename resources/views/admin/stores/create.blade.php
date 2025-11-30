@extends('layouts.admin')

@section('title', 'Create Store - DINE.CO.ID')

@section('page-title', 'Create Store')

@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.stores.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Brand -->
                <div class="md:col-span-2">
                    <label for="mdx_brand_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Brand <span class="text-red-500">*</span>
                    </label>
                    <select id="mdx_brand_id" name="mdx_brand_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 @error('mdx_brand_id') border-red-500 @enderror">
                        <option value="">Select Brand</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ old('mdx_brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                    @error('mdx_brand_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Name <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 @error('name') border-red-500 @enderror">
                    @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Slug -->
                <div>
                    <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">Slug <span class="text-gray-400 text-xs">(auto)</span></label>
                    <input type="text" id="slug" name="slug" value="{{ old('slug') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 @error('slug') border-red-500 @enderror">
                    @error('slug')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Address -->
                <div class="md:col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                    <textarea id="address" name="address" rows="2"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900">{{ old('address') }}</textarea>
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900">
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900">
                </div>

                <!-- Latitude -->
                <div>
                    <label for="latitude" class="block text-sm font-medium text-gray-700 mb-2">Latitude</label>
                    <input type="number" step="0.00000001" id="latitude" name="latitude" value="{{ old('latitude') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900">
                </div>

                <!-- Longitude -->
                <div>
                    <label for="longitude" class="block text-sm font-medium text-gray-700 mb-2">Longitude</label>
                    <input type="number" step="0.00000001" id="longitude" name="longitude" value="{{ old('longitude') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900">
                </div>

                <!-- Image -->
                <div class="md:col-span-2">
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Image URL</label>
                    <input type="text" id="image" name="image" value="{{ old('image') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900">
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea id="description" name="description" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900">{{ old('description') }}</textarea>
                </div>

                <!-- Status -->
                <div class="md:col-span-2">
                    <label class="flex items-center">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}
                            class="rounded border-gray-300 text-gray-900 focus:ring-gray-900">
                        <span class="ml-2 text-sm font-medium text-gray-700">Active</span>
                    </label>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-4 mt-6">
                <a href="{{ route('admin.stores.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800">Create Store</button>
            </div>
        </form>
    </div>
</div>
@endsection

