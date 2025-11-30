@extends('layouts.public')

@section('title', 'Daftar Pesanan')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200 sticky top-0 z-10">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold text-gray-900">Daftar Pesanan</h1>
                    @if($table)
                        <p class="text-sm text-gray-500 mt-1">{{ $table->name ?? 'Meja ' . $table->table_number }}</p>
                    @endif
                </div>
                @if($table && $table->store && $table->store->brand)
                    <a href="{{ route('public.menu', ['brandSlug' => $table->store->brand->slug, 'table' => $tableIdentifier]) }}" 
                       class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900">
                        Kembali ke Menu
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-6 pb-20">
        @if(isset($canCloseTable) && $canCloseTable && $orders->isNotEmpty())
            <div class="bg-white rounded-lg border border-gray-200 p-4 mb-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Semua pesanan sudah dibayar dan selesai</p>
                        <p class="text-xs text-gray-500 mt-1">Anda dapat menutup pesanan untuk meja ini</p>
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
            <div class="bg-white rounded-lg border border-gray-200 p-12 text-center">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Pesanan</h3>
                <p class="text-sm text-gray-600 mb-6">Anda belum memiliki pesanan untuk meja ini.</p>
                @if($table && $table->store && $table->store->brand)
                    <a href="{{ route('public.menu', ['brandSlug' => $table->store->brand->slug, 'table' => $tableIdentifier]) }}" 
                       class="inline-block px-6 py-3 bg-gray-900 text-white font-semibold rounded-lg hover:bg-gray-800 transition-colors">
                        Lihat Menu
                    </a>
                @endif
            </div>
        @else
            <div class="space-y-4">
                @foreach($orders as $order)
                    <a href="{{ route('public.orders.show', ['orderNumber' => $order->order_number, 'table' => $tableIdentifier]) }}" 
                       class="block bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $order->order_number }}</h3>
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
                                            'pending' => 'Menunggu',
                                            'confirmed' => 'Terkonfirmasi',
                                            'preparing' => 'Disiapkan',
                                            'ready' => 'Siap',
                                            'completed' => 'Selesai',
                                            'cancelled' => 'Dibatalkan',
                                        ];
                                        $statusColor = $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800';
                                        $statusLabel = $statusLabels[$order->status] ?? ucfirst($order->status);
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusColor }}">
                                        {{ $statusLabel }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600">
                                    {{ $order->customer_name }}
                                    @if($order->ordered_at)
                                        • {{ $order->ordered_at->format('d M Y, H:i') }}
                                    @endif
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-gray-900">
                                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                </p>
                                @php
                                    $paymentStatusColors = [
                                        'pending' => 'text-yellow-600',
                                        'paid' => 'text-green-600',
                                        'partial' => 'text-blue-600',
                                        'refunded' => 'text-red-600',
                                    ];
                                    $paymentStatusLabels = [
                                        'pending' => 'Belum Dibayar',
                                        'paid' => 'Sudah Dibayar',
                                        'partial' => 'Sebagian',
                                        'refunded' => 'Dikembalikan',
                                    ];
                                    $paymentStatusColor = $paymentStatusColors[$order->payment_status] ?? 'text-gray-600';
                                    $paymentStatusLabel = $paymentStatusLabels[$order->payment_status] ?? ucfirst($order->payment_status);
                                @endphp
                                <p class="text-xs {{ $paymentStatusColor }} mt-1">
                                    {{ $paymentStatusLabel }}
                                </p>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                            <div class="text-sm text-gray-600">
                                <span>{{ $order->orderDetails->count() }} item</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
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

<!-- Close Order Confirmation Modal -->
<div id="closeOrderModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-red-100 rounded-full">
            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
        </div>
        
        <h3 class="text-xl font-bold text-gray-900 text-center mb-3">
            Tutup Semua Pesanan?
        </h3>
        
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
            <p class="text-sm text-yellow-800 font-semibold mb-2">⚠️ Peringatan Penting:</p>
            <ul class="text-sm text-yellow-700 space-y-2 list-disc list-inside">
                <li>Setelah ditutup, pesanan tidak akan bisa dilihat lagi</li>
                <li>Anda harus membuat pesanan baru jika ingin menambah makanan</li>
                <li>Tindakan ini tidak dapat dibatalkan</li>
            </ul>
        </div>
        
        <p class="text-sm text-gray-600 text-center mb-6">
            Apakah Anda yakin ingin menutup semua pesanan untuk meja ini?
        </p>
        
        <div class="flex gap-3">
            <button type="button" 
                    onclick="closeCloseOrderModal()"
                    class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors">
                Batal
            </button>
            <button type="button" 
                    onclick="confirmCloseTableOrders()"
                    class="flex-1 px-4 py-2 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-colors">
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
                alert(data.message || 'Pesanan berhasil ditutup');
                window.location.reload();
            } else {
                alert('Gagal menutup pesanan: ' + (data.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menutup pesanan. Silakan coba lagi.');
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
</script>
@endsection

