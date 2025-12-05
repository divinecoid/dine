@extends('layouts.admin')

@section('title', 'Kasir - DINE.CO.ID')

@section('page-title', 'Kasir')

@section('content')
<div class="space-y-6">
    @if(session('error'))
        <div class="bg-[#1D0002] border border-[#F53003]/30 text-[#FF4433] px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <!-- Store Selection -->
    <div class="bg-[#161615] border border-[#3E3E3A] rounded-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-[#EDEDEC]">Pilih Store</h2>
        </div>
        
        <form method="GET" action="{{ route('admin.kasir.index') }}" class="flex items-end gap-4">
            <div class="flex-1">
                <label for="store_id" class="block text-sm font-medium text-[#EDEDEC] mb-2">
                    Store
                </label>
                @if($lockedStoreId)
                    <input type="hidden" name="store_id" value="{{ $lockedStoreId }}">
                    <select 
                        id="store_id" 
                        class="w-full px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] rounded-lg opacity-60 cursor-not-allowed"
                        disabled
                    >
                        @foreach($stores as $store)
                            <option value="{{ $store->id }}" {{ $selectedStore && $selectedStore->id == $store->id ? 'selected' : '' }}>
                                {{ $store->name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-2 text-xs text-[#A1A09A]">Store terkunci ke store yang ditugaskan untuk Anda</p>
                @else
                    <select 
                        name="store_id" 
                        id="store_id" 
                        class="w-full px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#F53003]/50 focus:border-[#F53003]/50 transition-colors"
                        onchange="this.form.submit()"
                    >
                        <option value="">-- Pilih Store --</option>
                        @foreach($stores as $store)
                            <option value="{{ $store->id }}" {{ $selectedStore && $selectedStore->id == $store->id ? 'selected' : '' }}>
                                {{ $store->name }}
                            </option>
                        @endforeach
                    </select>
                @endif
            </div>
        </form>
    </div>

    @if($selectedStore)
        @if(session('success'))
            <div class="bg-green-900/30 border border-green-500/30 text-green-400 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <!-- Tabs -->
        <div class="bg-[#161615] border border-[#3E3E3A] rounded-lg">
            <div class="flex border-b border-[#3E3E3A]">
                <button onclick="showTab('new-order')" id="tab-new-order" class="flex-1 px-6 py-3 text-sm font-medium text-[#EDEDEC] border-b-2 border-[#F53003] bg-[#0a0a0a]">
                    Input Order Baru
                </button>
                <button onclick="showTab('pending-orders')" id="tab-pending-orders" class="flex-1 px-6 py-3 text-sm font-medium text-[#A1A09A] hover:text-[#EDEDEC] transition-colors">
                    Order Pending ({{ $pendingOrders->count() }})
                </button>
                <button onclick="showTab('unpaid-orders')" id="tab-unpaid-orders" class="flex-1 px-6 py-3 text-sm font-medium text-[#A1A09A] hover:text-[#EDEDEC] transition-colors">
                    Belum Bayar ({{ $unpaidOrders->count() }})
                </button>
            </div>

            <!-- Tab Content: New Order -->
            <div id="content-new-order" class="p-6">
                <form action="{{ url('/admin/kasir/order') }}" method="POST" id="order-form">
                    @csrf
                    <input type="hidden" name="mdx_store_id" value="{{ $selectedStore->id }}">

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Left: Menu Grid -->
                        <div class="lg:col-span-2 space-y-4">
                            <!-- Order Info -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-[#EDEDEC] mb-2">Tipe Order *</label>
                                    <select name="order_type" id="order_type" required class="w-full px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#F53003]/50">
                                        <option value="dine_in">Dine In</option>
                                        <option value="takeaway">Takeaway</option>
                                        <option value="delivery">Delivery</option>
                                    </select>
                                </div>
                                <div id="table-field">
                                    <label class="block text-sm font-medium text-[#EDEDEC] mb-2">Meja *</label>
                                    <select name="mdx_table_id" id="mdx_table_id" class="w-full px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#F53003]/50">
                                        <option value="">-- Pilih Meja --</option>
                                        @foreach($tables as $table)
                                            <option value="{{ $table->id }}">{{ $table->name ?? "Meja {$table->table_number}" }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-[#EDEDEC] mb-2">Nama Customer *</label>
                                    <input type="text" name="customer_name" id="customer_name" required class="w-full px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#F53003]/50">
                                </div>
                            </div>

                            <!-- Search Filter -->
                            <div class="mb-4">
                                <input type="text" id="menu-search" placeholder="Cari menu..." class="w-full px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#F53003]/50">
                            </div>

                            <!-- Category Pills -->
                            <div class="mb-4 flex flex-wrap gap-2">
                                <button type="button" onclick="filterByCategory(null)" class="category-filter px-4 py-2 bg-[#F53003] text-white text-sm font-semibold rounded-full hover:bg-[#d42800] transition-colors" data-category="all">
                                    Semua
                                </button>
                                @foreach($categories as $category)
                                    <button type="button" onclick="filterByCategory({{ $category->id }})" class="category-filter px-4 py-2 bg-[#161615] border border-[#3E3E3A] text-[#EDEDEC] text-sm font-semibold rounded-full hover:bg-[#0a0a0a] transition-colors" data-category="{{ $category->id }}">
                                        {{ $category->name }}
                                    </button>
                                @endforeach
                            </div>

                            <!-- Menu Grid -->
                            <div id="menu-grid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                @foreach($menus as $menu)
                                    @php
                                        $imageUrl = '';
                                        if ($menu->image) {
                                            if (str_starts_with($menu->image, 'http://') || str_starts_with($menu->image, 'https://')) {
                                                $imageUrl = $menu->image;
                                            } else {
                                                $imageUrl = asset('storage/' . $menu->image);
                                            }
                                        }
                                        $categoryIds = $menu->categories->pluck('id')->toArray();
                                    @endphp
                                    <div class="menu-item bg-[#0a0a0a] border border-[#3E3E3A] rounded-lg overflow-hidden hover:border-[#F53003]/50 transition-all cursor-pointer" 
                                         data-menu-id="{{ $menu->id }}"
                                         data-menu-name="{{ strtolower($menu->name) }}"
                                         data-categories="{{ json_encode($categoryIds) }}"
                                         onclick="addMenuToCart({{ $menu->id }}, '{{ addslashes($menu->name) }}', {{ $menu->price }}, '{{ $imageUrl }}')">
                                        <div class="aspect-square bg-[#161615] overflow-hidden">
                                            @if($imageUrl)
                                                <img src="{{ $imageUrl }}" alt="{{ $menu->name }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <svg class="w-12 h-12 text-[#3E3E3A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="p-3">
                                            <h3 class="text-sm font-semibold text-[#EDEDEC] mb-1 line-clamp-2">{{ $menu->name }}</h3>
                                            <p class="text-lg font-bold text-[#EDEDEC]">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Right: Order Summary -->
                        <div class="lg:col-span-1">
                            <div class="bg-[#0a0a0a] border border-[#3E3E3A] rounded-lg p-4 sticky top-4">
                                <h3 class="text-lg font-semibold text-[#EDEDEC] mb-4">Order Summary</h3>
                                
                                <!-- Customer Info -->
                                <div class="mb-4 space-y-2">
                                    <input type="text" name="customer_phone" placeholder="No. Telepon" class="w-full px-3 py-2 bg-[#161615] border border-[#3E3E3A] text-[#EDEDEC] rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#F53003]/50">
                                    <div id="delivery-field" class="hidden">
                                        <textarea name="delivery_address" rows="2" placeholder="Alamat Pengiriman *" class="w-full px-3 py-2 bg-[#161615] border border-[#3E3E3A] text-[#EDEDEC] rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#F53003]/50"></textarea>
                                    </div>
                                </div>

                                <!-- Cart Items -->
                                <div id="cart-items" class="space-y-3 mb-4 max-h-96 overflow-y-auto">
                                    <p class="text-sm text-[#A1A09A] text-center py-8">Cart is empty. Add menu items.</p>
                                </div>

                                <!-- Total -->
                                <div class="border-t border-[#3E3E3A] pt-4 mb-4">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-sm text-[#A1A09A]">Total:</span>
                                        <span class="text-xl font-bold text-[#EDEDEC]" id="cart-total">Rp 0</span>
                                    </div>
                                </div>

                                <!-- Notes -->
                                <div class="mb-4">
                                    <textarea name="notes" rows="2" placeholder="Catatan (opsional)" class="w-full px-3 py-2 bg-[#161615] border border-[#3E3E3A] text-[#EDEDEC] rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#F53003]/50"></textarea>
                                </div>

                                <!-- Submit Button -->
                                <button type="submit" class="w-full px-4 py-3 bg-[#F53003] text-white font-semibold rounded-lg hover:bg-[#d42800] transition-colors">
                                    Buat Order
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Tab Content: Pending Orders -->
            <div id="content-pending-orders" class="p-6 hidden">
                @if($pendingOrders->isEmpty())
                    <p class="text-center text-[#A1A09A] py-8">Tidak ada order pending</p>
                @else
                    <div class="space-y-4">
                        @foreach($pendingOrders as $order)
                            <div class="bg-[#0a0a0a] border border-[#3E3E3A] rounded-lg p-4">
                                <div class="flex items-start justify-between mb-3">
                                    <div>
                                        <h3 class="text-lg font-semibold text-[#EDEDEC]">{{ $order->order_number }}</h3>
                                        <p class="text-sm text-[#A1A09A]">{{ $order->customer_name }}</p>
                                        @if($order->table)
                                            <p class="text-xs text-[#A1A09A]">Meja: {{ $order->table->name ?? "Meja {$order->table->table_number}" }}</p>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <p class="text-lg font-bold text-[#EDEDEC]">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                                        <span class="inline-block px-2 py-1 text-xs font-semibold rounded {{ $order->status === 'pending' ? 'bg-yellow-500/20 text-yellow-400' : 'bg-blue-500/20 text-blue-400' }}">
                                            {{ strtoupper($order->status) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <p class="text-sm text-[#EDEDEC] font-medium mb-1">Items:</p>
                                    <ul class="text-sm text-[#A1A09A] space-y-1">
                                        @foreach($order->orderDetails as $detail)
                                            <li>{{ $detail->quantity }}x {{ $detail->menu_name }} - Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="flex gap-2">
                                    <form action="{{ url('/admin/kasir/order/' . $order->id . '/complete') }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition-colors">
                                            Selesaikan Order
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Tab Content: Unpaid Orders -->
            <div id="content-unpaid-orders" class="p-6 hidden">
                @if($unpaidOrders->isEmpty())
                    <p class="text-center text-[#A1A09A] py-8">Tidak ada order yang belum dibayar</p>
                @else
                    <div class="space-y-4">
                        @foreach($unpaidOrders as $order)
                            @php
                                $totalPaid = $order->payments()->where('status', 'completed')->sum('amount');
                                $remainingAmount = $order->total_amount - $totalPaid;
                            @endphp
                            <div class="bg-[#0a0a0a] border border-[#3E3E3A] rounded-lg p-4">
                                <div class="flex items-start justify-between mb-3">
                                    <div>
                                        <h3 class="text-lg font-semibold text-[#EDEDEC]">{{ $order->order_number }}</h3>
                                        <p class="text-sm text-[#A1A09A]">{{ $order->customer_name }}</p>
                                        @if($order->table)
                                            <p class="text-xs text-[#A1A09A]">Meja: {{ $order->table->name ?? "Meja {$order->table->table_number}" }}</p>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <p class="text-lg font-bold text-[#EDEDEC]">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                                        <p class="text-sm text-[#A1A09A]">Sisa: Rp {{ number_format($remainingAmount, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <p class="text-sm text-[#EDEDEC] font-medium mb-1">Items:</p>
                                    <ul class="text-sm text-[#A1A09A] space-y-1">
                                        @foreach($order->orderDetails as $detail)
                                            <li>{{ $detail->quantity }}x {{ $detail->menu_name }} - Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                <button onclick="showPaymentModal({{ $order->id }}, {{ $remainingAmount }})" class="w-full px-4 py-2 bg-[#F53003] text-white text-sm font-semibold rounded-lg hover:bg-[#d42800] transition-colors">
                                    Bayar Order
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Payment Modal -->
        <div id="payment-modal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center" style="display: none;" onclick="closePaymentModal(event)">
            <div class="bg-[#161615] border border-[#3E3E3A] rounded-lg p-6 max-w-md w-full mx-4 relative z-10" onclick="event.stopPropagation()">
                <h3 class="text-xl font-semibold text-[#EDEDEC] mb-4">Proses Pembayaran</h3>
                <form action="#" method="POST" id="payment-form">
                    @csrf
                    <input type="hidden" name="order_id" id="payment-order-id" value="">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-[#EDEDEC] mb-2">Metode Pembayaran *</label>
                            <select name="payment_method" required class="w-full px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#F53003]/50">
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                                <option value="transfer">Transfer</option>
                                <option value="e_wallet">E-Wallet</option>
                                <option value="other">Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#EDEDEC] mb-2">Jumlah Pembayaran *</label>
                            <input type="number" name="amount" id="payment-amount" required step="0.01" min="0" class="w-full px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#F53003]/50">
                            <p class="text-xs text-[#A1A09A] mt-1">Sisa tagihan: <span id="remaining-amount">Rp 0</span></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#EDEDEC] mb-2">Nomor Referensi (opsional)</label>
                            <input type="text" name="reference_number" class="w-full px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#F53003]/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#EDEDEC] mb-2">Catatan (opsional)</label>
                            <textarea name="notes" rows="2" class="w-full px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#F53003]/50"></textarea>
                        </div>
                    </div>
                    <div class="flex gap-3 mt-6">
                        <button type="button" onclick="closePaymentModal()" class="flex-1 px-4 py-2 border border-[#3E3E3A] text-[#EDEDEC] rounded-lg hover:bg-[#0a0a0a] transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="flex-1 px-4 py-2 bg-[#F53003] text-white font-semibold rounded-lg hover:bg-[#d42800] transition-colors">
                            Proses Pembayaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @else
        <!-- No Store Selected -->
        <div class="bg-[#161615] border border-[#3E3E3A] rounded-lg p-8 text-center">
            <svg class="w-16 h-16 text-[#A1A09A] mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
            @if($stores->isEmpty())
                <p class="text-lg font-medium text-[#EDEDEC] mb-2">Tidak Ada Store</p>
                <p class="text-sm text-[#A1A09A]">
                    Tidak ada store yang tersedia untuk Anda
                </p>
            @else
                <p class="text-lg font-medium text-[#EDEDEC] mb-2">Pilih Store</p>
                <p class="text-sm text-[#A1A09A]">
                    Silakan pilih store terlebih dahulu untuk mengakses interface kasir
                </p>
            @endif
        </div>
    @endif
</div>

@push('styles')
<style>
    #payment-modal {
        display: none;
    }
    #payment-modal:not(.hidden) {
        display: flex;
    }
    #payment-modal .relative {
        pointer-events: auto;
    }

    /* Custom Scrollbar for Order Summary */
    #cart-items::-webkit-scrollbar {
        width: 6px;
    }
    #cart-items::-webkit-scrollbar-track {
        background: #0a0a0a;
        border-radius: 3px;
    }
    #cart-items::-webkit-scrollbar-thumb {
        background: #3E3E3A;
        border-radius: 3px;
    }
    #cart-items::-webkit-scrollbar-thumb:hover {
        background: #4a4a46;
    }
    
    /* Firefox scrollbar */
    #cart-items {
        scrollbar-width: thin;
        scrollbar-color: #3E3E3A #0a0a0a;
    }
</style>
@endpush

@push('scripts')
<script>
    // Tab switching
    function showTab(tabName) {
        // Hide all tabs
        document.querySelectorAll('[id^="content-"]').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('[id^="tab-"]').forEach(el => {
            el.classList.remove('border-[#F53003]', 'bg-[#0a0a0a]', 'text-[#EDEDEC]');
            el.classList.add('text-[#A1A09A]');
        });

        // Show selected tab
        document.getElementById('content-' + tabName).classList.remove('hidden');
        const tabButton = document.getElementById('tab-' + tabName);
        tabButton.classList.add('border-[#F53003]', 'bg-[#0a0a0a]', 'text-[#EDEDEC]');
        tabButton.classList.remove('text-[#A1A09A]');
    }

    // Order type change handler
    document.getElementById('order_type')?.addEventListener('change', function() {
        const orderType = this.value;
        const tableField = document.getElementById('table-field');
        const deliveryField = document.getElementById('delivery-field');
        const tableSelect = document.getElementById('mdx_table_id');

        if (orderType === 'dine_in') {
            tableField.classList.remove('hidden');
            deliveryField.classList.add('hidden');
            tableSelect.required = true;
        } else if (orderType === 'delivery') {
            tableField.classList.add('hidden');
            deliveryField.classList.remove('hidden');
            tableSelect.required = false;
        } else {
            tableField.classList.add('hidden');
            deliveryField.classList.add('hidden');
            tableSelect.required = false;
        }
    });

    // Cart management
    let cart = [];
    let itemIndex = 0;

    // Add menu to cart
    function addMenuToCart(menuId, menuName, price, imageUrl) {
        const existingItem = cart.find(item => item.menuId === menuId);
        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            cart.push({
                menuId: menuId,
                menuName: menuName,
                price: price,
                imageUrl: imageUrl,
                quantity: 1,
                notes: ''
            });
        }
        updateCartDisplay();
    }

    // Update cart quantity
    function updateCartQuantity(menuId, change) {
        const item = cart.find(item => item.menuId === menuId);
        if (item) {
            item.quantity = Math.max(1, item.quantity + change);
            if (item.quantity === 0) {
                cart = cart.filter(item => item.menuId !== menuId);
            }
            updateCartDisplay();
        }
    }

    // Remove item from cart
    function removeFromCart(menuId) {
        cart = cart.filter(item => item.menuId !== menuId);
        updateCartDisplay();
    }

    // Update cart display
    function updateCartDisplay() {
        const cartItemsContainer = document.getElementById('cart-items');
        const cartTotal = document.getElementById('cart-total');
        
        if (cart.length === 0) {
            cartItemsContainer.innerHTML = '<p class="text-sm text-[#A1A09A] text-center py-8">Cart is empty. Add menu items.</p>';
            cartTotal.textContent = 'Rp 0';
            return;
        }

        let total = 0;
        let html = '';
        itemIndex = 0;

        cart.forEach(item => {
            const itemTotal = item.price * item.quantity;
            total += itemTotal;
            
            html += `
                <div class="bg-[#161615] border border-[#3E3E3A] rounded-lg p-3">
                    <div class="flex gap-3 mb-2">
                        ${item.imageUrl ? `<img src="${item.imageUrl}" alt="${item.menuName}" class="w-12 h-12 rounded-lg object-cover flex-shrink-0">` : '<div class="w-12 h-12 rounded-lg bg-[#0a0a0a] flex items-center justify-center flex-shrink-0"><svg class="w-6 h-6 text-[#3E3E3A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg></div>'}
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-semibold text-[#EDEDEC] mb-1 truncate">${item.menuName}</h4>
                            <p class="text-xs text-[#EDEDEC]">Rp ${item.price.toLocaleString('id-ID')}</p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="updateCartQuantity(${item.menuId}, -1)" class="w-8 h-8 flex items-center justify-center bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] rounded-lg hover:bg-[#161615] transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                </svg>
                            </button>
                            <span class="text-sm font-semibold text-[#EDEDEC] min-w-[2rem] text-center">${item.quantity}</span>
                            <button type="button" onclick="updateCartQuantity(${item.menuId}, 1)" class="w-8 h-8 flex items-center justify-center bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] rounded-lg hover:bg-[#161615] transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </button>
                        </div>
                        <button type="button" onclick="removeFromCart(${item.menuId})" class="text-red-500 hover:text-red-400 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                    <input type="hidden" name="items[${itemIndex}][mdx_menu_id]" value="${item.menuId}">
                    <input type="hidden" name="items[${itemIndex}][quantity]" value="${item.quantity}">
                    <input type="hidden" name="items[${itemIndex}][unit_price]" value="${item.price}">
                    <input type="hidden" name="items[${itemIndex}][discount_amount]" value="0">
                    <input type="hidden" name="items[${itemIndex}][notes]" value="${item.notes}">
                </div>
            `;
            itemIndex++;
        });

        cartItemsContainer.innerHTML = html;
        cartTotal.textContent = 'Rp ' + total.toLocaleString('id-ID');
    }

    // Filter by category
    function filterByCategory(categoryId) {
        const menuItems = document.querySelectorAll('.menu-item');
        const categoryButtons = document.querySelectorAll('.category-filter');
        
        // Update button states
        categoryButtons.forEach(btn => {
            if (categoryId === null && btn.dataset.category === 'all') {
                btn.classList.remove('bg-[#161615]', 'border-[#3E3E3A]');
                btn.classList.add('bg-[#F53003]', 'text-white');
            } else if (btn.dataset.category == categoryId) {
                btn.classList.remove('bg-[#161615]', 'border-[#3E3E3A]');
                btn.classList.add('bg-[#F53003]', 'text-white');
            } else {
                btn.classList.remove('bg-[#F53003]', 'text-white');
                btn.classList.add('bg-[#161615]', 'border-[#3E3E3A]');
            }
        });

        // Filter menu items
        menuItems.forEach(item => {
            const categories = JSON.parse(item.dataset.categories || '[]');
            const menuName = item.dataset.menuName || '';
            const searchTerm = document.getElementById('menu-search')?.value.toLowerCase() || '';
            
            const matchesCategory = categoryId === null || categories.includes(categoryId);
            const matchesSearch = !searchTerm || menuName.includes(searchTerm);
            
            if (matchesCategory && matchesSearch) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // Search menu
    document.getElementById('menu-search')?.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const activeCategory = document.querySelector('.category-filter.bg-\\[\\#F53003\\]')?.dataset.category;
        const categoryId = activeCategory === 'all' ? null : parseInt(activeCategory);
        filterByCategory(categoryId);
    });

    // OLD: Add order item (kept for backward compatibility but not used)
    function addOrderItem() {
        const itemsContainer = document.getElementById('order-items');
        const itemDiv = document.createElement('div');
        itemDiv.className = 'bg-[#0a0a0a] border border-[#3E3E3A] rounded-lg p-4';
        itemDiv.innerHTML = `
            <div class="flex gap-4 mb-3">
                <!-- Menu Image Preview -->
                <div class="w-20 h-20 rounded-lg overflow-hidden bg-[#161615] border border-[#3E3E3A] flex-shrink-0 flex items-center justify-center menu-image-preview">
                    <svg class="w-8 h-8 text-[#3E3E3A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <!-- Menu Details -->
                <div class="flex-1">
                    <div class="grid grid-cols-12 gap-3">
                        <div class="col-span-12 md:col-span-6">
                            <label class="block text-xs font-medium text-[#EDEDEC] mb-1">Menu *</label>
                            <select name="items[${itemIndex}][mdx_menu_id]" required onchange="updateItemPrice(this)" class="w-full px-3 py-2 bg-[#161615] border border-[#3E3E3A] text-[#EDEDEC] rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#F53003]/50">
                                <option value="">-- Pilih Menu --</option>
                                @foreach($menus as $menu)
                                    @php
                                        $imageUrl = '';
                                        if ($menu->image) {
                                            if (str_starts_with($menu->image, 'http://') || str_starts_with($menu->image, 'https://')) {
                                                $imageUrl = $menu->image;
                                            } else {
                                                $imageUrl = asset('storage/' . $menu->image);
                                            }
                                        }
                                    @endphp
                                    <option value="{{ $menu->id }}" data-price="{{ $menu->price }}" data-image="{{ $imageUrl }}">{{ $menu->name }} - Rp {{ number_format($menu->price, 0, ',', '.') }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-4 md:col-span-2">
                            <label class="block text-xs font-medium text-[#EDEDEC] mb-1">Qty *</label>
                            <input type="number" name="items[${itemIndex}][quantity]" required min="1" value="1" onchange="calculateItemSubtotal(this)" class="w-full px-3 py-2 bg-[#161615] border border-[#3E3E3A] text-[#EDEDEC] rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#F53003]/50">
                        </div>
                        <div class="col-span-4 md:col-span-2">
                            <label class="block text-xs font-medium text-[#EDEDEC] mb-1">Harga</label>
                            <input type="number" name="items[${itemIndex}][unit_price]" step="0.01" min="0" onchange="calculateItemSubtotal(this)" class="item-price w-full px-3 py-2 bg-[#161615] border border-[#3E3E3A] text-[#EDEDEC] rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#F53003]/50">
                        </div>
                        <div class="col-span-4 md:col-span-2">
                            <label class="block text-xs font-medium text-[#EDEDEC] mb-1">Diskon</label>
                            <input type="number" name="items[${itemIndex}][discount_amount]" step="0.01" min="0" value="0" onchange="calculateItemSubtotal(this)" class="w-full px-3 py-2 bg-[#161615] border border-[#3E3E3A] text-[#EDEDEC] rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#F53003]/50">
                        </div>
                        <div class="col-span-12 md:col-span-2 flex items-end">
                            <button type="button" onclick="removeOrderItem(this)" class="w-full px-3 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition-colors">
                                Hapus
                            </button>
                        </div>
                    </div>
                    <div class="mt-2">
                        <input type="text" name="items[${itemIndex}][notes]" placeholder="Catatan (opsional)" class="w-full px-3 py-2 bg-[#161615] border border-[#3E3E3A] text-[#EDEDEC] rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#F53003]/50">
                    </div>
                </div>
            </div>
        `;
        itemsContainer.appendChild(itemDiv);
        itemIndex++;
    }

    function removeOrderItem(button) {
        button.closest('div.bg-\\[\\#0a0a0a\\]').remove();
    }

    // Form submit validation
    document.getElementById('order-form')?.addEventListener('submit', function(e) {
        if (cart.length === 0) {
            e.preventDefault();
            alert('Silakan tambahkan menu ke cart terlebih dahulu.');
            return false;
        }
        // Ensure cart data is in form
        updateCartDisplay();
    });

    // OLD: Update item price (kept for backward compatibility)
    function updateItemPrice(select) {
        const option = select.options[select.selectedIndex];
        const price = option.getAttribute('data-price');
        const imageUrl = option.getAttribute('data-image');
        const itemDiv = select.closest('div.bg-\\[\\#0a0a0a\\]');
        const priceInput = itemDiv.querySelector('.item-price');
        const imagePreview = itemDiv.querySelector('.menu-image-preview');
        
        // Update price
        if (price && priceInput) {
            priceInput.value = price;
            calculateItemSubtotal(priceInput);
        }
        
        // Update image preview
        if (imagePreview) {
            if (imageUrl && imageUrl !== '') {
                imagePreview.innerHTML = `<img src="${imageUrl}" alt="Menu" class="w-full h-full object-cover">`;
            } else {
                imagePreview.innerHTML = `
                    <svg class="w-8 h-8 text-[#3E3E3A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                `;
            }
        }
    }

    function calculateItemSubtotal(input) {
        // This is just for display, actual calculation is done on server
    }

    // Payment modal
    function showPaymentModal(orderId, remainingAmount) {
        if (!orderId || orderId <= 0) {
            console.error('Invalid order ID');
            return;
        }

        const modal = document.getElementById('payment-modal');
        const form = document.getElementById('payment-form');
        const amountInput = document.getElementById('payment-amount');
        const remainingSpan = document.getElementById('remaining-amount');
        const orderIdInput = document.getElementById('payment-order-id');

        if (!modal || !form) {
            console.error('Modal or form not found');
            return;
        }

        // Set form action with full URL
        const paymentUrl = `{{ url('/admin/kasir/order') }}/${orderId}/payment`;
        form.action = paymentUrl;
        
        if (orderIdInput) {
            orderIdInput.value = orderId;
        }
        
        if (amountInput) {
            amountInput.value = remainingAmount;
            amountInput.max = remainingAmount;
        }
        
        if (remainingSpan) {
            remainingSpan.textContent = 'Rp ' + Math.round(remainingAmount).toLocaleString('id-ID');
        }

        // Show modal
        modal.classList.remove('hidden');
        modal.style.display = 'flex';
    }

    function closePaymentModal(event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }
        
        const modal = document.getElementById('payment-modal');
        const form = document.getElementById('payment-form');
        
        if (modal) {
            modal.classList.add('hidden');
            modal.style.display = 'none';
        }
        
        // Reset form
        if (form) {
            form.reset();
            form.action = '#';
            const orderIdInput = document.getElementById('payment-order-id');
            if (orderIdInput) {
                orderIdInput.value = '';
            }
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Ensure payment modal is hidden on page load
        const paymentModal = document.getElementById('payment-modal');
        if (paymentModal) {
            paymentModal.classList.add('hidden');
            paymentModal.style.display = 'none';
        }

        // Initialize cart
        cart = [];
        updateCartDisplay();

        // Initialize category filter - select "Semua" by default
        filterByCategory(null);

        // Ensure order form action is set correctly
        const orderForm = document.getElementById('order-form');
        if (orderForm) {
            if (!orderForm.action || orderForm.action === '') {
                orderForm.action = '{{ url("/admin/kasir/order") }}';
            }

            // Prevent form submit if action is wrong
            orderForm.addEventListener('submit', function(e) {
                if (!this.action || (this.action.includes('/admin/kasir') && !this.action.includes('/admin/kasir/order'))) {
                    e.preventDefault();
                    console.error('Order form action is incorrect:', this.action);
                    alert('Terjadi kesalahan. Silakan refresh halaman dan coba lagi.');
                }
            });
        }

        // Setup payment form validation
        const paymentForm = document.getElementById('payment-form');
        if (paymentForm) {
            paymentForm.addEventListener('submit', function(e) {
                if (!this.action || this.action === '#' || this.action === '') {
                    e.preventDefault();
                    console.error('Payment form action is not set');
                    alert('Terjadi kesalahan. Silakan tutup modal dan coba lagi.');
                    return false;
                }
                
                if (!this.action.includes('/admin/kasir/order/') || !this.action.includes('/payment')) {
                    e.preventDefault();
                    console.error('Payment form action is incorrect:', this.action);
                    alert('Terjadi kesalahan. Silakan tutup modal dan coba lagi.');
                    return false;
                }
            });
        }
    });
</script>
@endpush
@endsection

