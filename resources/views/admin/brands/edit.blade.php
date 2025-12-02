@extends('layouts.admin')

@section('title', 'Edit Brand - DINE.CO.ID')

@section('page-title', 'Edit Brand')

@section('content')
<div class="max-w-2xl">
    <div class="bg-[#161615] border border-[#3E3E3A] rounded-lg shadow p-6">
        <form action="{{ route('admin.brands.update', $brand) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-[#EDEDEC] mb-2">
                    Name <span class="text-[#F53003]">*</span>
                </label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="{{ old('name', $brand->name) }}"
                    required
                    class="w-full px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] rounded-lg focus:ring-[#F53003]/50 focus:border-[#F53003]/50 transition-colors @error('name') border-[#F53003] @enderror"
                >
                @error('name')
                    <p class="mt-1 text-sm text-[#F53003]">{{ $message }}</p>
                @enderror
            </div>

            <!-- Slug -->
            <div class="mb-6">
                <label for="slug" class="block text-sm font-medium text-[#EDEDEC] mb-2">
                    Slug <span class="text-[#A1A09A] text-xs">(auto-generated if empty)</span>
                </label>
                <input 
                    type="text" 
                    id="slug" 
                    name="slug" 
                    value="{{ old('slug', $brand->slug) }}"
                    class="w-full px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] placeholder-[#A1A09A] rounded-lg focus:ring-[#F53003]/50 focus:border-[#F53003]/50 transition-colors @error('slug') border-[#F53003] @enderror"
                    placeholder="Leave empty to auto-generate from name"
                >
                @error('slug')
                    <p class="mt-1 text-sm text-[#F53003]">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-[#EDEDEC] mb-2">
                    Description
                </label>
                <textarea 
                    id="description" 
                    name="description" 
                    rows="4"
                    class="w-full px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] placeholder-[#A1A09A] rounded-lg focus:ring-[#F53003]/50 focus:border-[#F53003]/50 transition-colors @error('description') border-[#F53003] @enderror"
                >{{ old('description', $brand->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-[#F53003]">{{ $message }}</p>
                @enderror
            </div>

            <!-- Logo -->
            <div class="mb-6">
                <label for="logo" class="block text-sm font-medium text-[#EDEDEC] mb-2">
                    Logo URL
                </label>
                <input 
                    type="text" 
                    id="logo" 
                    name="logo" 
                    value="{{ old('logo', $brand->logo) }}"
                    class="w-full px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] placeholder-[#A1A09A] rounded-lg focus:ring-[#F53003]/50 focus:border-[#F53003]/50 transition-colors @error('logo') border-[#F53003] @enderror"
                    placeholder="https://example.com/logo.png"
                >
                @error('logo')
                    <p class="mt-1 text-sm text-[#F53003]">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div class="mb-6">
                <label class="flex items-center">
                    <input 
                        type="checkbox" 
                        name="is_active" 
                        value="1"
                        {{ old('is_active', $brand->is_active) ? 'checked' : '' }}
                        class="rounded border-[#3E3E3A] text-[#EDEDEC] focus:ring-gray-900"
                    >
                    <span class="ml-2 text-sm font-medium text-[#EDEDEC]">Active</span>
                </label>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.brands.index') }}" class="px-6 py-2 border border-[#3E3E3A] text-[#A1A09A] rounded-lg hover:bg-[#0a0a0a] transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-[#F53003] text-white rounded-lg hover:bg-[#d42800] transition-colors">
                    Update Brand
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

