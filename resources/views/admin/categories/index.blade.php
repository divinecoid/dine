@extends('layouts.admin')

@section('title', 'Categories - DINE.CO.ID')

@section('page-title', 'Kategori')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Kategori</h1>
            <p class="text-sm text-gray-500 mt-1">Manage your menu categories</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add Kategori
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" action="{{ route('admin.categories.index') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or slug..." 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent">
            </div>
            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900">
                <option value="">All Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            <select name="brand_id" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900">
                <option value="">All Brands</option>
                @foreach($brands as $brand)
                    <option value="{{ $brand->id }}" {{ (string)request('brand_id') === (string)$brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-6 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800">Filter</button>
            @if(request('search') || request('status') || request('brand_id'))
                <a href="{{ route('admin.categories.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">Reset</a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Slug</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Brand</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sort Order</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Active</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($categories as $category)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $category->name }}</div>
                                @if($category->description)
                                    <div class="text-xs text-gray-500 mt-1 line-clamp-1">{{ Str::limit($category->description, 50) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">{{ $category->slug }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $category->brand->name ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $category->sort_order ?? 0 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($category->is_active)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Inactive</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="text-gray-600 hover:text-gray-900">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Delete this category?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-semibold text-gray-900">No categories found</h3>
                                <p class="mt-1 text-sm text-gray-500">Get started by creating a new category.</p>
                                <div class="mt-6">
                                    <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition-colors">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Add Kategori
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($categories->hasPages())
            <div class="bg-white px-4 py-3 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Showing {{ $categories->firstItem() ?? 0 }} to {{ $categories->lastItem() ?? 0 }} of {{ $categories->total() }}
                    </div>
                    <div class="flex space-x-2">
                        @if($categories->onFirstPage())
                            <span class="px-4 py-2 text-gray-400 bg-gray-50 rounded-lg cursor-not-allowed">Previous</span>
                        @else
                            <a href="{{ $categories->previousPageUrl() }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Previous</a>
                        @endif
                        @foreach(range(1, min(5, $categories->lastPage())) as $page)
                            @if($page == $categories->currentPage())
                                <span class="px-4 py-2 bg-gray-900 text-white rounded-lg">{{ $page }}</span>
                            @else
                                <a href="{{ $categories->url($page) }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">{{ $page }}</a>
                            @endif
                        @endforeach
                        @if($categories->hasMorePages())
                            <a href="{{ $categories->nextPageUrl() }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Next</a>
                        @else
                            <span class="px-4 py-2 text-gray-400 bg-gray-50 rounded-lg cursor-not-allowed">Next</span>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
