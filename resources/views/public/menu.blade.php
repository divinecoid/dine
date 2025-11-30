@extends('layouts.public')

@section('title', ($brand->name ?? 'Menu') . ' - Menu')

@section('content')
<div class="min-h-screen bg-white">
    <!-- Category Navigation Bar -->
    <div class="sticky top-0 z-10 bg-white border-b border-gray-200 shadow-sm overflow-visible">
        <div class="flex items-center overflow-x-auto scrollbar-hide px-4 py-3 gap-4">
            <!-- Hamburger Menu Icon -->
            <div class="relative shrink-0 z-20">
                <button type="button" 
                        id="menu-toggle"
                        class="flex items-center justify-center w-10 h-10 hover:bg-gray-100 rounded-lg transition-colors" 
                        aria-label="Menu"
                        onclick="toggleMenu()">
                    <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                
                <!-- Dropdown Menu -->
                <div id="menu-dropdown" 
                     class="fixed left-4 top-16 w-48 bg-white rounded-lg shadow-xl border border-gray-200 py-2 hidden z-[100]">
                    <a href="{{ route('public.menu', ['brandSlug' => $brand->slug, 'table' => $tableIdentifier]) }}" 
                       class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        <span>Menu</span>
                    </a>
                    <a href="{{ route('public.orders.index', ['table' => $tableIdentifier]) }}" 
                       class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <span>Order</span>
                    </a>
                </div>
            </div>

            <!-- Category Tabs -->
            <div class="flex gap-4 flex-1 overflow-x-auto scrollbar-hide">
                <!-- All Tab -->
                <a href="{{ route('public.menu', ['brandSlug' => $brand->slug, 'category' => 'all', 'table' => $tableIdentifier]) }}"
                   class="shrink-0 px-3 py-2 text-sm font-medium whitespace-nowrap transition-colors relative
                          {{ isset($showAll) && $showAll 
                              ? 'text-gray-900 font-semibold' 
                              : 'text-gray-600 hover:text-gray-900' }}">
                    Semua
                    @if(isset($showAll) && $showAll)
                        <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-gray-900"></span>
                    @endif
                </a>
                
                @forelse($categories as $category)
                    <a href="{{ route('public.menu', ['brandSlug' => $brand->slug, 'category' => $category->id, 'table' => $tableIdentifier]) }}"
                       class="shrink-0 px-3 py-2 text-sm font-medium whitespace-nowrap transition-colors relative
                              {{ $selectedCategory && $selectedCategory->id === $category->id 
                                  ? 'text-gray-900 font-semibold' 
                                  : 'text-gray-600 hover:text-gray-900' }}">
                        {{ $category->name }}
                        @if($selectedCategory && $selectedCategory->id === $category->id)
                            <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-gray-900"></span>
                        @endif
                    </a>
                @empty
                    <span class="text-sm text-gray-500 px-4 py-2">Tidak ada kategori</span>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Menu Items Grid -->
    <div class="container mx-auto px-4 py-6 pb-20">
        @if(($selectedCategory || (isset($showAll) && $showAll)) && $menus->isNotEmpty())
            <div class="grid grid-cols-2 gap-4">
                @foreach($menus as $menu)
                    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                        <!-- Menu Image -->
                        <div class="aspect-square bg-gray-100 overflow-hidden">
                            @if($menu->image)
                                <img src="{{ Str::startsWith($menu->image, ['http://', 'https://']) ? $menu->image : asset('storage/' . $menu->image) }}" 
                                     alt="{{ $menu->name }}"
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
                                    <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <!-- Menu Info -->
                        <div class="p-3">
                            <h3 class="font-semibold text-gray-900 text-sm mb-1 line-clamp-2 min-h-[2.5rem]">
                                {{ $menu->name }}
                            </h3>
                            <p class="text-xs text-gray-500 mb-2 line-clamp-2">
                                {{ Str::limit($menu->description ?? '', 50) }}
                            </p>
                            <div class="flex items-center justify-between">
                                <span class="text-base font-bold text-gray-900">
                                    Rp {{ number_format($menu->price, 0, ',', '.') }}
                                </span>
                                <button type="button" 
                                        class="px-3 py-1.5 bg-gray-900 text-white text-xs font-medium rounded-md hover:bg-gray-800 transition-colors active:scale-95"
                                        onclick="addToCart({{ $menu->id }}, '{{ addslashes($menu->name) }}', {{ $menu->price }})">
                                    + Add
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @elseif(($selectedCategory || (isset($showAll) && $showAll)) && $menus->isEmpty())
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="text-gray-500 text-sm">
                    @if(isset($showAll) && $showAll)
                        Tidak ada menu tersedia
                    @else
                        Tidak ada menu tersedia di kategori ini
                    @endif
                </p>
            </div>
        @else
            <div class="text-center py-12">
                <p class="text-gray-500 text-sm">Pilih kategori untuk melihat menu</p>
            </div>
        @endif
    </div>

    <!-- Cart Summary (Floating Bottom) -->
    <div id="cart-summary" class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg p-4 hidden">
        <div class="container mx-auto flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Total Item</p>
                <p id="cart-total-items" class="text-lg font-bold text-gray-900">0</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-600">Total Harga</p>
                <p id="cart-total-price" class="text-lg font-bold text-gray-900">Rp 0</p>
            </div>
            <button id="cart-checkout-btn" 
                    class="px-6 py-3 bg-gray-900 text-white font-semibold rounded-lg hover:bg-gray-800 transition-colors"
                    onclick="checkout()">
                Pesan
            </button>
        </div>
    </div>
