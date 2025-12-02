@extends('layouts.admin')

@section('title', 'Bank Accounts - DINE.CO.ID')

@section('page-title', 'Rekening')

@section('content')
<div class="space-y-6">
    <!-- Header with Add Button -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Rekening</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola rekening bank untuk brand dan store</p>
        </div>
        <a href="{{ route('admin.bank-accounts.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Rekening
        </a>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error') || $errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            @if(session('error'))
                {{ session('error') }}
            @endif
            @if($errors->any())
                <ul class="list-disc list-inside mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif

    <!-- Filters and Search -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" action="{{ route('admin.bank-accounts.index') }}" class="flex flex-wrap gap-4">
            <!-- Search -->
            <div class="flex-1 min-w-[200px]">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}"
                    placeholder="Cari nama pemilik, nomor rekening, atau bank..." 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                >
            </div>

            <!-- Bank Filter -->
            <select name="bank_name" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                <option value="">Semua Bank</option>
                @foreach($bankNames as $bankName)
                    <option value="{{ $bankName }}" {{ request('bank_name') === $bankName ? 'selected' : '' }}>{{ $bankName }}</option>
                @endforeach
            </select>

            <!-- Verified Status Filter -->
            <select name="is_verified" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                <option value="">Semua Status</option>
                <option value="1" {{ request('is_verified') === '1' ? 'selected' : '' }}>Terverifikasi</option>
                <option value="0" {{ request('is_verified') === '0' ? 'selected' : '' }}>Belum Terverifikasi</option>
            </select>

            <!-- Owner Type Filter -->
            <select name="owner_type" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                <option value="">Semua Tipe</option>
                <option value="brand" {{ request('owner_type') === 'brand' ? 'selected' : '' }}>Brand</option>
                <option value="store" {{ request('owner_type') === 'store' ? 'selected' : '' }}>Store</option>
            </select>

            <!-- Submit Button -->
            <button type="submit" class="px-6 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition-colors">
                Filter
            </button>

            <!-- Reset Button -->
            @if(request('search') || request('bank_name') || request('is_verified') || request('owner_type'))
                <a href="{{ route('admin.bank-accounts.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Bank Accounts Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bank</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Pemilik</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Rekening</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pemilik</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Verifikasi</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($bankAccounts as $bankAccount)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $bankAccount->bank_name }}</div>
                                @if($bankAccount->bank_code)
                                    <div class="text-xs text-gray-500">{{ $bankAccount->bank_code }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $bankAccount->account_name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $bankAccount->account_number }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($bankAccount->brand)
                                    <div class="text-sm text-gray-900">{{ $bankAccount->brand->name }}</div>
                                    <div class="text-xs text-gray-500">Brand</div>
                                @elseif($bankAccount->store)
                                    <div class="text-sm text-gray-900">{{ $bankAccount->store->name }}</div>
                                    <div class="text-xs text-gray-500">Store ({{ $bankAccount->store->brand->name ?? 'N/A' }})</div>
                                @else
                                    <span class="text-sm text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($bankAccount->is_active)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Tidak Aktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($bankAccount->is_verified)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Terverifikasi</span>
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Belum Terverifikasi</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end items-center space-x-2">
                                    @if(!$bankAccount->is_verified)
                                        <form action="{{ route('admin.bank-accounts.verify', $bankAccount) }}" method="POST" class="inline" onsubmit="return confirm('Verifikasi rekening ini?');">
                                            @csrf
                                            <button type="submit" class="text-blue-600 hover:text-blue-900" title="Verifikasi">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('admin.bank-accounts.edit', $bankAccount) }}" class="text-gray-600 hover:text-gray-900">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.bank-accounts.destroy', $bankAccount) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus rekening ini?');">
                                        @csrf
                                        @method('DELETE')
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
                            <td colspan="7" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-semibold text-gray-900">Tidak ada rekening</h3>
                                <p class="mt-1 text-sm text-gray-500">Mulai dengan menambahkan rekening baru.</p>
                                <div class="mt-6">
                                    <a href="{{ route('admin.bank-accounts.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition-colors">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Tambah Rekening
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($bankAccounts->hasPages())
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Menampilkan {{ $bankAccounts->firstItem() ?? 0 }} sampai {{ $bankAccounts->lastItem() ?? 0 }} dari {{ $bankAccounts->total() }} hasil
                    </div>
                    <div class="flex space-x-2">
                        @if($bankAccounts->onFirstPage())
                            <span class="px-4 py-2 text-gray-400 bg-gray-50 rounded-lg cursor-not-allowed">Previous</span>
                        @else
                            <a href="{{ $bankAccounts->previousPageUrl() }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">Previous</a>
                        @endif
                        
                        @foreach(range(1, min(5, $bankAccounts->lastPage())) as $page)
                            @if($page == $bankAccounts->currentPage())
                                <span class="px-4 py-2 bg-gray-900 text-white rounded-lg">{{ $page }}</span>
                            @else
                                <a href="{{ $bankAccounts->url($page) }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">{{ $page }}</a>
                            @endif
                        @endforeach
                        
                        @if($bankAccounts->hasMorePages())
                            <a href="{{ $bankAccounts->nextPageUrl() }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">Next</a>
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
