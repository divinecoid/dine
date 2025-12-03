@extends('layouts.public')

@section('title', 'Detail Pesanan - ' . ($order->order_number ?? 'Order'))

@section('content')
<div class="min-h-screen bg-[#0a0a0a]">
    <!-- Header -->
    <div class="bg-[#0a0a0a] border-b border-[#3E3E3A] sticky top-0 z-10">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold text-[#EDEDEC]">Detail Pesanan</h1>
                    <p class="text-sm text-[#A1A09A] mt-1">{{ $order->order_number }}</p>
                </div>
                <div class="text-right">
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-500/20 text-yellow-400',
                            'confirmed' => 'bg-blue-500/20 text-blue-400',
                            'preparing' => 'bg-purple-500/20 text-purple-400',
                            'ready' => 'bg-indigo-500/20 text-indigo-400',
                            'completed' => 'bg-green-500/20 text-green-400',
                            'cancelled' => 'bg-[#F53003]/20 text-[#F53003]',
                        ];
                        $statusLabels = [
                            'pending' => 'Menunggu Konfirmasi',
                            'confirmed' => 'Terkonfirmasi',
                            'preparing' => 'Sedang Disiapkan',
                            'ready' => 'Siap',
                            'completed' => 'Selesai',
                            'cancelled' => 'Dibatalkan',
                        ];
                        $statusColor = $statusColors[$order->status] ?? 'bg-[#3E3E3A] text-[#A1A09A]';
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
        <div class="bg-[#161615] rounded-lg border border-[#3E3E3A] p-4 mb-4">
            <h2 class="text-lg font-semibold text-[#EDEDEC] mb-4">Informasi Pesanan</h2>
            
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-[#A1A09A]">Nama Pemesan</span>
                    <span class="text-sm font-medium text-[#EDEDEC]">{{ $order->customer_name }}</span>
                </div>
                
                @if($order->customer_phone)
                <div class="flex justify-between">
                    <span class="text-sm text-[#A1A09A]">No. Telepon</span>
                    <span class="text-sm font-medium text-[#EDEDEC]">{{ $order->customer_phone }}</span>
                </div>
                @endif

                @if($order->table)
                <div class="flex justify-between">
                    <span class="text-sm text-[#A1A09A]">Meja</span>
                    <span class="text-sm font-medium text-[#EDEDEC]">{{ $order->table->name ?? 'Meja ' . $order->table->table_number }}</span>
                </div>
                @endif

                <div class="flex justify-between">
                    <span class="text-sm text-[#A1A09A]">Tipe Pesanan</span>
                    <span class="text-sm font-medium text-[#EDEDEC]">
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
                    <span class="text-sm text-[#A1A09A]">Waktu Pesanan</span>
                    <span class="text-sm font-medium text-[#EDEDEC]">
                        {{ $order->ordered_at ? $order->ordered_at->format('d M Y, H:i') : '-' }}
                    </span>
                </div>

                @if($order->store)
                <div class="flex justify-between">
                    <span class="text-sm text-[#A1A09A]">Store</span>
                    <span class="text-sm font-medium text-[#EDEDEC]">{{ $order->store->name }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Order Items -->
        <div class="bg-[#161615] rounded-lg border border-[#3E3E3A] p-4 mb-4">
            <h2 class="text-lg font-semibold text-[#EDEDEC] mb-4">Item Pesanan</h2>
            
            <div class="space-y-4">
                @foreach($order->orderDetails as $detail)
                    <div class="flex gap-4 pb-4 border-b border-[#3E3E3A] last:border-0 last:pb-0">
                        @if($detail->menu && $detail->menu->image)
                            <div class="w-20 h-20 rounded-lg overflow-hidden bg-[#0a0a0a] flex-shrink-0">
                                <img src="{{ Str::startsWith($detail->menu->image, ['http://', 'https://']) ? $detail->menu->image : asset('storage/' . $detail->menu->image) }}" 
                                     alt="{{ $detail->menu_name }}"
                                     class="w-full h-full object-cover">
                            </div>
                        @else
                            <div class="w-20 h-20 rounded-lg bg-[#0a0a0a] flex items-center justify-center flex-shrink-0">
                                <svg class="w-8 h-8 text-[#3E3E3A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        @endif
                        
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-[#EDEDEC] text-sm mb-1">{{ $detail->menu_name }}</h3>
                            @if($detail->menu_description)
                                <p class="text-xs text-[#A1A09A] mb-2 line-clamp-2">{{ $detail->menu_description }}</p>
                            @endif
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    @if($order->status !== 'cancelled' && $order->status !== 'completed' && $order->canBeCancelled())
                                        <button type="button" 
                                                onclick="updateOrderItemQuantity({{ $detail->id }}, {{ $detail->quantity - 1 }})"
                                                class="w-7 h-7 flex items-center justify-center border border-[#3E3E3A] rounded-md hover:bg-[#0a0a0a] transition-colors {{ $detail->quantity <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                                {{ $detail->quantity <= 1 ? 'disabled' : '' }}
                                                title="Kurangi">
                                            <svg class="w-4 h-4 text-[#EDEDEC]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                            </svg>
                                        </button>
                                        <span class="text-xs text-[#EDEDEC] min-w-[2rem] text-center font-medium" id="qty-{{ $detail->id }}">{{ $detail->quantity }}</span>
                                        <button type="button" 
                                                onclick="updateOrderItemQuantity({{ $detail->id }}, {{ $detail->quantity + 1 }})"
                                                class="w-7 h-7 flex items-center justify-center border border-[#3E3E3A] rounded-md hover:bg-[#0a0a0a] transition-colors"
                                                title="Tambah">
                                            <svg class="w-4 h-4 text-[#EDEDEC]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                        </button>
                                        <span class="text-xs text-[#A1A09A] ml-1">× Rp {{ number_format($detail->unit_price, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-xs text-[#A1A09A]">Qty: {{ $detail->quantity }} × Rp {{ number_format($detail->unit_price, 0, ',', '.') }}</span>
                                    @endif
                                </div>
                                <span class="text-sm font-semibold text-[#EDEDEC]" id="subtotal-{{ $detail->id }}">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</span>
                            </div>
                            @if($detail->notes)
                                <p class="text-xs text-[#A1A09A] mt-1 italic">Note: {{ $detail->notes }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Payment Summary -->
        <div class="bg-[#161615] rounded-lg border border-[#3E3E3A] p-4 mb-4">
            <h2 class="text-lg font-semibold text-[#EDEDEC] mb-4">Ringkasan Pembayaran</h2>
            
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-sm text-[#A1A09A]">Subtotal</span>
                    <span class="text-sm text-[#EDEDEC]">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                </div>
                
                @if($order->discount_amount > 0)
                <div class="flex justify-between text-green-400">
                    <span class="text-sm">Diskon</span>
                    <span class="text-sm">- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                </div>
                @endif

                @if($order->tax_amount > 0)
                <div class="flex justify-between">
                    <span class="text-sm text-[#A1A09A]">Pajak</span>
                    <span class="text-sm text-[#EDEDEC]">Rp {{ number_format($order->tax_amount, 0, ',', '.') }}</span>
                </div>
                @endif

                @if($order->service_charge > 0)
                <div class="flex justify-between">
                    <span class="text-sm text-[#A1A09A]">Service Charge</span>
                    <span class="text-sm text-[#EDEDEC]">Rp {{ number_format($order->service_charge, 0, ',', '.') }}</span>
                </div>
                @endif

                <div class="border-t border-[#3E3E3A] pt-2 mt-2">
                    <div class="flex justify-between">
                        <span class="text-base font-semibold text-[#EDEDEC]">Total</span>
                        <span class="text-base font-bold text-[#EDEDEC]">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="pt-2">
                    @php
                        $paymentStatusColors = [
                            'pending' => 'bg-yellow-500/20 text-yellow-400',
                            'paid' => 'bg-green-500/20 text-green-400',
                            'partial' => 'bg-blue-500/20 text-blue-400',
                            'refunded' => 'bg-[#F53003]/20 text-[#F53003]',
                        ];
                        $paymentStatusLabels = [
                            'pending' => 'Belum Dibayar',
                            'paid' => 'Sudah Dibayar',
                            'partial' => 'Sebagian Dibayar',
                            'refunded' => 'Dikembalikan',
                        ];
                        $paymentStatusColor = $paymentStatusColors[$order->payment_status] ?? 'bg-[#3E3E3A] text-[#A1A09A]';
                        $paymentStatusLabel = $paymentStatusLabels[$order->payment_status] ?? ucfirst($order->payment_status);
                    @endphp
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-[#A1A09A]">Status Pembayaran</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $paymentStatusColor }}">
                            {{ $paymentStatusLabel }}
                        </span>
                    </div>
                    @if($order->payment_method)
                        <div class="flex justify-between mt-1">
                            <span class="text-sm text-[#A1A09A]">Metode Pembayaran</span>
                            <span class="text-sm font-medium text-[#EDEDEC]">
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
        <div class="bg-[#161615] rounded-lg border border-[#3E3E3A] p-4 mb-4">
            <h2 class="text-lg font-semibold text-[#EDEDEC] mb-2">Catatan</h2>
            <p class="text-sm text-[#A1A09A]">{{ $order->notes }}</p>
        </div>
        @endif

        <!-- Payment Button -->
        @php
            $totalPaid = $order->payments()->where('status', 'completed')->sum('amount');
            $remainingAmount = $order->total_amount - $totalPaid;
            $canPay = $order->status !== 'cancelled' && !$order->isClosed() && $remainingAmount > 0;
        @endphp
        @if($canPay)
        <div class="bg-[#161615] rounded-lg border border-[#3E3E3A] p-4 mb-4">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <h2 class="text-lg font-semibold text-[#EDEDEC]">Pembayaran</h2>
                    @if($totalPaid > 0)
                        <p class="text-sm text-[#A1A09A] mt-1">
                            Sudah dibayar: <span class="text-green-400 font-medium">Rp {{ number_format($totalPaid, 0, ',', '.') }}</span>
                        </p>
                    @endif
                    <p class="text-sm text-[#A1A09A] mt-1">
                        Sisa: <span class="text-[#EDEDEC] font-semibold">Rp {{ number_format($remainingAmount, 0, ',', '.') }}</span>
                    </p>
                </div>
                <button type="button" 
                        onclick="showPaymentModal()"
                        class="px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors">
                    Bayar
                </button>
            </div>
        </div>
        @endif

        <!-- Action Buttons -->
        <div class="flex flex-col gap-3">
            @if($order->status !== 'cancelled' && $order->status !== 'completed')
                @if($order->canBeCancelled())
                    <button type="button" 
                            onclick="cancelOrder('{{ $order->order_number }}')"
                            class="w-full px-4 py-3 bg-red-600/20 border border-red-600/50 text-red-400 font-semibold rounded-lg hover:bg-red-600/30 hover:border-red-600/70 transition-all flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Batalkan Pesanan
                    </button>
                @endif
            @endif
            
            @if($order->brand && $order->brand->slug && isset($tableIdentifier))
                <a href="{{ route('public.menu', ['brandSlug' => $order->brand->slug, 'table' => $tableIdentifier]) }}" 
                   class="w-full px-4 py-3 bg-[#161615] border border-[#3E3E3A] text-[#EDEDEC] font-semibold rounded-lg hover:bg-[#0a0a0a] hover:border-[#4a4a46] transition-all text-center flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Kembali ke Menu
                </a>
            @elseif(!isset($tableIdentifier))
                <a href="/" 
                   class="w-full px-4 py-3 bg-[#161615] border border-[#3E3E3A] text-[#EDEDEC] font-semibold rounded-lg hover:bg-[#0a0a0a] hover:border-[#4a4a46] transition-all text-center flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Kembali ke Beranda
                </a>
            @endif
        </div>
    </div>
</div>

<!-- Payment QRIS Modal -->
<div id="paymentModal" class="fixed inset-0 bg-black bg-opacity-70 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-[#161615] border border-[#3E3E3A] rounded-lg shadow-xl max-w-md w-full p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-[#EDEDEC]">Pembayaran QRIS</h3>
            <button type="button" 
                    onclick="closePaymentModal()"
                    class="text-[#A1A09A] hover:text-[#EDEDEC]">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <div class="space-y-4">
            <!-- Payment Info -->
            <div class="bg-[#0a0a0a] border border-[#3E3E3A] rounded-lg px-4 py-3">
                <div class="text-center">
                    <p class="text-sm text-[#A1A09A] mb-1">Total Pembayaran</p>
                    <p class="text-2xl font-bold text-[#EDEDEC]">Rp {{ number_format($remainingAmount, 0, ',', '.') }}</p>
                    @if($totalPaid > 0)
                        <p class="text-xs text-[#A1A09A] mt-1">
                            Sudah dibayar: <span class="text-green-400">Rp {{ number_format($totalPaid, 0, ',', '.') }}</span>
                        </p>
                    @endif
                </div>
            </div>

            <!-- QRIS Display -->
            <div class="bg-white rounded-lg p-6 flex flex-col items-center">
                <div class="mb-4 flex items-center justify-center min-h-[256px]">
                    <img id="qrisImage" src="" alt="QRIS" class="w-64 h-64 mx-auto border-2 border-gray-200 rounded-lg object-contain">
                </div>
                <div class="text-center">
                    <p class="text-sm font-semibold text-gray-700 mb-1">Scan QRIS dengan aplikasi e-wallet Anda</p>
                    <p class="text-xs text-gray-500">Order: <span class="font-medium">{{ $order->order_number }}</span></p>
                    <p class="text-xs text-gray-500 mt-1">Gunakan aplikasi seperti GoPay, OVO, DANA, LinkAja, atau aplikasi bank</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col gap-3">
                <button type="button" 
                        onclick="checkPaymentStatus()"
                        class="w-full px-4 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Cek Status Pembayaran
                </button>
                
                <button type="button" 
                        onclick="downloadQRIS()"
                        class="w-full px-4 py-3 bg-[#3E3E3A] text-white font-semibold rounded-lg hover:bg-[#2a2a28] transition-colors flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Download QRIS
                </button>
            </div>

            <button type="button" 
                    onclick="closePaymentModal()"
                    class="w-full px-4 py-3 border border-[#3E3E3A] text-[#EDEDEC] font-semibold rounded-lg hover:bg-[#0a0a0a] transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- Alert Modal -->
<div id="alertModal" class="fixed inset-0 bg-black bg-opacity-70 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-[#161615] border border-[#3E3E3A] rounded-lg shadow-xl max-w-sm w-full p-6">
        <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 rounded-full" id="alertModalIcon">
            <!-- Icon will be set dynamically -->
        </div>
        
        <h3 class="text-lg font-semibold text-[#EDEDEC] text-center mb-2" id="alertModalTitle">
            <!-- Title will be set dynamically -->
        </h3>
        
        <p class="text-sm text-[#A1A09A] text-center mb-6" id="alertModalMessage">
            <!-- Message will be set dynamically -->
        </p>
        
        <button type="button" 
                onclick="closeAlertModal()"
                class="w-full px-4 py-2 bg-[#F53003] text-white font-semibold rounded-lg hover:bg-[#d42800] transition-colors"
                id="alertModalButton">
            OK
        </button>
    </div>
</div>

<!-- Cancel Order Confirmation Modal -->
<div id="cancelOrderModal" class="fixed inset-0 bg-black bg-opacity-70 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-[#161615] border border-[#3E3E3A] rounded-lg shadow-xl max-w-md w-full p-6">
        <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-[#F53003]/20 rounded-full">
            <svg class="w-8 h-8 text-[#F53003]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
        </div>
        
        <h3 class="text-xl font-bold text-[#EDEDEC] text-center mb-3">
            Batalkan Pesanan?
        </h3>
        
        <div class="bg-yellow-900/30 border border-yellow-700/50 rounded-lg p-4 mb-4">
            <p class="text-sm text-yellow-400 font-semibold mb-2">⚠️ Peringatan:</p>
            <ul class="text-sm text-yellow-300 space-y-1 list-disc list-inside">
                <li>Pesanan yang dibatalkan tidak dapat dikembalikan</li>
                <li>Pesanan akan dihapus dari daftar pesanan Anda</li>
                <li>Tindakan ini tidak dapat dibatalkan</li>
            </ul>
        </div>
        
        <p class="text-sm text-[#A1A09A] text-center mb-6">
            Apakah Anda yakin ingin membatalkan pesanan <span class="font-semibold text-[#EDEDEC]">{{ $order->order_number }}</span>?
        </p>
        
        <div class="flex gap-3">
            <button type="button" 
                    onclick="closeCancelOrderModal()"
                    class="flex-1 px-4 py-2 border border-[#3E3E3A] text-[#EDEDEC] font-semibold rounded-lg hover:bg-[#0a0a0a] transition-colors">
                Batal
            </button>
            <button type="button" 
                    onclick="confirmCancelOrder()"
                    class="flex-1 px-4 py-2 bg-[#F53003] text-white font-semibold rounded-lg hover:bg-[#d42800] transition-colors">
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
    const remainingAmount = @json($remainingAmount);

    // Payment Modal Functions
    let qrisUrl = null;
    let qrisData = null;

    async function showPaymentModal() {
        const modal = document.getElementById('paymentModal');
        const qrisImage = document.getElementById('qrisImage');
        
        // Show modal
        modal.classList.remove('hidden');
        
        // Show loading state
        qrisImage.src = '';
        qrisImage.alt = 'Loading QRIS...';
        qrisImage.classList.add('animate-pulse');
        qrisImage.classList.add('bg-gray-200');
        
        try {
            // Generate QRIS
            const url = `/orders/${orderNumber}/qris${tableIdentifier ? '?table=' + encodeURIComponent(tableIdentifier) : ''}`;
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                }
            });

            const data = await response.json();
            
            if (data.success) {
                qrisUrl = data.data.qris_url;
                qrisData = data.data.qris_data;
                
                // Set QRIS image
                qrisImage.src = qrisUrl;
                qrisImage.alt = 'QRIS';
                qrisImage.classList.remove('animate-pulse');
                qrisImage.classList.remove('bg-gray-200');
            } else {
                showAlert('error', 'Gagal', data.message || 'Gagal memuat QRIS');
                closePaymentModal();
            }
        } catch (error) {
            console.error('Error:', error);
            showAlert('error', 'Error', 'Terjadi kesalahan saat memuat QRIS. Silakan coba lagi.');
            closePaymentModal();
        }
    }

    function closePaymentModal() {
        document.getElementById('paymentModal').classList.add('hidden');
        qrisUrl = null;
        qrisData = null;
    }

    async function checkPaymentStatus() {
        try {
            const url = `/orders/${orderNumber}/payment-status${tableIdentifier ? '?table=' + encodeURIComponent(tableIdentifier) : ''}`;
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                }
            });

            const data = await response.json();
            
            if (data.success) {
                if (data.data.is_paid) {
                    showAlert('success', 'Pembayaran Berhasil', 'Pembayaran Anda telah dikonfirmasi. Terima kasih!', function() {
                        window.location.reload();
                    });
                } else {
                    showAlert('info', 'Belum Dibayar', `Sisa pembayaran: Rp ${parseInt(data.data.remaining_amount).toLocaleString('id-ID')}`);
                }
            } else {
                showAlert('error', 'Gagal', data.message || 'Gagal mengecek status pembayaran');
            }
        } catch (error) {
            console.error('Error:', error);
            showAlert('error', 'Error', 'Terjadi kesalahan saat mengecek status pembayaran. Silakan coba lagi.');
        }
    }

    async function downloadQRIS() {
        if (!qrisUrl) {
            showAlert('error', 'Error', 'QRIS belum dimuat. Silakan tutup dan buka kembali modal pembayaran.');
            return;
        }

        try {
            // Fetch the QRIS image as blob
            const response = await fetch(qrisUrl);
            const blob = await response.blob();
            
            // Create object URL
            const url = window.URL.createObjectURL(blob);
            
            // Create a temporary link to download the image
            const link = document.createElement('a');
            link.href = url;
            link.download = `QRIS-${orderNumber}-${Date.now()}.png`;
            document.body.appendChild(link);
            link.click();
            
            // Cleanup
            document.body.removeChild(link);
            window.URL.revokeObjectURL(url);
        } catch (error) {
            console.error('Error downloading QRIS:', error);
            // Fallback: open in new tab
            window.open(qrisUrl, '_blank');
        }
    }

    // Close payment modal when clicking outside
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('paymentModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closePaymentModal();
            }
        });
    });

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
                showAlert('cancel', 'Berhasil', 'Pesanan berhasil dibatalkan', function() {
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
            iconBgClass = 'bg-green-500/20';
            iconHtml = `
                <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            `;
        } else if (type === 'cancel') {
            iconBgClass = 'bg-[#F53003]/20';
            iconHtml = `
                <svg class="w-8 h-8 text-[#F53003]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            `;
        } else if (type === 'error') {
            iconBgClass = 'bg-[#F53003]/20';
            iconHtml = `
                <svg class="w-8 h-8 text-[#F53003]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            `;
        } else {
            iconBgClass = 'bg-blue-500/20';
            iconHtml = `
                <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        } else if (type === 'cancel' || type === 'error') {
            buttonElement.className = 'w-full px-4 py-2 bg-[#F53003] text-white font-semibold rounded-lg hover:bg-[#d42800] transition-colors';
        } else {
            buttonElement.className = 'w-full px-4 py-2 bg-[#F53003] text-white font-semibold rounded-lg hover:bg-[#d42800] transition-colors';
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

