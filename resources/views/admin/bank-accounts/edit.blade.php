@extends('layouts.admin')

@section('title', 'Edit Rekening - DINE.CO.ID')

@section('page-title', 'Edit Rekening')

@section('content')
<div class="max-w-3xl">
    <div class="bg-[#161615] border border-[#3E3E3A] rounded-lg shadow p-6">
        <form action="{{ route('admin.bank-accounts.update', $bankAccount) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Owner Type Selection -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-[#EDEDEC] mb-2">
                        Tipe Pemilik <span class="text-[#F53003]">*</span>
                    </label>
                    <div class="flex gap-4">
                        <label class="flex items-center">
                            <input type="radio" name="owner_type" value="brand" class="mr-2 text-[#F53003] focus:ring-[#F53003]/50" onchange="toggleOwnerFields()" {{ $bankAccount->mdx_brand_id ? 'checked' : '' }}>
                            <span class="text-sm text-[#EDEDEC]">Brand</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="owner_type" value="store" class="mr-2 text-[#F53003] focus:ring-[#F53003]/50" onchange="toggleOwnerFields()" {{ $bankAccount->mdx_store_id ? 'checked' : '' }}>
                            <span class="text-sm text-[#EDEDEC]">Store</span>
                        </label>
                    </div>
                </div>

                <!-- Brand -->
                <div id="brand-field" style="display: {{ $bankAccount->mdx_brand_id ? 'block' : 'none' }};">
                    <label for="mdx_brand_id" class="block text-sm font-medium text-[#EDEDEC] mb-2">
                        Brand <span class="text-[#F53003]">*</span>
                    </label>
                    <select id="mdx_brand_id" name="mdx_brand_id"
                        class="w-full px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] rounded-lg focus:ring-[#F53003]/50 focus:border-[#F53003]/50 transition-colors @error('mdx_brand_id') border-[#F53003] @enderror">
                        <option value="">Pilih Brand</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ old('mdx_brand_id', $bankAccount->mdx_brand_id) == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                    @error('mdx_brand_id')
                        <p class="mt-1 text-sm text-[#F53003]">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Store -->
                <div id="store-field" style="display: {{ $bankAccount->mdx_store_id ? 'block' : 'none' }};">
                    <label for="mdx_store_id" class="block text-sm font-medium text-[#EDEDEC] mb-2">
                        Store <span class="text-[#F53003]">*</span>
                    </label>
                    <select id="mdx_store_id" name="mdx_store_id"
                        class="w-full px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] rounded-lg focus:ring-[#F53003]/50 focus:border-[#F53003]/50 transition-colors @error('mdx_store_id') border-[#F53003] @enderror">
                        <option value="">Pilih Store</option>
                        @foreach($stores as $store)
                            <option value="{{ $store->id }}" {{ old('mdx_store_id', $bankAccount->mdx_store_id) == $store->id ? 'selected' : '' }}>
                                {{ $store->name }} ({{ $store->brand->name ?? 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                    @error('mdx_store_id')
                        <p class="mt-1 text-sm text-[#F53003]">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Bank Name -->
                <div>
                    <label for="bank_name" class="block text-sm font-medium text-[#EDEDEC] mb-2">
                        Bank <span class="text-[#F53003]">*</span>
                    </label>
                    <input type="text" id="bank_name" name="bank_name" value="{{ old('bank_name', $bankAccount->bank_name) }}" required
                        class="w-full px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] placeholder-[#A1A09A] rounded-lg focus:ring-[#F53003]/50 focus:border-[#F53003]/50 transition-colors @error('bank_name') border-[#F53003] @enderror"
                        placeholder="Contoh: BCA, BNI, Mandiri, BRI">
                    @error('bank_name')
                        <p class="mt-1 text-sm text-[#F53003]">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Bank Code -->
                <div>
                    <label for="bank_code" class="block text-sm font-medium text-[#EDEDEC] mb-2">
                        Kode Bank
                    </label>
                    <input type="text" id="bank_code" name="bank_code" value="{{ old('bank_code', $bankAccount->bank_code) }}"
                        class="w-full px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] placeholder-[#A1A09A] rounded-lg focus:ring-[#F53003]/50 focus:border-[#F53003]/50 transition-colors @error('bank_code') border-[#F53003] @enderror"
                        placeholder="Contoh: BCA, BNI, MANDIRI">
                    @error('bank_code')
                        <p class="mt-1 text-sm text-[#F53003]">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Account Name -->
                <div>
                    <label for="account_name" class="block text-sm font-medium text-[#EDEDEC] mb-2">
                        Nama Pemilik Rekening <span class="text-[#F53003]">*</span>
                    </label>
                    <input type="text" id="account_name" name="account_name" value="{{ old('account_name', $bankAccount->account_name) }}" required
                        class="w-full px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] placeholder-[#A1A09A] rounded-lg focus:ring-[#F53003]/50 focus:border-[#F53003]/50 transition-colors @error('account_name') border-[#F53003] @enderror">
                    @error('account_name')
                        <p class="mt-1 text-sm text-[#F53003]">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Account Number -->
                <div>
                    <label for="account_number" class="block text-sm font-medium text-[#EDEDEC] mb-2">
                        Nomor Rekening <span class="text-[#F53003]">*</span>
                    </label>
                    <input type="text" id="account_number" name="account_number" value="{{ old('account_number', $bankAccount->account_number) }}" required
                        class="w-full px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] placeholder-[#A1A09A] rounded-lg focus:ring-[#F53003]/50 focus:border-[#F53003]/50 transition-colors @error('account_number') border-[#F53003] @enderror">
                    @error('account_number')
                        <p class="mt-1 text-sm text-[#F53003]">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Branch Name -->
                <div>
                    <label for="branch_name" class="block text-sm font-medium text-[#EDEDEC] mb-2">
                        Nama Cabang
                    </label>
                    <input type="text" id="branch_name" name="branch_name" value="{{ old('branch_name', $bankAccount->branch_name) }}"
                        class="w-full px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] placeholder-[#A1A09A] rounded-lg focus:ring-[#F53003]/50 focus:border-[#F53003]/50 transition-colors">
                </div>

                <!-- Currency -->
                <div>
                    <label for="currency" class="block text-sm font-medium text-[#EDEDEC] mb-2">
                        Mata Uang
                    </label>
                    <select id="currency" name="currency"
                        class="w-full px-4 py-2 border border-[#3E3E3A] rounded-lg focus:ring-2 focus:ring-[#F53003]/50 focus:border-[#F53003]/50">
                        <option value="IDR" {{ old('currency', $bankAccount->currency) === 'IDR' ? 'selected' : '' }}>IDR (Rupiah)</option>
                        <option value="USD" {{ old('currency', $bankAccount->currency) === 'USD' ? 'selected' : '' }}>USD (Dollar)</option>
                    </select>
                </div>

                <!-- Notes -->
                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-[#EDEDEC] mb-2">
                        Catatan
                    </label>
                    <textarea id="notes" name="notes" rows="3"
                        class="w-full px-4 py-2 border border-[#3E3E3A] rounded-lg focus:ring-2 focus:ring-[#F53003]/50 focus:border-[#F53003]/50">{{ old('notes', $bankAccount->notes) }}</textarea>
                </div>

                <!-- Verification Status (Read-only) -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-[#EDEDEC] mb-2">
                        Status Verifikasi
                    </label>
                    @if($bankAccount->is_verified)
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-blue-500/20 text-blue-400">Terverifikasi</span>
                    @else
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-yellow-500/20 text-yellow-400">Belum Terverifikasi</span>
                        <p class="mt-2 text-sm text-[#A1A09A]">Gunakan tombol verifikasi di halaman daftar rekening untuk memverifikasi rekening ini.</p>
                    @endif
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-4 mt-6">
                <a href="{{ route('admin.bank-accounts.index') }}" class="px-6 py-2 border border-[#3E3E3A] text-[#A1A09A] rounded-lg hover:bg-[#0a0a0a] transition-colors">Batal</a>
                <button type="submit" class="px-6 py-2 bg-[#F53003] text-white rounded-lg hover:bg-[#d42800] transition-colors">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleOwnerFields() {
        const ownerType = document.querySelector('input[name="owner_type"]:checked').value;
        const brandField = document.getElementById('brand-field');
        const storeField = document.getElementById('store-field');
        const brandSelect = document.getElementById('mdx_brand_id');
        const storeSelect = document.getElementById('mdx_store_id');

        if (ownerType === 'brand') {
            brandField.style.display = 'block';
            storeField.style.display = 'none';
            brandSelect.required = true;
            storeSelect.required = false;
            storeSelect.value = '';
        } else {
            brandField.style.display = 'none';
            storeField.style.display = 'block';
            brandSelect.required = false;
            storeSelect.required = true;
            brandSelect.value = '';
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        toggleOwnerFields();
    });
</script>
@endsection

