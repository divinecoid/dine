@extends('layouts.admin')

@section('title', 'Brands - DINE.CO.ID')

@section('page-title', 'Brands')

@section('content')
<div class="space-y-6">
    <!-- Header with Add Button -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-[#EDEDEC]">Brands</h1>
            <p class="text-sm text-[#A1A09A] mt-1">Manage your brands</p>
        </div>
        <a href="{{ route('admin.brands.create') }}" class="inline-flex items-center px-4 py-2 bg-[#F53003] text-white rounded-lg hover:bg-[#d42800] transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add Brand
        </a>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-500/20 border border-green-500/50 text-green-400 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <!-- Filters and Search -->
    <div class="bg-[#161615] border border-[#3E3E3A] rounded-lg shadow p-6">
        <form method="GET" action="{{ route('admin.brands.index') }}" class="flex flex-wrap gap-4">
            <!-- Search -->
            <div class="flex-1 min-w-[200px]">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}"
                    placeholder="Search by name or slug..." 
                    class="w-full px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] placeholder-[#A1A09A] rounded-lg focus:ring-2 focus:ring-[#F53003]/50 focus:border-[#F53003]/50 transition-colors"
                >
            </div>

            <!-- Status Filter -->
            <select name="status" class="px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] rounded-lg focus:ring-2 focus:ring-[#F53003]/50 focus:border-[#F53003]/50">
                <option value="">All Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>

            <!-- Submit Button -->
            <button type="submit" class="px-6 py-2 bg-[#F53003] text-white rounded-lg hover:bg-[#d42800] transition-colors">
                Filter
            </button>

            <!-- Reset Button -->
            @if(request('search') || request('status'))
                <a href="{{ route('admin.brands.index') }}" class="px-6 py-2 border border-[#3E3E3A] text-[#A1A09A] rounded-lg hover:bg-[#0a0a0a] transition-colors">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Brands Table -->
    <div class="bg-[#161615] border border-[#3E3E3A] rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-[#3E3E3A]">
                <thead class="bg-[#0a0a0a]">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#A1A09A] uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#A1A09A] uppercase tracking-wider">Slug</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#A1A09A] uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#A1A09A] uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#A1A09A] uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-[#A1A09A] uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-[#161615] divide-y divide-[#3E3E3A]">
                    @forelse($brands as $brand)
                        <tr class="hover:bg-[#0a0a0a] transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-[#EDEDEC]">{{ $brand->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-[#A1A09A]">{{ $brand->slug ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-[#A1A09A]">
                                    @if($brand->description)
                                        {{ \Illuminate\Support\Str::limit($brand->description, 50) }}
                                    @else
                                        -
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($brand->is_active)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-500/20 text-green-400 border border-green-500/30">Active</span>
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-[#3E3E3A] text-[#A1A09A] border border-[#3E3E3A]">Inactive</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-[#A1A09A]">
                                {{ $brand->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <a href="{{ route('admin.brands.edit', $brand) }}" class="text-[#A1A09A] hover:text-[#F53003] transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.brands.destroy', $brand) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this brand?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-[#F53003] hover:text-[#d42800] transition-colors">
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
                                <svg class="mx-auto h-12 w-12 text-[#3E3E3A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-semibold text-[#EDEDEC]">No brands found</h3>
                                <p class="mt-1 text-sm text-[#A1A09A]">Get started by creating a new brand.</p>
                                <div class="mt-6">
                                    <a href="{{ route('admin.brands.create') }}" class="inline-flex items-center px-4 py-2 bg-[#F53003] text-white rounded-lg hover:bg-[#d42800] transition-colors">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Add Brand
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($brands->hasPages())
            <div class="bg-[#0a0a0a] px-4 py-3 border-t border-[#3E3E3A] sm:px-6">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-[#A1A09A]">
                        Showing {{ $brands->firstItem() ?? 0 }} to {{ $brands->lastItem() ?? 0 }} of {{ $brands->total() }} results
                    </div>
                    <div class="flex space-x-2">
                        @if($brands->onFirstPage())
                            <span class="px-4 py-2 text-[#3E3E3A] bg-[#0a0a0a] rounded-lg cursor-not-allowed">Previous</span>
                        @else
                            <a href="{{ $brands->previousPageUrl() }}" class="px-4 py-2 border border-[#3E3E3A] text-[#A1A09A] rounded-lg hover:bg-[#161615] transition-colors">Previous</a>
                        @endif
                        
                        @foreach(range(1, min(5, $brands->lastPage())) as $page)
                            @if($page == $brands->currentPage())
                                <span class="px-4 py-2 bg-[#F53003] text-white rounded-lg">{{ $page }}</span>
                            @else
                                <a href="{{ $brands->url($page) }}" class="px-4 py-2 border border-[#3E3E3A] text-[#A1A09A] rounded-lg hover:bg-[#161615] transition-colors">{{ $page }}</a>
                            @endif
                        @endforeach
                        
                        @if($brands->hasMorePages())
                            <a href="{{ $brands->nextPageUrl() }}" class="px-4 py-2 border border-[#3E3E3A] text-[#A1A09A] rounded-lg hover:bg-[#161615] transition-colors">Next</a>
                        @else
                            <span class="px-4 py-2 text-[#3E3E3A] bg-[#0a0a0a] rounded-lg cursor-not-allowed">Next</span>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
