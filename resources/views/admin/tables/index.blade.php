@extends('layouts.admin')

@section('title', 'Tables - DINE.CO.ID')

@section('page-title', 'Meja')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-[#EDEDEC]">Meja</h1>
            <p class="text-sm text-[#A1A09A] mt-1">Manage your tables</p>
        </div>
        <a href="{{ route('admin.tables.create') }}" class="inline-flex items-center px-4 py-2 bg-[#F53003] text-white rounded-lg hover:bg-[#d42800] transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add Meja
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-500/20 border border-green-500/50 text-green-400 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-[#161615] border border-[#3E3E3A] rounded-lg p-6">
        <form method="GET" action="{{ route('admin.tables.index') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or table number..." 
                    class="w-full px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] placeholder-[#A1A09A] rounded-lg focus:ring-2 focus:ring-[#F53003]/50 focus:border-[#F53003]/50 transition-colors">
            </div>
            <select name="status" class="px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] rounded-lg focus:ring-2 focus:ring-[#F53003]/50 focus:border-[#F53003]/50">
                <option value="">All Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            <select name="store_id" class="px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] rounded-lg focus:ring-2 focus:ring-[#F53003]/50 focus:border-[#F53003]/50">
                <option value="">All Stores</option>
                @foreach($stores as $store)
                    <option value="{{ $store->id }}" {{ (string)request('store_id') === (string)$store->id ? 'selected' : '' }}>
                        {{ $store->name }} ({{ $store->brand->name ?? '-' }})
                    </option>
                @endforeach
            </select>
            <select name="table_status" class="px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] rounded-lg focus:ring-2 focus:ring-[#F53003]/50 focus:border-[#F53003]/50">
                <option value="">All Table Status</option>
                <option value="available" {{ request('table_status') === 'available' ? 'selected' : '' }}>Available</option>
                <option value="occupied" {{ request('table_status') === 'occupied' ? 'selected' : '' }}>Occupied</option>
                <option value="reserved" {{ request('table_status') === 'reserved' ? 'selected' : '' }}>Reserved</option>
                <option value="maintenance" {{ request('table_status') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
            </select>
            <button type="submit" class="px-6 py-2 bg-[#F53003] text-white rounded-lg hover:bg-[#d42800] transition-colors">Filter</button>
            @if(request('search') || request('status') || request('store_id') || request('table_status'))
                <a href="{{ route('admin.tables.index') }}" class="px-6 py-2 border border-[#3E3E3A] text-[#A1A09A] rounded-lg hover:bg-[#0a0a0a] transition-colors">Reset</a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="bg-[#161615] border border-[#3E3E3A] rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-[#3E3E3A]">
                <thead class="bg-[#0a0a0a]">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#A1A09A] uppercase tracking-wider">Table #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#A1A09A] uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#A1A09A] uppercase tracking-wider">Store</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#A1A09A] uppercase tracking-wider">Capacity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#A1A09A] uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#A1A09A] uppercase tracking-wider">Zone/Floor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#A1A09A] uppercase tracking-wider">Active</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-[#A1A09A] uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-[#161615] divide-y divide-[#3E3E3A]">
                    @forelse($tables as $table)
                        <tr class="hover:bg-[#0a0a0a] transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-[#EDEDEC]">{{ $table->table_number }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-[#EDEDEC]">{{ $table->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-[#EDEDEC]">{{ $table->store->name ?? '-' }}</div>
                                <div class="text-xs text-[#A1A09A]">{{ $table->store->brand->name ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-[#A1A09A]">
                                {{ $table->capacity ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'available' => 'bg-green-500/20 text-green-400 border border-green-500/30',
                                        'occupied' => 'bg-red-500/20 text-red-400 border border-red-500/30',
                                        'reserved' => 'bg-yellow-500/20 text-yellow-400 border border-yellow-500/30',
                                        'maintenance' => 'bg-[#3E3E3A] text-[#A1A09A] border border-[#3E3E3A]',
                                        'closed' => 'bg-red-500/20 text-red-400 border border-red-500/30',
                                    ];
                                    $statusColor = $statusColors[$table->status] ?? 'bg-[#3E3E3A] text-[#A1A09A] border border-[#3E3E3A]';
                                @endphp
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColor }}">
                                    {{ ucfirst($table->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-[#A1A09A]">
                                @if($table->zone || $table->floor)
                                    {{ $table->zone ?? '-' }} / {{ $table->floor ?? '-' }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($table->is_active)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-500/20 text-green-400 border border-green-500/30">Active</span>
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-[#3E3E3A] text-[#A1A09A] border border-[#3E3E3A]">Inactive</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    @if(isset($tablesCanClose[$table->id]) && $tablesCanClose[$table->id])
                                        <form action="{{ route('admin.tables.close-orders', $table) }}" method="POST" class="inline" onsubmit="return confirm('Tutup semua pesanan untuk meja ini? Pastikan semua pesanan sudah dibayar dan selesai.');">
                                            @csrf
                                            <button type="submit" class="text-green-400 hover:text-green-300 transition-colors" title="Tutup Pesanan">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('admin.tables.edit', $table) }}" class="text-[#A1A09A] hover:text-[#F53003] transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.tables.destroy', $table) }}" method="POST" class="inline" onsubmit="return confirm('Delete this table?');">
                                        @csrf @method('DELETE')
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
                            <td colspan="8" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-[#3E3E3A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-semibold text-[#EDEDEC]">No tables found</h3>
                                <p class="mt-1 text-sm text-[#A1A09A]">Get started by creating a new table.</p>
                                <div class="mt-6">
                                    <a href="{{ route('admin.tables.create') }}" class="inline-flex items-center px-4 py-2 bg-[#F53003] text-white rounded-lg hover:bg-[#d42800] transition-colors">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Add Meja
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($tables->hasPages())
            <div class="bg-[#0a0a0a] px-4 py-3 border-t border-[#3E3E3A]">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-[#A1A09A]">
                        Showing {{ $tables->firstItem() ?? 0 }} to {{ $tables->lastItem() ?? 0 }} of {{ $tables->total() }}
                    </div>
                    <div class="flex space-x-2">
                        @if($tables->onFirstPage())
                            <span class="px-4 py-2 text-[#3E3E3A] bg-[#0a0a0a] rounded-lg cursor-not-allowed">Previous</span>
                        @else
                            <a href="{{ $tables->previousPageUrl() }}" class="px-4 py-2 border border-[#3E3E3A] text-[#A1A09A] rounded-lg hover:bg-[#161615] transition-colors">Previous</a>
                        @endif
                        @foreach(range(1, min(5, $tables->lastPage())) as $page)
                            @if($page == $tables->currentPage())
                                <span class="px-4 py-2 bg-[#F53003] text-white rounded-lg">{{ $page }}</span>
                            @else
                                <a href="{{ $tables->url($page) }}" class="px-4 py-2 border border-[#3E3E3A] text-[#A1A09A] rounded-lg hover:bg-[#161615] transition-colors">{{ $page }}</a>
                            @endif
                        @endforeach
                        @if($tables->hasMorePages())
                            <a href="{{ $tables->nextPageUrl() }}" class="px-4 py-2 border border-[#3E3E3A] text-[#A1A09A] rounded-lg hover:bg-[#161615] transition-colors">Next</a>
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
