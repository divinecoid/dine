@extends('layouts.public')

@section('title', 'Detail Pesanan - ' . ($order->order_number ?? 'Order'))

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200 sticky top-0 z-10">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold text-gray-900">Detail Pesanan</h1>
                    <p class="text-sm text-gray-500 mt-1">{{ $order->order_number }}</p>
                </div>
                <div class="text-right">
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'confirmed' => 'bg-blue-100 text-blue-800',
                            'preparing' => 'bg-purple-100 text-purple-800',
                            'ready' => 'bg-indigo-100 text-indigo-800',
                            'completed' => 'bg-green-100 text-green-800',
                            'cancelled' => 'bg-red-100 text-red-800',
                        ];
                        $statusLabels = [
                            'pending' => 'Menunggu Konfirmasi',
                            'confirmed' => 'Terkonfirmasi',
                            'preparing' => 'Sedang Disiapkan',
                            'ready' => 'Siap',
                            'completed' => 'Selesai',
                            'cancelled' => 'Dibatalkan',
                        ];
                        $statusColor = $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800';
                        $statusLabel = $statusLabels[$order->status] ?? ucfirst($order->status);
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $statusColor }}">
                        {{ $statusLabel }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-6 pb-20">
        <!-- Order Information -->
        <div class="bg-white rounded-lg border border-gray-200 p-4 mb-4">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Pesanan</h2>
            
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Nama Pemesan</span>
                    <span class="text-sm font-medium text-gray-900">{{ $order->customer_name }}</span>
                </div>
                
                @if($order->customer_phone)
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">No. Telepon</span>
                    <span class="text-sm font-medium text-gray-900">{{ $order->customer_phone }}</span>
                </div>
                @endif

                @if($order->table)
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Meja</span>
                    <span class="text-sm font-medium text-gray-900">{{ $order->table->name ?? 'Meja ' . $order->table->table_number }}</span>
                </div>
                @endif

                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Tipe Pesanan</span>
                    <span class="text-sm font-medium text-gray-900">
                        @if($order->order_type === 'dine_in')
                            Dine In
                        @elseif($order->order_type === 'takeaway')
                            Takeaway
                        @else
                            Delivery
                        @endif
                    </span>
                </div>

                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Waktu Pesanan</span>
                    <span class="text-sm font-medium text-gray-900">
                        {{ $order->ordered_at ? $order->ordered_at->format('d M Y, H:i') : '-' }}
                    </span>
                </div>

                @if($order->store)
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Store</span>
                    <span class="text-sm font-medium text-gray-900">{{ $order->store->name }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Order Items -->
        <div class="bg-white rounded-lg border border-gray-200 p-4 mb-4">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Item Pesanan</h2>
            
            <div class="space-y-4">
                @foreach($order->orderDetails as $detail)
                    <div class="flex gap-4 pb-4 border-b border-gray-100 last:border-0 last:pb-0">
                        @if($detail->menu && $detail->menu->image)
                            <div class="w-20 h-20 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0">
                                <img src="{{ Str::startsWith($detail->menu->image, ['http://', 'https://']) ? $detail->menu->image : asset('storage/' . $detail->menu->image) }}" 
                                     alt="{{ $detail->menu_name }}"
                                     class="w-full h-full object-cover">
                            </div>
                        @else
                            <div class="w-20 h-20 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        @endif
                        
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-gray-900 text-sm mb-1">{{ $detail->menu_name }}</h3>
                            @if($detail->menu_description)
                                <p class="text-xs text-gray-500 mb-2 line-clamp-2">{{ $detail->menu_description }}</p>
                            @endif
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    @if($order->status !== 'cancelled' && $order->status !== 'completed' && $order->canBeCancelled())
                                        <button type="button" 
                                                onclick="updateOrderItemQuantity({{ $detail->id }}, {{ $detail->quantity - 1 }})"
                                                class="w-7 h-7 flex items-center justify-center border border-gray-300 rounded-md hover:bg-gray-50 transition-colors {{ $detail->quantity <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                                {{ $detail->quantity <= 1 ? 'disabled' : '' }}
                                                title="Kurangi">
                                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                            </svg>
                                        </button>
                                        <span class="text-xs text-gray-600 min-w-[2rem] text-center font-medium" id="qty-{{ $detail->id }}">{{ $detail->quantity }}</span>
                                        <button type="button" 
                                                onclick="updateOrderItemQuantity({{ $detail->id }}, {{ $detail->quantity + 1 }})"
                                                class="w-7 h-7 flex items-center justify-center border border-gray-300 rounded-md hover:bg-gray-50 transition-colors"
                                                title="Tambah">
                                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                        </button>
                                        <span class="text-xs text-gray-500 ml-1">× Rp {{ number_format($detail->unit_price, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-xs text-gray-600">Qty: {{ $detail->quantity }} × Rp {{ number_format($detail->unit_price, 0, ',', '.') }}</span>
                                    @endif
                                </div>
                                <span class="text-sm font-semibold text-gray-900" id="subtotal-{{ $detail->id }}">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</span>
                            </div>
                            @if($detail->notes)
                                <p class="text-xs text-gray-500 mt-1 italic">Note: {{ $detail->notes }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Payment Summary -->
        <div class="bg-white rounded-lg border border-gray-200 p-4 mb-4">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Ringkasan Pembayaran</h2>
            
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Subtotal</span>
                    <span class="text-sm text-gray-900">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                </div>
                
                @if($order->discount_amount > 0)
                <div class="flex justify-between text-green-600">
                    <span class="text-sm">Diskon</span>
                    <span class="text-sm">- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                </div>
                @endif

                @if($order->tax_amount > 0)
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Pajak</span>
                    <span class="text-sm text-gray-900">Rp {{ number_format($order->tax_amount, 0, ',', '.') }}</span>
                </div>
                @endif

                @if($order->service_charge > 0)
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Service Charge</span>
                    <span class="text-sm text-gray-900">Rp {{ number_format($order->service_charge, 0, ',', '.') }}</span>
                </div>
                @endif

                <div class="border-t border-gray-200 pt-2 mt-2">
                    <div class="flex justify-between">
                        <span class="text-base font-semibold text-gray-900">Total</span>
                        <span class="text-base font-bold text-gray-900">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="pt-2">
                    @php
                        $paymentStatusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'paid' => 'bg-green-100 text-green-800',
                            'partial' => 'bg-blue-100 text-blue-800',
                            'refunded' => 'bg-red-100 text-red-800',
                        ];
                        $paymentStatusLabels = [
                            'pending' => 'Belum Dibayar',
                            'paid' => 'Sudah Dibayar',
                            'partial' => 'Sebagian Dibayar',
                            'refunded' => 'Dikembalikan',
                        ];
                        $paymentStatusColor = $paymentStatusColors[$order->payment_status] ?? 'bg-gray-100 text-gray-800';
                        $paymentStatusLabel = $paymentStatusLabels[$order->payment_status] ?? ucfirst($order->payment_status);
                    @endphp
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Status Pembayaran</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $paymentStatusColor }}">
                            {{ $paymentStatusLabel }}
                        </span>
                    </div>
                    @if($order->payment_method)
                        <div class="flex justify-between mt-1">
                            <span class="text-sm text-gray-600">Metode Pembayaran</span>
                            <span class="text-sm font-medium text-gray-900">
                                @if($order->payment_method === 'cash')
                                    Tunai
                                @elseif($order->payment_method === 'card')
                                    Kartu
                                @elseif($order->payment_method === 'transfer')
                                    Transfer
                                @elseif($order->payment_method === 'e_wallet')
                                    E-Wallet
                                @else
                                    {{ ucfirst($order->payment_method) }}
                                @endif
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Notes -->
        @if($order->notes)
        <div class="bg-white rounded-lg border border-gray-200 p-4 mb-4">
            <h2 class="text-lg font-semibold text-gray-900 mb-2">Catatan</h2>
            <p class="text-sm text-gray-600">{{ $order->notes }}</p>
        </div>
        @endif

        <!-- Action Buttons -->
        <div class="flex flex-col md:flex-row gap-3">
            @if($order->status !== 'cancelled' && $order->status !== 'completed')
                @if($order->canBeCancelled())
                    <button type="button" 
                            onclick="cancelOrder('{{ $order->order_number }}')"
                            class="w-full md:flex-1 px-4 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-colors">
                        Batalkan Pesanan
                    </button>
                @endif
            @endif
            
            @if(isset($tableIdentifier))
                <a href="{{ route('public.orders.index', ['table' => $tableIdentifier]) }}" 
                   class="w-full md:flex-1 px-4 py-3 bg-gray-600 text-white font-semibold rounded-lg hover:bg-gray-700 transition-colors text-center">
                    Kembali ke Daftar Pesanan
                </a>
            @endif
            
            @if($order->brand && $order->brand->slug && isset($tableIdentifier))
                <a href="{{ route('public.menu', ['brandSlug' => $order->brand->slug, 'table' => $tableIdentifier]) }}" 
                   class="w-full md:flex-1 px-4 py-3 bg-gray-900 text-white font-semibold rounded-lg hover:bg-gray-800 transition-colors text-center">
                    Kembali ke Menu
                </a>
            @elseif(!isset($tableIdentifier))
                <a href="/" 
                   class="w-full md:flex-1 px-4 py-3 bg-gray-900 text-white font-semibold rounded-lg hover:bg-gray-800 transition-colors text-center">
                    Kembali ke Beranda
                </a>
            @endif
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

<!-- Cancel Order Confirmation Modal -->
<div id="cancelOrderModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-red-100 rounded-full">
            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
        </div>
        
        <h3 class="text-xl font-bold text-gray-900 text-center mb-3">
            Batalkan Pesanan?
        </h3>
        
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
            <p class="text-sm text-yellow-800 font-semibold mb-2">⚠️ Peringatan:</p>
            <ul class="text-sm text-yellow-700 space-y-1 list-disc list-inside">
                <li>Pesanan yang dibatalkan tidak dapat dikembalikan</li>
                <li>Pesanan akan dihapus dari daftar pesanan Anda</li>
                <li>Tindakan ini tidak dapat dibatalkan</li>
            </ul>
        </div>
        
        <p class="text-sm text-gray-600 text-center mb-6">
            Apakah Anda yakin ingin membatalkan pesanan <span class="font-semibold text-gray-900">{{ $order->order_number }}</span>?
        </p>
        
        <div class="flex gap-3">
            <button type="button" 
                    onclick="closeCancelOrderModal()"
                    class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors">
                Batal
            </button>
            <button type="button" 
                    onclick="confirmCancelOrder()"
                    class="flex-1 px-4 py-2 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-colors">
                Ya, Batalkan Pesanan
            </button>
        </div>
    </div>
</div>

<script>
    const orderNumber = @json($order->order_number);
    const orderId = @json($order->id);
    const tableIdentifier = @json($tableIdentifier ?? null);
    const brandSlug = @json($order->brand && $order->brand->slug ? $order->brand->slug : null);

    function cancelOrder(orderNumber) {
        // Show cancel order confirmation modal
        showCancelOrderModal();
    }

    function showCancelOrderModal() {
        document.getElementById('cancelOrderModal').classList.remove('hidden');
    }

    function closeCancelOrderModal() {
        document.getElementById('cancelOrderModal').classList.add('hidden');
    }

    async function confirmCancelOrder() {
        closeCancelOrderModal();

        try {
            const response = await fetch(`/api/v1/orders/${orderNumber}/cancel`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            
            if (data.success) {
                showAlert('success', 'Berhasil', 'Pesanan berhasil dibatalkan', function() {
                    // Redirect to menu with table parameter if available
                    if (brandSlug && tableIdentifier) {
                        window.location.href = '/' + brandSlug + '?table=' + encodeURIComponent(tableIdentifier);
                    } else if (tableIdentifier) {
                        // If no brand slug, redirect to order list
                        window.location.href = '/orders?table=' + encodeURIComponent(tableIdentifier);
                    } else {
                        // Fallback: reload page
                        window.location.reload();
                    }
                });
            } else {
                showAlert('error', 'Gagal', 'Gagal membatalkan pesanan: ' + (data.message || 'Unknown error'));
                if (data.errors) {
                    console.error('Validation errors:', data.errors);
                }
            }
        } catch (error) {
            console.error('Error:', error);
            showAlert('error', 'Error', 'Terjadi kesalahan saat membatalkan pesanan. Silakan coba lagi.');
        }
    }

    // Close cancel order modal when clicking outside
    document.getElementById('cancelOrderModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeCancelOrderModal();
        }
    });

    async function updateOrderItemQuantity(orderDetailId, newQuantity) {
        if (newQuantity < 1) {
            showAlert('error', 'Error', 'Jumlah tidak boleh kurang dari 1');
            return;
        }

        try {
            const response = await fetch(`/api/v1/orders/${orderId}/items/${orderDetailId}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    quantity: newQuantity
                })
            });

            const data = await response.json();
            
            if (data.success) {
                // Update UI
                const qtyElement = document.getElementById(`qty-${orderDetailId}`);
                const subtotalElement = document.getElementById(`subtotal-${orderDetailId}`);
                
                if (qtyElement) {
                    qtyElement.textContent = data.data.quantity;
                }
                
                if (subtotalElement) {
                    subtotalElement.textContent = 'Rp ' + parseInt(data.data.subtotal).toLocaleString('id-ID');
                }
                
                // Reload page to update totals and order summary
                window.location.reload();
            } else {
                showAlert('error', 'Gagal', 'Gagal mengupdate jumlah: ' + (data.message || 'Unknown error'));
                if (data.errors) {
                    console.error('Validation errors:', data.errors);
                }
            }
        } catch (error) {
            console.error('Error:', error);
            showAlert('error', 'Error', 'Terjadi kesalahan saat mengupdate jumlah. Silakan coba lagi.');
        }
    }

    // Alert Modal Functions
    function showAlert(type, title, message, callback = null) {
        const modal = document.getElementById('alertModal');
        const iconContainer = document.getElementById('alertModalIcon');
        const titleElement = document.getElementById('alertModalTitle');
        const messageElement = document.getElementById('alertModalMessage');
        const buttonElement = document.getElementById('alertModalButton');
        
        // Set icon based on type
        let iconHtml = '';
        let iconBgClass = '';
        
        if (type === 'success') {
            iconBgClass = 'bg-green-100';
            iconHtml = `
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            `;
        } else if (type === 'error') {
            iconBgClass = 'bg-red-100';
            iconHtml = `
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            `;
        } else {
            iconBgClass = 'bg-blue-100';
            iconHtml = `
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            `;
        }
        
        iconContainer.className = `flex items-center justify-center w-16 h-16 mx-auto mb-4 rounded-full ${iconBgClass}`;
        iconContainer.innerHTML = iconHtml;
        
        titleElement.textContent = title;
        messageElement.textContent = message;
        
        // Set button color based on type
        if (type === 'success') {
            buttonElement.className = 'w-full px-4 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors';
        } else if (type === 'error') {
            buttonElement.className = 'w-full px-4 py-2 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-colors';
        } else {
            buttonElement.className = 'w-full px-4 py-2 bg-gray-900 text-white font-semibold rounded-lg hover:bg-gray-800 transition-colors';
        }
        
        // Remove old event listener and add new one
        const newButton = buttonElement.cloneNode(true);
        buttonElement.parentNode.replaceChild(newButton, buttonElement);
        
        newButton.addEventListener('click', function() {
            closeAlertModal();
            if (callback && typeof callback === 'function') {
                callback();
            }
        });
        
        modal.classList.remove('hidden');
    }

    function closeAlertModal() {
        document.getElementById('alertModal').classList.add('hidden');
    }

    // Close alert modal when clicking outside
    document.getElementById('alertModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeAlertModal();
        }
    });
</script>

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endsection

