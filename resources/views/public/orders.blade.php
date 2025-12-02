@extends('layouts.public')

@section('title', 'Daftar Pesanan')

@section('content')
<div class="min-h-screen bg-[#0a0a0a]">
    <!-- Header -->
    <div class="bg-[#0a0a0a] border-b border-[#3E3E3A] sticky top-0 z-10">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold text-[#EDEDEC]">Daftar Pesanan</h1>
                    @if($table)
                        <p class="text-sm text-[#A1A09A] mt-1">{{ $table->name ?? 'Meja ' . $table->table_number }}</p>
                    @endif
                </div>
                @if($table && $table->store && $table->store->brand)
                    <a href="{{ route('public.menu', ['brandSlug' => $table->store->brand->slug, 'table' => $tableIdentifier]) }}" 
                       class="px-4 py-2 text-sm font-medium text-[#A1A09A] hover:text-[#EDEDEC] transition-colors">
                        Kembali ke Menu
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-6 pb-20">
        @if(isset($canCloseTable) && $canCloseTable && $orders->isNotEmpty())
            <div class="bg-[#161615] rounded-lg border border-[#3E3E3A] p-4 mb-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-[#EDEDEC]">Semua pesanan sudah dibayar dan selesai</p>
                        <p class="text-xs text-[#A1A09A] mt-1">Anda dapat menutup pesanan untuk meja ini</p>
                    </div>
                    <button type="button" 
                            onclick="closeTableOrders()"
                            class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition-colors">
                        Tutup Pesanan
                    </button>
                </div>
            </div>
        @endif

        @if($orders->isEmpty())
            <div class="bg-[#161615] rounded-lg border border-[#3E3E3A] p-12 text-center">
                <svg class="w-16 h-16 text-[#3E3E3A] mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="text-lg font-semibold text-[#EDEDEC] mb-2">Belum Ada Pesanan</h3>
                <p class="text-sm text-[#A1A09A] mb-6">Anda belum memiliki pesanan untuk meja ini.</p>
                @if($table && $table->store && $table->store->brand)
                    <a href="{{ route('public.menu', ['brandSlug' => $table->store->brand->slug, 'table' => $tableIdentifier]) }}" 
                       class="inline-block px-6 py-3 bg-[#F53003] text-white font-semibold rounded-lg hover:bg-[#d42800] transition-colors">
                        Lihat Menu
                    </a>
                @endif
            </div>
        @else
            <div class="space-y-4">
                @foreach($orders as $order)
                    <a href="{{ route('public.orders.show', ['orderNumber' => $order->order_number, 'table' => $tableIdentifier]) }}" 
                       class="block bg-[#161615] rounded-lg border border-[#3E3E3A] p-4 hover:border-[#F53003]/50 transition-all">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="text-lg font-semibold text-[#EDEDEC]">{{ $order->order_number }}</h3>
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
                                            'pending' => 'Menunggu',
                                            'confirmed' => 'Terkonfirmasi',
                                            'preparing' => 'Disiapkan',
                                            'ready' => 'Siap',
                                            'completed' => 'Selesai',
                                            'cancelled' => 'Dibatalkan',
                                        ];
                                        $statusColor = $statusColors[$order->status] ?? 'bg-[#3E3E3A] text-[#A1A09A]';
                                        $statusLabel = $statusLabels[$order->status] ?? ucfirst($order->status);
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusColor }}">
                                        {{ $statusLabel }}
                                    </span>
                                </div>
                                <p class="text-sm text-[#A1A09A]">
                                    {{ $order->customer_name }}
                                    @if($order->ordered_at)
                                        • {{ $order->ordered_at->format('d M Y, H:i') }}
                                    @endif
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-[#EDEDEC]">
                                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                </p>
                                @php
                                    $paymentStatusColors = [
                                        'pending' => 'text-yellow-400',
                                        'paid' => 'text-green-400',
                                        'partial' => 'text-blue-400',
                                        'refunded' => 'text-[#F53003]',
                                    ];
                                    $paymentStatusLabels = [
                                        'pending' => 'Belum Dibayar',
                                        'paid' => 'Sudah Dibayar',
                                        'partial' => 'Sebagian',
                                        'refunded' => 'Dikembalikan',
                                    ];
                                    $paymentStatusColor = $paymentStatusColors[$order->payment_status] ?? 'text-[#A1A09A]';
                                    $paymentStatusLabel = $paymentStatusLabels[$order->payment_status] ?? ucfirst($order->payment_status);
                                @endphp
                                <p class="text-xs {{ $paymentStatusColor }} mt-1">
                                    {{ $paymentStatusLabel }}
                                </p>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between pt-3 border-t border-[#3E3E3A]">
                            <div class="text-sm text-[#A1A09A]">
                                <span>{{ $order->orderDetails->count() }} item</span>
                            </div>
                            <div class="flex items-center text-sm text-[#A1A09A]">
                                <span>Lihat Detail</span>
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
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

<!-- Close Order Confirmation Modal -->
<div id="closeOrderModal" class="fixed inset-0 bg-black bg-opacity-70 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-[#161615] border border-[#3E3E3A] rounded-lg shadow-xl max-w-md w-full p-6">
        <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-[#F53003]/20 rounded-full">
            <svg class="w-8 h-8 text-[#F53003]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
        </div>
        
        <h3 class="text-xl font-bold text-[#EDEDEC] text-center mb-3">
            Tutup Semua Pesanan?
        </h3>
        
        <div class="bg-yellow-900/30 border border-yellow-700/50 rounded-lg p-4 mb-4">
            <p class="text-sm text-yellow-400 font-semibold mb-2">⚠️ Peringatan Penting:</p>
            <ul class="text-sm text-yellow-300 space-y-2 list-disc list-inside">
                <li>Setelah ditutup, pesanan tidak akan bisa dilihat lagi</li>
                <li>Anda harus membuat pesanan baru jika ingin menambah makanan</li>
                <li>Tindakan ini tidak dapat dibatalkan</li>
            </ul>
        </div>
        
        <p class="text-sm text-[#A1A09A] text-center mb-6">
            Apakah Anda yakin ingin menutup semua pesanan untuk meja ini?
        </p>
        
        <div class="flex gap-3">
            <button type="button" 
                    onclick="closeCloseOrderModal()"
                    class="flex-1 px-4 py-2 border border-[#3E3E3A] text-[#EDEDEC] font-semibold rounded-lg hover:bg-[#0a0a0a] transition-colors">
                Batal
            </button>
            <button type="button" 
                    onclick="confirmCloseTableOrders()"
                    class="flex-1 px-4 py-2 bg-[#F53003] text-white font-semibold rounded-lg hover:bg-[#d42800] transition-colors">
                Ya, Tutup Pesanan
            </button>
        </div>
    </div>
</div>

<script>
    function showCloseOrderModal() {
        document.getElementById('closeOrderModal').classList.remove('hidden');
    }

    function closeCloseOrderModal() {
        document.getElementById('closeOrderModal').classList.add('hidden');
    }

    async function confirmCloseTableOrders() {
        closeCloseOrderModal();
        
        const tableId = @json($tableIdentifier);

        try {
            const response = await fetch('/orders/close-table?table=' + encodeURIComponent(tableId), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            
            if (data.success) {
                showAlert('success', 'Berhasil', data.message || 'Pesanan berhasil ditutup', function() {
                    window.location.reload();
                });
            } else {
                showAlert('error', 'Gagal', 'Gagal menutup pesanan: ' + (data.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error:', error);
            showAlert('error', 'Error', 'Terjadi kesalahan saat menutup pesanan. Silakan coba lagi.');
        }
    }

    async function closeTableOrders() {
        showCloseOrderModal();
    }

    // Close modal when clicking outside
    document.getElementById('closeOrderModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeCloseOrderModal();
        }
    });

    // Alert Modal Functions
    function showAlert(type, title, message, callback = null) {
        const modal = document.getElementById('alertModal');
        const icon = document.getElementById('alertModalIcon');
        const titleEl = document.getElementById('alertModalTitle');
        const messageEl = document.getElementById('alertModalMessage');
        const button = document.getElementById('alertModalButton');
        
        // Set title and message
        titleEl.textContent = title;
        messageEl.textContent = message;
        
        // Set icon based on type
        icon.innerHTML = '';
        if (type === 'success') {
            icon.className = 'flex items-center justify-center w-16 h-16 mx-auto mb-4 rounded-full bg-green-500/20';
            icon.innerHTML = '<svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
        } else if (type === 'error') {
            icon.className = 'flex items-center justify-center w-16 h-16 mx-auto mb-4 rounded-full bg-[#F53003]/20';
            icon.innerHTML = '<svg class="w-8 h-8 text-[#F53003]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
        } else {
            icon.className = 'flex items-center justify-center w-16 h-16 mx-auto mb-4 rounded-full bg-blue-500/20';
            icon.innerHTML = '<svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
        }
        
        // Show modal
        modal.classList.remove('hidden');
        
        // Handle button click
        button.onclick = function() {
            closeAlertModal();
            if (callback && typeof callback === 'function') {
                callback();
            }
        };
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
@endsection

