@extends('layouts.admin')

@section('title', 'Edit User - DINE.CO.ID')

@section('page-title', 'Edit User')

@section('content')
<div class="max-w-3xl">
    <div class="bg-[#161615] border border-[#3E3E3A] rounded-lg shadow p-6">
        <form action="{{ route('admin.users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-[#EDEDEC] mb-2">
                        Name <span class="text-[#F53003]">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] placeholder-[#A1A09A] rounded-lg focus:ring-[#F53003]/50 focus:border-[#F53003]/50 transition-colors @error('name') border-[#F53003] @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-[#F53003]">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-[#EDEDEC] mb-2">
                        Email <span class="text-[#F53003]">*</span>
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="w-full px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] placeholder-[#A1A09A] rounded-lg focus:ring-[#F53003]/50 focus:border-[#F53003]/50 transition-colors @error('email') border-[#F53003] @enderror">
                    @error('email')
                        <p class="mt-1 text-sm text-[#F53003]">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-[#EDEDEC] mb-2">
                        Phone <span class="text-[#F53003]">*</span>
                    </label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" required
                        class="w-full px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] placeholder-[#A1A09A] rounded-lg focus:ring-[#F53003]/50 focus:border-[#F53003]/50 transition-colors @error('phone') border-[#F53003] @enderror"
                        placeholder="081234567890">
                    <p class="mt-1 text-xs text-[#A1A09A]">Format: 081234567890 atau +6281234567890</p>
                    @error('phone')
                        <p class="mt-1 text-sm text-[#F53003]">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Role -->
                <div>
                    <label for="role" class="block text-sm font-medium text-[#EDEDEC] mb-2">
                        Role <span class="text-[#F53003]">*</span>
                    </label>
                    <select id="role" name="role" required
                        class="w-full px-4 py-2 border border-[#3E3E3A] rounded-lg focus:ring-2 focus:ring-[#F53003]/50 focus:border-[#F53003]/50 @error('role') border-[#F53003] @enderror">
                        <option value="">Select Role</option>
                        @if($isBrandOwner ?? false)
                            <option value="store_manager" {{ old('role', $user->role) === 'store_manager' ? 'selected' : '' }}>Store Manager</option>
                        @endif
                        <option value="chef" {{ old('role', $user->role) === 'chef' ? 'selected' : '' }}>Chef</option>
                        <option value="waiter" {{ old('role', $user->role) === 'waiter' ? 'selected' : '' }}>Waiter</option>
                        <option value="kasir" {{ old('role', $user->role) === 'kasir' ? 'selected' : '' }}>Kasir</option>
                    </select>
                    @error('role')
                        <p class="mt-1 text-sm text-[#F53003]">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Store -->
                <div class="md:col-span-2">
                    <label for="mdx_store_id" class="block text-sm font-medium text-[#EDEDEC] mb-2">
                        Store <span class="text-[#F53003]">*</span>
                        <span class="text-[#A1A09A] text-xs" id="store-help-text">(Required for Chef, Waiter, Kasir)</span>
                    </label>
                    <select id="mdx_store_id" name="mdx_store_id"
                        class="w-full px-4 py-2 border border-[#3E3E3A] rounded-lg focus:ring-2 focus:ring-[#F53003]/50 focus:border-[#F53003]/50 @error('mdx_store_id') border-[#F53003] @enderror">
                        <option value="">Select Store</option>
                        @foreach($stores as $store)
                            <option value="{{ $store->id }}" 
                                {{ old('mdx_store_id', ($user->role === 'store_manager' ? $user->store->id ?? null : $user->mdx_store_id)) == $store->id ? 'selected' : '' }}>
                                {{ $store->name }} ({{ $store->brand->name ?? 'N/A' }})
                                @if($store->user_id && $store->user_id != $user->id && old('role') !== 'store_manager')
                                    - Already has manager
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('mdx_store_id')
                        <p class="mt-1 text-sm text-[#F53003]">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-[#EDEDEC] mb-2">
                        Password <span class="text-[#A1A09A] text-xs">(leave blank to keep current)</span>
                    </label>
                    <input type="password" id="password" name="password"
                        class="w-full px-4 py-2 border border-[#3E3E3A] rounded-lg focus:ring-2 focus:ring-[#F53003]/50 focus:border-[#F53003]/50 @error('password') border-[#F53003] @enderror">
                    @error('password')
                        <p class="mt-1 text-sm text-[#F53003]">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Confirmation -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-[#EDEDEC] mb-2">
                        Confirm Password
                    </label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                        class="w-full px-4 py-2 border border-[#3E3E3A] rounded-lg focus:ring-2 focus:ring-[#F53003]/50 focus:border-[#F53003]/50">
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-4 mt-6">
                <a href="{{ route('admin.users.index') }}" class="px-6 py-2 border border-[#3E3E3A] text-[#A1A09A] rounded-lg hover:bg-[#0a0a0a] transition-colors">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-[#F53003] text-white rounded-lg hover:bg-[#d42800] transition-colors">Update User</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Show/hide store field based on role
    document.getElementById('role').addEventListener('change', function() {
        const role = this.value;
        const storeField = document.getElementById('mdx_store_id');
        const helpText = document.getElementById('store-help-text');
        
        if (role === 'store_manager') {
            storeField.required = true;
            helpText.textContent = '(Required for Store Manager)';
            // Filter out stores that already have a manager (except current user's store)
            const currentStoreId = '{{ $user->store->id ?? null }}';
            Array.from(storeField.options).forEach(option => {
                if (option.value && option.text.includes('Already has manager') && option.value != currentStoreId) {
                    option.style.display = 'none';
                } else {
                    option.style.display = '';
                }
            });
        } else if (role === 'chef' || role === 'waiter' || role === 'kasir') {
            storeField.required = true;
            helpText.textContent = '(Required for Chef, Waiter, Kasir)';
            // Show all stores
            Array.from(storeField.options).forEach(option => {
                option.style.display = '';
            });
        } else {
            storeField.required = false;
            helpText.textContent = '';
        }
    });
    
    // Trigger on page load
    document.getElementById('role').dispatchEvent(new Event('change'));
</script>
@endsection