</div>

<!-- Alert Modal -->
<div id="alertModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-sm w-full p-6">
        <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 rounded-full" id="alertModalIcon">
            <!-- Icon will be set dynamically -->
        </div>
        
        <h3 class="text-lg font-semibold text-gray-900 text-center mb-2" id="alertModalTitle">
            <!-- Title will be set dynamically -->
        </h3>
        
        <p class="text-sm text-gray-600 text-center mb-6" id="alertModalMessage">
            <!-- Message will be set dynamically -->
        </p>
        
        <button type="button" 
                onclick="closeAlertModal()"
                class="w-full px-4 py-2 bg-gray-900 text-white font-semibold rounded-lg hover:bg-gray-800 transition-colors"
                id="alertModalButton">
            OK
        </button>
    </div>
</div>

<!-- Quantity Input Modal -->
<div id="quantityModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-sm w-full p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-2" id="quantityModalTitle">Pilih Jumlah</h3>
        <p class="text-sm text-gray-600 mb-4" id="quantityModalMenuName"></p>
        
        <div class="flex items-center justify-center gap-4 mb-6">
            <button type="button" 
                    onclick="decreaseQuantity()"
                    class="w-12 h-12 flex items-center justify-center border-2 border-gray-300 rounded-lg hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    id="quantityDecreaseBtn">
                <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                </svg>
            </button>
            <div class="w-20 text-center">
                <span class="text-3xl font-bold text-gray-900" id="quantityDisplay">1</span>
            </div>
            <button type="button" 
                    onclick="increaseQuantity()"
                    class="w-12 h-12 flex items-center justify-center border-2 border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                    id="quantityIncreaseBtn">
                <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
            </button>
        </div>
        
        <div class="flex gap-3">
            <button type="button" 
                    onclick="closeQuantityModal()"
                    class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors">
                Batal
            </button>
            <button type="button" 
                    onclick="confirmQuantity()"
                    class="flex-1 px-4 py-2 bg-gray-900 text-white font-semibold rounded-lg hover:bg-gray-800 transition-colors">
                Tambah ke Keranjang
            </button>
        </div>
    </div>
</div>

<!-- Customer Info Modal -->
<div id="customerModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Customer</h3>
        
        <div class="space-y-4 mb-6">
            <div>
                <label for="customerNameInput" class="block text-sm font-medium text-gray-700 mb-2">
                    Nama <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="customerNameInput"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                       placeholder="Masukkan nama Anda"
                       required>
                <p class="text-xs text-red-500 mt-1 hidden" id="customerNameError">Nama wajib diisi</p>
            </div>
            
            <div>
                <label for="customerPhoneInput" class="block text-sm font-medium text-gray-700 mb-2">
                    No. Telepon <span class="text-gray-400 text-xs">(Opsional)</span>
                </label>
                <input type="tel" 
                       id="customerPhoneInput"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                       placeholder="Masukkan nomor telepon">
            </div>
        </div>
        
        <div class="flex gap-3">
            <button type="button" 
                    onclick="closeCustomerModal()"
                    class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors">
                Batal
            </button>
            <button type="button" 
                    onclick="confirmCustomerInfo()"
                    class="flex-1 px-4 py-2 bg-gray-900 text-white font-semibold rounded-lg hover:bg-gray-800 transition-colors">
                Lanjutkan Pesanan
            </button>
        </div>
    </div>
