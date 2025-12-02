@extends('layouts.admin')

@section('title', 'Edit Table - DINE.CO.ID')

@section('page-title', 'Edit Meja')

@section('content')
<div class="max-w-3xl">
    <div class="bg-[#161615] border border-[#3E3E3A] rounded-lg shadow p-6">
        <form action="{{ route('admin.tables.update', $table) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Store -->
                <div class="md:col-span-2">
                    <label for="mdx_store_id" class="block text-sm font-medium text-[#EDEDEC] mb-2">
                        Store <span class="text-[#F53003]">*</span>
                    </label>
                    <select id="mdx_store_id" name="mdx_store_id" required
                        class="w-full px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] rounded-lg focus:ring-2 focus:ring-[#F53003]/50 focus:border-[#F53003]/50 transition-colors @error('mdx_store_id') border-[#F53003] @enderror">
                        @foreach($stores as $store)
                            <option value="{{ $store->id }}" {{ old('mdx_store_id', $table->mdx_store_id) == $store->id ? 'selected' : '' }}>
                                {{ $store->name }} ({{ $store->brand->name ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                    @error('mdx_store_id')
                        <p class="mt-1 text-sm text-[#F53003]">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Table Number -->
                <div>
                    <label for="table_number" class="block text-sm font-medium text-[#EDEDEC] mb-2">
                        Table Number <span class="text-[#F53003]">*</span>
                    </label>
                    <input type="number" id="table_number" name="table_number" value="{{ old('table_number', $table->table_number) }}" min="1" required
                        class="w-full px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] rounded-lg focus:ring-2 focus:ring-[#F53003]/50 focus:border-[#F53003]/50 transition-colors @error('table_number') border-[#F53003] @enderror">
                    @error('table_number')
                        <p class="mt-1 text-sm text-[#F53003]">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-[#EDEDEC] mb-2">Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $table->name) }}"
                        class="w-full px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] placeholder-[#A1A09A] rounded-lg focus:ring-2 focus:ring-[#F53003]/50 focus:border-[#F53003]/50 transition-colors">
                </div>

                <!-- Capacity -->
                <div>
                    <label for="capacity" class="block text-sm font-medium text-[#EDEDEC] mb-2">Capacity</label>
                    <input type="number" id="capacity" name="capacity" value="{{ old('capacity', $table->capacity) }}" min="1"
                        class="w-full px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] rounded-lg focus:ring-2 focus:ring-[#F53003]/50 focus:border-[#F53003]/50 transition-colors">
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-[#EDEDEC] mb-2">
                        Table Status <span class="text-[#F53003]">*</span>
                    </label>
                    <select id="status" name="status" required
                        class="w-full px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] rounded-lg focus:ring-2 focus:ring-[#F53003]/50 focus:border-[#F53003]/50 transition-colors @error('status') border-[#F53003] @enderror">
                        <option value="available" {{ old('status', $table->status) === 'available' ? 'selected' : '' }}>Available</option>
                        <option value="occupied" {{ old('status', $table->status) === 'occupied' ? 'selected' : '' }}>Occupied</option>
                        <option value="reserved" {{ old('status', $table->status) === 'reserved' ? 'selected' : '' }}>Reserved</option>
                        <option value="maintenance" {{ old('status', $table->status) === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-[#F53003]">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Zone -->
                <div>
                    <label for="zone" class="block text-sm font-medium text-[#EDEDEC] mb-2">Zone</label>
                    <input type="text" id="zone" name="zone" value="{{ old('zone', $table->zone) }}"
                        class="w-full px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] placeholder-[#A1A09A] rounded-lg focus:ring-2 focus:ring-[#F53003]/50 focus:border-[#F53003]/50 transition-colors"
                        placeholder="e.g., Indoor, Outdoor, VIP">
                </div>

                <!-- Floor -->
                <div>
                    <label for="floor" class="block text-sm font-medium text-[#EDEDEC] mb-2">Floor</label>
                    <input type="number" id="floor" name="floor" value="{{ old('floor', $table->floor) }}" min="1"
                        class="w-full px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] placeholder-[#A1A09A] rounded-lg focus:ring-2 focus:ring-[#F53003]/50 focus:border-[#F53003]/50 transition-colors"
                        placeholder="1, 2, etc">
                </div>

                <!-- Sort Order -->
                <div>
                    <label for="sort_order" class="block text-sm font-medium text-[#EDEDEC] mb-2">Sort Order</label>
                    <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $table->sort_order) }}"
                        class="w-full px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] rounded-lg focus:ring-2 focus:ring-[#F53003]/50 focus:border-[#F53003]/50 transition-colors">
                </div>

                <!-- Notes -->
                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-[#EDEDEC] mb-2">Notes</label>
                    <textarea id="notes" name="notes" rows="3"
                        class="w-full px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] placeholder-[#A1A09A] rounded-lg focus:ring-2 focus:ring-[#F53003]/50 focus:border-[#F53003]/50 transition-colors">{{ old('notes', $table->notes) }}</textarea>
                </div>

                <!-- Active Status -->
                <div class="md:col-span-2">
                    <label class="flex items-center">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $table->is_active) == '1' ? 'checked' : '' }}
                            class="rounded border-[#3E3E3A] bg-[#0a0a0a] text-[#F53003] focus:ring-[#F53003]/50 focus:border-[#F53003]/50">
                        <span class="ml-2 text-sm font-medium text-[#EDEDEC]">Active</span>
                    </label>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-4 mt-6">
                <a href="{{ route('admin.tables.index') }}" class="px-6 py-2 border border-[#3E3E3A] text-[#A1A09A] rounded-lg hover:bg-[#0a0a0a] transition-colors">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-[#F53003] text-white rounded-lg hover:bg-[#d42800] transition-colors">Update Meja</button>
            </div>
        </form>
    </div>
</div>
@endsection
