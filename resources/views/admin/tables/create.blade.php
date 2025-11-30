@extends('layouts.admin')

@section('title', 'Create Table - DINE.CO.ID')

@section('page-title', 'Create Meja')

@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.tables.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Store -->
                <div class="md:col-span-2">
                    <label for="mdx_store_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Store <span class="text-red-500">*</span>
                    </label>
                    <select id="mdx_store_id" name="mdx_store_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 @error('mdx_store_id') border-red-500 @enderror">
                        <option value="">Select Store</option>
                        @foreach($stores as $store)
                            <option value="{{ $store->id }}" {{ old('mdx_store_id') == $store->id ? 'selected' : '' }}>
                                {{ $store->name }} ({{ $store->brand->name ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                    @error('mdx_store_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Table Number -->
                <div>
                    <label for="table_number" class="block text-sm font-medium text-gray-700 mb-2">
                        Table Number <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="table_number" name="table_number" value="{{ old('table_number') }}" min="1" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 @error('table_number') border-red-500 @enderror">
                    @error('table_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Name <span class="text-gray-400 text-xs">(auto: Meja {number})</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 @error('name') border-red-500 @enderror"
                        placeholder="Leave empty for auto-name">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Capacity -->
                <div>
                    <label for="capacity" class="block text-sm font-medium text-gray-700 mb-2">Capacity</label>
                    <input type="number" id="capacity" name="capacity" value="{{ old('capacity') }}" min="1"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900">
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Table Status <span class="text-red-500">*</span>
                    </label>
                    <select id="status" name="status" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 @error('status') border-red-500 @enderror">
                        <option value="available" {{ old('status') === 'available' ? 'selected' : '' }}>Available</option>
                        <option value="occupied" {{ old('status') === 'occupied' ? 'selected' : '' }}>Occupied</option>
                        <option value="reserved" {{ old('status') === 'reserved' ? 'selected' : '' }}>Reserved</option>
                        <option value="maintenance" {{ old('status') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Zone -->
                <div>
                    <label for="zone" class="block text-sm font-medium text-gray-700 mb-2">Zone</label>
                    <input type="text" id="zone" name="zone" value="{{ old('zone') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900"
                        placeholder="e.g., Indoor, Outdoor, VIP">
                </div>

                <!-- Floor -->
                <div>
                    <label for="floor" class="block text-sm font-medium text-gray-700 mb-2">Floor</label>
                    <input type="number" id="floor" name="floor" value="{{ old('floor') }}" min="1"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900"
                        placeholder="1, 2, etc">
                </div>

                <!-- Sort Order -->
                <div>
                    <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">Sort Order</label>
                    <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900">
                </div>

                <!-- Notes -->
                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea id="notes" name="notes" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900">{{ old('notes') }}</textarea>
                </div>

                <!-- Active Status -->
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
                <a href="{{ route('admin.tables.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800">Create Meja</button>
            </div>
        </form>
    </div>
</div>
@endsection