</div>

<script>
    const tableId = @json($table ? $table->id : null);
    const tableIdentifier = @json($tableIdentifier);
    const storeId = @json($store->id);
    const brandId = @json($brand->id);
    
    // Cart storage key based on table identifier
    const CART_STORAGE_KEY = `cart_${tableIdentifier}`;
    
    // Initialize cart from localStorage or empty array
    let cart = JSON.parse(localStorage.getItem(CART_STORAGE_KEY) || '[]');
    
    // Restore cart UI on page load (after DOM is ready)
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            updateCartUI();
        });
    } else {
        // DOM is already ready
        updateCartUI();
    }

    // Menu Toggle Functions
    function toggleMenu() {
        const dropdown = document.getElementById('menu-dropdown');
        const toggle = document.getElementById('menu-toggle');
        
        if (dropdown.classList.contains('hidden')) {
            // Calculate position
            const rect = toggle.getBoundingClientRect();
            const dropdownWidth = 192; // w-48 = 192px
            const viewportWidth = window.innerWidth;
            
            // Position dropdown
            let leftPos = rect.left;
            
            // Ensure dropdown doesn't go off screen on the right
            if (leftPos + dropdownWidth > viewportWidth - 16) {
                leftPos = viewportWidth - dropdownWidth - 16;
            }
            
            // Ensure dropdown doesn't go off screen on the left
            if (leftPos < 16) {
                leftPos = 16;
            }
            
            dropdown.style.left = leftPos + 'px';
            dropdown.style.top = (rect.bottom + 8) + 'px';
            dropdown.classList.remove('hidden');
        } else {
            dropdown.classList.add('hidden');
        }
    }

    // Close menu when clicking outside
    document.addEventListener('click', function(event) {
        const menuToggle = document.getElementById('menu-toggle');
        const menuDropdown = document.getElementById('menu-dropdown');
        
        if (!menuToggle.contains(event.target) && !menuDropdown.contains(event.target)) {
            menuDropdown.classList.add('hidden');
        }
    });

    function saveCart() {
        localStorage.setItem(CART_STORAGE_KEY, JSON.stringify(cart));
    }

    // Quantity Modal State
    let currentMenuId = null;
    let currentMenuName = null;
    let currentMenuPrice = null;
    let currentQuantity = 1;

    function addToCart(menuId, menuName, price) {
        // Set current menu info
        currentMenuId = menuId;
        currentMenuName = menuName;
        currentMenuPrice = price;
        
        // Check if item already in cart
        const existingItem = cart.find(item => item.menu_id === menuId);
        currentQuantity = existingItem ? existingItem.quantity : 1;
        
        // Show quantity modal
        showQuantityModal();
    }

    function showQuantityModal() {
        document.getElementById('quantityModalMenuName').textContent = currentMenuName;
        document.getElementById('quantityDisplay').textContent = currentQuantity;
        
        // Update button states
        const decreaseBtn = document.getElementById('quantityDecreaseBtn');
        decreaseBtn.disabled = currentQuantity <= 1;
        
        document.getElementById('quantityModal').classList.remove('hidden');
    }

    function closeQuantityModal() {
        document.getElementById('quantityModal').classList.add('hidden');
        currentMenuId = null;
        currentMenuName = null;
        currentMenuPrice = null;
        currentQuantity = 1;
    }

    function increaseQuantity() {
        currentQuantity++;
        document.getElementById('quantityDisplay').textContent = currentQuantity;
        
        const decreaseBtn = document.getElementById('quantityDecreaseBtn');
        decreaseBtn.disabled = false;
    }

    function decreaseQuantity() {
        if (currentQuantity > 1) {
            currentQuantity--;
            document.getElementById('quantityDisplay').textContent = currentQuantity;
            
            const decreaseBtn = document.getElementById('quantityDecreaseBtn');
            if (currentQuantity <= 1) {
                decreaseBtn.disabled = true;
            }
        }
    }

    function confirmQuantity() {
        // Add or update item in cart
        const existingItemIndex = cart.findIndex(item => item.menu_id === currentMenuId);
        
        if (existingItemIndex >= 0) {
            cart[existingItemIndex].quantity = currentQuantity;
        } else {
            cart.push({
                menu_id: currentMenuId,
                name: currentMenuName,
                price: currentMenuPrice,
                quantity: currentQuantity
            });
        }
        
        saveCart();
        updateCartUI();
        closeQuantityModal();
    }

    // Close quantity modal when clicking outside
    document.getElementById('quantityModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeQuantityModal();
        }
    });

    function removeFromCart(menuId) {
        cart = cart.filter(item => item.menu_id !== menuId);
        saveCart();
        updateCartUI();
    }

    function updateCartUI() {
        const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        const totalPrice = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        
        document.getElementById('cart-total-items').textContent = totalItems;
        document.getElementById('cart-total-price').textContent = 'Rp ' + totalPrice.toLocaleString('id-ID');
        
        const cartSummary = document.getElementById('cart-summary');
        if (totalItems > 0) {
            cartSummary.classList.remove('hidden');
        } else {
            cartSummary.classList.add('hidden');
        }
    }

    async function checkout() {
        if (cart.length === 0) {
            showAlert('error', 'Keranjang Kosong', 'Keranjang Anda masih kosong. Silakan tambahkan item terlebih dahulu.');
            return;
        }

        // Show customer info modal
        showCustomerModal();
    }

    function showCustomerModal() {
        // Clear inputs
        document.getElementById('customerNameInput').value = '';
        document.getElementById('customerPhoneInput').value = '';
        document.getElementById('customerNameError').classList.add('hidden');
        
        document.getElementById('customerModal').classList.remove('hidden');
    }

    function closeCustomerModal() {
        document.getElementById('customerModal').classList.add('hidden');
        document.getElementById('customerNameError').classList.add('hidden');
    }

    async function confirmCustomerInfo() {
        const customerName = document.getElementById('customerNameInput').value.trim();
        const customerPhone = document.getElementById('customerPhoneInput').value.trim() || null;
        
        // Validate customer name
        if (!customerName) {
            document.getElementById('customerNameError').classList.remove('hidden');
            return;
        }
        
        document.getElementById('customerNameError').classList.add('hidden');
        
        // Close modal
        closeCustomerModal();
        
        // Proceed with checkout
        await processCheckout(customerName, customerPhone);
    }

    async function processCheckout(customerName, customerPhone) {

        try {
            const response = await fetch('/api/v1/orders', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    mdx_store_id: storeId,
                    mdx_brand_id: brandId,
                    mdx_table_id: tableId,
                    order_type: 'dine_in',
                    customer_name: customerName,
                    customer_phone: customerPhone,
                    items: cart.map(item => ({
                        mdx_menu_id: item.menu_id,
                        quantity: item.quantity
                    }))
                })
            });

            const data = await response.json();
            
            if (data.success) {
                // Clear cart from localStorage after successful checkout
                cart = [];
                localStorage.removeItem(CART_STORAGE_KEY);
                
                // Redirect to order detail page with table
                const tableId = @json($tableIdentifier);
                window.location.href = '/orders/' + data.data.order_number + (tableId ? '?table=' + encodeURIComponent(tableId) : '');
            } else {
                showAlert('error', 'Gagal', 'Gagal membuat pesanan: ' + (data.message || 'Unknown error'));
                if (data.errors) {
                    console.error('Validation errors:', data.errors);
                }
            }
        } catch (error) {
            console.error('Error:', error);
            showAlert('error', 'Error', 'Terjadi kesalahan saat membuat pesanan. Silakan coba lagi.');
        }
    }

    // Close customer modal when clicking outside
    document.getElementById('customerModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeCustomerModal();
        }
    });

    // Allow Enter key to submit customer form
    document.getElementById('customerNameInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            confirmCustomerInfo();
        }
    });

    document.getElementById('customerPhoneInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            confirmCustomerInfo();
        }
    });
</script>

<style>
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endsection

