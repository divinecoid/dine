@extends('layouts.admin')

@section('title', 'Menus - DINE.CO.ID')

@section('page-title', 'Menu')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-[#EDEDEC]">Menu</h1>
            <p class="text-sm text-[#A1A09A] mt-1">Manage your menus</p>
        </div>
        <a href="{{ route('admin.menus.create') }}" class="inline-flex items-center px-4 py-2 bg-[#F53003] text-white rounded-lg hover:bg-[#d42800] transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add Menu
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-500/20 border border-green-500/50 text-green-400 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-[#161615] border border-[#3E3E3A] rounded-lg shadow p-6">
        <form method="GET" action="{{ route('admin.menus.index') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, slug, or description..." 
                    class="w-full px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] placeholder-[#A1A09A] rounded-lg focus:ring-2 focus:ring-[#F53003]/50 focus:border-[#F53003]/50 transition-colors">
            </div>
            <select name="status" class="px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] rounded-lg focus:ring-2 focus:ring-[#F53003]/50 focus:border-[#F53003]/50">
                <option value="">All Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            <select name="available" class="px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] rounded-lg focus:ring-2 focus:ring-[#F53003]/50 focus:border-[#F53003]/50">
                <option value="">All Availability</option>
                <option value="yes" {{ request('available') === 'yes' ? 'selected' : '' }}>Available</option>
                <option value="no" {{ request('available') === 'no' ? 'selected' : '' }}>Not Available</option>
            </select>
            <select name="category_id" class="px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] rounded-lg focus:ring-2 focus:ring-[#F53003]/50 focus:border-[#F53003]/50">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ (string)request('category_id') === (string)$category->id ? 'selected' : '' }}>
                        {{ $category->name }} ({{ $category->brand->name ?? '-' }})
                    </option>
                @endforeach
            </select>
            <button type="submit" class="px-6 py-2 bg-[#F53003] text-white rounded-lg hover:bg-[#d42800]">Filter</button>
            @if(request('search') || request('status') || request('available') || request('category_id'))
                <a href="{{ route('admin.menus.index') }}" class="px-6 py-2 border border-[#3E3E3A] text-[#A1A09A] rounded-lg hover:bg-[#0a0a0a]">Reset</a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="bg-[#161615] border border-[#3E3E3A] rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-[#3E3E3A]">
                <thead class="bg-[#0a0a0a]">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#A1A09A] uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#A1A09A] uppercase">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#A1A09A] uppercase">Categories</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#A1A09A] uppercase">Availability</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#A1A09A] uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-[#A1A09A] uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-[#161615] border border-[#3E3E3A] divide-y divide-[#3E3E3A]">
                    @forelse($menus as $menu)
                        <tr class="hover:bg-[#0a0a0a] transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    @if($menu->image)
                                        <img src="{{ $menu->image }}" alt="{{ $menu->name }}" class="w-12 h-12 rounded-lg object-cover mr-3">
                                    @else
                                        <div class="w-12 h-12 rounded-lg bg-[#0a0a0a] flex items-center justify-center mr-3">
                                            <svg class="w-6 h-6 text-[#3E3E3A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="text-sm font-medium text-[#EDEDEC]">{{ $menu->name }}</div>
                                        @if($menu->description)
                                            <div class="text-xs text-[#A1A09A] mt-1 line-clamp-1">{{ Str::limit($menu->description, 50) }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-[#EDEDEC]">Rp {{ number_format($menu->price, 0, ',', '.') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @forelse($menu->categories->take(3) as $category)
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-blue-500/20 text-blue-400 border border-blue-500/30">
                                            {{ $category->name }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-[#3E3E3A]">No categories</span>
                                    @endforelse
                                    @if($menu->categories->count() > 3)
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-[#3E3E3A] text-[#EDEDEC]">
                                            +{{ $menu->categories->count() - 3 }} more
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($menu->is_available)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-500/20 text-green-400 border border-green-500/30">Available</span>
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-[#F53003]/20 text-[#F53003] border border-[#F53003]/30">Not Available</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($menu->is_active)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-500/20 text-green-400 border border-green-500/30">Active</span>
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-[#3E3E3A] text-[#EDEDEC]">Inactive</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <a href="{{ route('admin.menus.edit', $menu) }}" class="text-[#A1A09A] hover:text-[#EDEDEC]">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.menus.destroy', $menu) }}" method="POST" class="inline" onsubmit="return confirm('Delete this menu?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-[#F53003] hover:text-[#d42800]">
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-semibold text-[#EDEDEC]">No menus found</h3>
                                <p class="mt-1 text-sm text-[#A1A09A]">Get started by creating a new menu.</p>
                                <div class="mt-6">
                                    <a href="{{ route('admin.menus.create') }}" class="inline-flex items-center px-4 py-2 bg-[#F53003] text-white rounded-lg hover:bg-[#d42800] transition-colors">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Add Menu
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($menus->hasPages())
            <div class="bg-[#0a0a0a] px-4 py-3 border-t border-[#3E3E3A]">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-[#A1A09A]">
                        Showing {{ $menus->firstItem() ?? 0 }} to {{ $menus->lastItem() ?? 0 }} of {{ $menus->total() }}
                    </div>
                    <div class="flex space-x-2">
                        @if($menus->onFirstPage())
                            <span class="px-4 py-2 text-[#3E3E3A] bg-[#0a0a0a] rounded-lg cursor-not-allowed">Previous</span>
                        @else
                            <a href="{{ $menus->previousPageUrl() }}" class="px-4 py-2 border border-[#3E3E3A] text-[#A1A09A] rounded-lg hover:bg-[#161615] transition-colors">Previous</a>
                        @endif
                        @foreach(range(1, min(5, $menus->lastPage())) as $page)
                            @if($page == $menus->currentPage())
                                <span class="px-4 py-2 bg-[#F53003] text-white rounded-lg">{{ $page }}</span>
                            @else
                                <a href="{{ $menus->url($page) }}" class="px-4 py-2 border border-[#3E3E3A] text-[#A1A09A] rounded-lg hover:bg-[#161615] transition-colors">{{ $page }}</a>
                            @endif
                        @endforeach
                        @if($menus->hasMorePages())
                            <a href="{{ $menus->nextPageUrl() }}" class="px-4 py-2 border border-[#3E3E3A] text-[#A1A09A] rounded-lg hover:bg-[#161615] transition-colors">Next</a>
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
