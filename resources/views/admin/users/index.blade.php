@extends('layouts.admin')

@section('title', 'Users - DINE.CO.ID')

@section('page-title', 'Users')

@section('content')
<div class="space-y-6">
    <!-- Header with Add Button -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-[#EDEDEC]">Users</h1>
            <p class="text-sm text-[#A1A09A] mt-1">
                @if($isBrandOwner ?? false)
                    Manage users (Store Manager, Chef, Waiter, Kasir)
                @else
                    Manage staff users (Chef, Waiter, Kasir)
                @endif
            </p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="inline-flex items-center px-4 py-2 bg-[#F53003] text-white rounded-lg hover:bg-[#d42800] transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add User
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
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap gap-4">
            <!-- Search -->
            <div class="flex-1 min-w-[200px]">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}"
                    placeholder="Search by name, email, or phone..." 
                    class="w-full px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] placeholder-[#A1A09A] rounded-lg focus:ring-2 focus:ring-[#F53003]/50 focus:border-[#F53003]/50 transition-colors"
                >
            </div>

            <!-- Role Filter -->
            <select name="role" class="px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] rounded-lg focus:ring-2 focus:ring-[#F53003]/50 focus:border-[#F53003]/50">
                <option value="">All Roles</option>
                @if($isBrandOwner ?? false)
                    <option value="store_manager" {{ request('role') === 'store_manager' ? 'selected' : '' }}>Store Manager</option>
                @endif
                <option value="chef" {{ request('role') === 'chef' ? 'selected' : '' }}>Chef</option>
                <option value="waiter" {{ request('role') === 'waiter' ? 'selected' : '' }}>Waiter</option>
                <option value="kasir" {{ request('role') === 'kasir' ? 'selected' : '' }}>Kasir</option>
            </select>

            <!-- Store Filter -->
            <select name="store_id" class="px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] rounded-lg focus:ring-2 focus:ring-[#F53003]/50 focus:border-[#F53003]/50">
                <option value="">All Stores</option>
                @foreach($stores as $store)
                    <option value="{{ $store->id }}" {{ request('store_id') == $store->id ? 'selected' : '' }}>
                        {{ $store->name }} ({{ $store->brand->name ?? 'N/A' }})
                    </option>
                @endforeach
            </select>

            <!-- Submit Button -->
            <button type="submit" class="px-6 py-2 bg-[#F53003] text-white rounded-lg hover:bg-[#d42800] transition-colors">
                Filter
            </button>

            <!-- Reset Button -->
            @if(request('search') || request('role') || request('store_id'))
                <a href="{{ route('admin.users.index') }}" class="px-6 py-2 border border-[#3E3E3A] text-[#A1A09A] rounded-lg hover:bg-[#0a0a0a] transition-colors">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-[#161615] border border-[#3E3E3A] rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-[#3E3E3A]">
                <thead class="bg-[#0a0a0a]">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#A1A09A] uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#A1A09A] uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#A1A09A] uppercase tracking-wider">Phone</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#A1A09A] uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#A1A09A] uppercase tracking-wider">Store</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#A1A09A] uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-[#A1A09A] uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-[#161615] border border-[#3E3E3A] divide-y divide-[#3E3E3A]">
                    @forelse($users as $user)
                        <tr class="hover:bg-[#0a0a0a] transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-[#EDEDEC]">{{ $user->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-[#A1A09A]">{{ $user->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-[#A1A09A]">{{ $user->phone }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($user->role === 'store_manager')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-purple-500/20 text-purple-400 border border-purple-500/30">Store Manager</span>
                                @elseif($user->role === 'chef')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-orange-500/20 text-orange-400 border border-orange-500/30">Chef</span>
                                @elseif($user->role === 'waiter')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-500/20 text-blue-400 border border-blue-500/30">Waiter</span>
                                @elseif($user->role === 'kasir')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-500/20 text-green-400 border border-green-500/30">Kasir</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($user->role === 'store_manager')
                                    <div class="text-sm text-[#EDEDEC]">{{ $user->store->name ?? '-' }}</div>
                                    @if($user->store && $user->store->brand)
                                        <div class="text-xs text-[#A1A09A]">{{ $user->store->brand->name }}</div>
                                    @endif
                                @else
                                    <div class="text-sm text-[#EDEDEC]">{{ $user->workStore->name ?? '-' }}</div>
                                    @if($user->workStore && $user->workStore->brand)
                                        <div class="text-xs text-[#A1A09A]">{{ $user->workStore->brand->name }}</div>
                                    @endif
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-[#A1A09A]">
                                {{ $user->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="text-[#A1A09A] hover:text-[#EDEDEC]">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                        @csrf
                                        @method('DELETE')
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
                            <td colspan="7" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-[#3E3E3A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-semibold text-[#EDEDEC]">No users found</h3>
                                <p class="mt-1 text-sm text-[#A1A09A]">Get started by creating a new user.</p>
                                <div class="mt-6">
                                    <a href="{{ route('admin.users.create') }}" class="inline-flex items-center px-4 py-2 bg-[#F53003] text-white rounded-lg hover:bg-[#d42800] transition-colors">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Add User
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
            <div class="bg-[#0a0a0a] px-4 py-3 border-t border-[#3E3E3A] sm:px-6">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-[#A1A09A]">
                        Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} results
                    </div>
                    <div class="flex space-x-2">
                        @if($users->onFirstPage())
                            <span class="px-4 py-2 text-[#3E3E3A] bg-[#0a0a0a] rounded-lg cursor-not-allowed">Previous</span>
                        @else
                            <a href="{{ $users->previousPageUrl() }}" class="px-4 py-2 bg-[#161615] border border-[#3E3E3A] border border-[#3E3E3A] text-[#A1A09A] rounded-lg hover:bg-[#0a0a0a]">Previous</a>
                        @endif
                        
                        @foreach(range(1, min(5, $users->lastPage())) as $page)
                            @if($page == $users->currentPage())
                                <span class="px-4 py-2 bg-[#F53003] text-white rounded-lg">{{ $page }}</span>
                            @else
                                <a href="{{ $users->url($page) }}" class="px-4 py-2 bg-[#161615] border border-[#3E3E3A] border border-[#3E3E3A] text-[#A1A09A] rounded-lg hover:bg-[#0a0a0a]">{{ $page }}</a>
                            @endif
                        @endforeach
                        
                        @if($users->hasMorePages())
                            <a href="{{ $users->nextPageUrl() }}" class="px-4 py-2 bg-[#161615] border border-[#3E3E3A] border border-[#3E3E3A] text-[#A1A09A] rounded-lg hover:bg-[#0a0a0a]">Next</a>
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
