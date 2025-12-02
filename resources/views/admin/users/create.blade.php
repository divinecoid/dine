@extends('layouts.admin')

@section('title', 'Create User - DINE.CO.ID')

@section('page-title', 'Create User')

@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                        Phone <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 @error('phone') border-red-500 @enderror"
                        placeholder="081234567890">
                    <p class="mt-1 text-xs text-gray-500">Format: 081234567890 atau +6281234567890</p>
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Role -->
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                        Role <span class="text-red-500">*</span>
                    </label>
                    <select id="role" name="role" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 @error('role') border-red-500 @enderror">
                        <option value="">Select Role</option>
                        @if($isBrandOwner ?? false)
                            <option value="store_manager" {{ old('role') === 'store_manager' ? 'selected' : '' }}>Store Manager</option>
                        @endif
                        <option value="chef" {{ old('role') === 'chef' ? 'selected' : '' }}>Chef</option>
                        <option value="waiter" {{ old('role') === 'waiter' ? 'selected' : '' }}>Waiter</option>
                        <option value="kasir" {{ old('role') === 'kasir' ? 'selected' : '' }}>Kasir</option>
                    </select>
                    @error('role')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Store -->
                <div class="md:col-span-2">
                    <label for="mdx_store_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Store <span class="text-red-500">*</span>
                        <span class="text-gray-400 text-xs" id="store-help-text">(Required for Chef, Waiter, Kasir)</span>
                    </label>
                    <select id="mdx_store_id" name="mdx_store_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 @error('mdx_store_id') border-red-500 @enderror">
                        <option value="">Select Store</option>
                        @foreach($stores as $store)
                            <option value="{{ $store->id }}" {{ old('mdx_store_id') == $store->id ? 'selected' : '' }}>
                                {{ $store->name }} ({{ $store->brand->name ?? 'N/A' }})
                                @if($store->user_id && old('role') !== 'store_manager')
                                    - Already has manager
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('mdx_store_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password" id="password" name="password" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 @error('password') border-red-500 @enderror">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Confirmation -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        Confirm Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900">
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-4 mt-6">
                <a href="{{ route('admin.users.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800">Create User</button>
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
            // Filter out stores that already have a manager
            Array.from(storeField.options).forEach(option => {
                if (option.value && option.text.includes('Already has manager')) {
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
</script>
@endsection

