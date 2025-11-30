@extends('layouts.admin')

@section('title', 'Create Brand - DINE.CO.ID')

@section('page-title', 'Create Brand')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.brands.store') }}" method="POST">
            @csrf

            <!-- Name -->
            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Name <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="{{ old('name') }}"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent @error('name') border-red-500 @enderror"
                >
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Slug -->
            <div class="mb-6">
                <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
                    Slug <span class="text-gray-400 text-xs">(auto-generated if empty)</span>
                </label>
                <input 
                    type="text" 
                    id="slug" 
                    name="slug" 
                    value="{{ old('slug') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent @error('slug') border-red-500 @enderror"
                    placeholder="Leave empty to auto-generate from name"
                >
                @error('slug')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Description
                </label>
                <textarea 
                    id="description" 
                    name="description" 
                    rows="4"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent @error('description') border-red-500 @enderror"
                >{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Logo -->
            <div class="mb-6">
                <label for="logo" class="block text-sm font-medium text-gray-700 mb-2">
                    Logo URL
                </label>
                <input 
                    type="text" 
                    id="logo" 
                    name="logo" 
                    value="{{ old('logo') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent @error('logo') border-red-500 @enderror"
                    placeholder="https://example.com/logo.png"
                >
                @error('logo')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div class="mb-6">
                <label class="flex items-center">
                    <input 
                        type="checkbox" 
                        name="is_active" 
                        value="1"
                        {{ old('is_active', true) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-gray-900 focus:ring-gray-900"
                    >
                    <span class="ml-2 text-sm font-medium text-gray-700">Active</span>
                </label>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.brands.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition-colors">
                    Create Brand
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

