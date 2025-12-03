<?php

namespace App\Http\Controllers;

use App\Models\v1\Order;
use App\Models\v1\Payment;
use App\Models\v1\Table;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PublicOrderController extends Controller
{
    /**
     * Display list of orders for a table.
     * If there's an incomplete order, redirect to its detail page.
     */
    public function index(Request $request): View|RedirectResponse
    {
        // Get table from query parameter (required from QR code)
        $tableIdentifier = $request->query('table');
        
        // If no table, show error page
        if (!$tableIdentifier) {
            return view('public.table-error', [
                'error' => 'QR code meja tidak ditemukan. Silakan scan QR code yang ada di meja restoran untuk melihat pesanan.',
            ]);
        }

        // Find table by unique identifier
        $table = Table::findByUniqueIdentifier($tableIdentifier);
        
        if (!$table) {
            return view('public.table-error', [
                'error' => 'QR code meja tidak valid. Silakan scan QR code yang benar.',
            ]);
        }
        
        // Load store relationship
        $table->load('store');

        // Get orders for this table (exclude closed and cancelled orders)
        $orders = Order::with(['store', 'brand', 'orderDetails.menu'])
            ->where('mdx_table_id', $table->id)
            ->where('status', '!=', 'cancelled')
            ->notClosed()
            ->orderBy('ordered_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Check if there's an incomplete order (not completed) - redirect to latest incomplete order
        $latestIncompleteOrder = $orders->firstWhere('status', '!=', 'completed');
        
        if ($latestIncompleteOrder) {
            // Redirect to the latest incomplete order detail
            return redirect()->route('public.orders.show', [
                'orderNumber' => $latestIncompleteOrder->order_number,
                'table' => $tableIdentifier
            ]);
        }

        // Check if table can be closed (all orders paid and completed)
        $canCloseTable = Order::canCloseTable($table->id);

        return view('public.orders', [
            'orders' => $orders,
            'table' => $table,
            'tableIdentifier' => $tableIdentifier,
            'canCloseTable' => $canCloseTable,
        ]);
    }

    /**
     * Display order details by order number.
     */
    public function show(Request $request, string $orderNumber): View
    {
        // Get table from query parameter (required from QR code)
        $tableIdentifier = $request->query('table');
        
        $order = Order::with(['store', 'brand', 'table', 'orderDetails.menu', 'payments'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        // If no table identifier in query, try to get from order's table
        if (!$tableIdentifier && $order->table) {
            $tableIdentifier = $order->table->unique_identifier;
        }

        // If still no table_id, show error page (must come from QR code)
        if (!$tableIdentifier) {
            return view('public.table-error', [
                'error' => 'QR code meja tidak ditemukan. Silakan scan QR code yang ada di meja restoran untuk melihat pesanan.',
            ]);
        }

        // Validate that order belongs to the table
        if (!$order->table || $order->table->unique_identifier !== $tableIdentifier) {
            return view('public.table-error', [
                'error' => 'Pesanan tidak terkait dengan meja ini. Pastikan Anda scan QR code meja yang benar.',
            ]);
        }

        // Check if order is cancelled
        if ($order->status === 'cancelled') {
            return view('public.table-error', [
                'error' => 'Pesanan ini sudah dibatalkan dan tidak dapat dilihat lagi.',
            ]);
        }

        // Check if order is closed
        if ($order->isClosed()) {
            return view('public.table-error', [
                'error' => 'Pesanan ini sudah ditutup dan tidak dapat dilihat lagi.',
            ]);
        }

        return view('public.order', [
            'order' => $order,
            'tableIdentifier' => $tableIdentifier,
        ]);
    }

    /**
     * Generate QRIS for payment (dummy).
     */
    public function generateQRIS(Request $request, string $orderNumber)
    {
        $tableIdentifier = $request->query('table');
        
        $order = Order::with(['store', 'brand', 'table', 'payments'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        // Validate table identifier
        if (!$tableIdentifier && $order->table) {
            $tableIdentifier = $order->table->unique_identifier;
        }

        if (!$tableIdentifier) {
            return response()->json([
                'success' => false,
                'message' => 'Table identifier is required',
            ], 400);
        }

        // Validate that order belongs to the table
        if (!$order->table || $order->table->unique_identifier !== $tableIdentifier) {
            return response()->json([
                'success' => false,
                'message' => 'Order does not belong to this table',
            ], 403);
        }

        // Check if order is cancelled or closed
        if ($order->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot generate QRIS for cancelled order',
            ], 422);
        }

        if ($order->isClosed()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot generate QRIS for closed order',
            ], 422);
        }

        // Calculate remaining amount
        $totalPaid = $order->payments()
            ->where('status', 'completed')
            ->sum('amount');
        $remainingAmount = $order->total_amount - $totalPaid;

        if ($remainingAmount <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Order is already fully paid',
            ], 422);
        }

        // Generate dummy QRIS data
        // Format: QRIS dummy dengan data order (simulasi format EMV QR Code)
        $merchantName = $order->store ? $order->store->name : 'DINE.CO.ID';
        $merchantId = 'DINE' . str_pad($order->mdx_store_id ?? 0, 10, '0', STR_PAD_LEFT);
        
        // Format QRIS dummy (EMV QR Code format - simplified for dummy)
        // Structure: 000201 (Payload Format Indicator) + merchant data + amount
        $merchantNamePadded = str_pad(substr($merchantName, 0, 25), 25);
        $orderNumberPadded = str_pad(substr($order->order_number, 0, 25), 25);
        $amountFormatted = str_pad((int)($remainingAmount), 13, '0', STR_PAD_LEFT);
        
        // Build QRIS content (dummy EMV format)
        $qrisContent = '00020101021226650016COM.DINE.CO.ID0104PAY';
        $qrisContent .= '5204000053033605802ID';
        $qrisContent .= '59' . str_pad(strlen($merchantNamePadded), 2, '0', STR_PAD_LEFT) . $merchantNamePadded;
        $qrisContent .= '60' . str_pad(strlen('JAKARTA'), 2, '0', STR_PAD_LEFT) . 'JAKARTA';
        $qrisContent .= '61' . str_pad(strlen('12345'), 2, '0', STR_PAD_LEFT) . '12345';
        $qrisContent .= '62' . str_pad(strlen($orderNumberPadded), 2, '0', STR_PAD_LEFT) . $orderNumberPadded;
        $qrisContent .= '54' . str_pad(strlen($amountFormatted), 2, '0', STR_PAD_LEFT) . $amountFormatted;
        $qrisContent .= '6304'; // CRC placeholder
        
        // Generate QR code URL (using qr-server.com API for dummy)
        $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($qrisContent);
        
        // Store QRIS data for reference
        $qrisData = [
            'order_number' => $order->order_number,
            'amount' => $remainingAmount,
            'merchant_name' => $merchantName,
            'merchant_id' => $merchantId,
            'timestamp' => now()->toIso8601String(),
            'qris_content' => $qrisContent,
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'qris_url' => $qrCodeUrl,
                'qris_data' => $qrisData,
                'amount' => $remainingAmount,
                'order_number' => $order->order_number,
            ],
        ]);
    }

    /**
     * Check payment status for an order.
     */
    public function checkPaymentStatus(Request $request, string $orderNumber)
    {
        $tableIdentifier = $request->query('table');
        
        $order = Order::with(['store', 'brand', 'table', 'payments'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        // Validate table identifier
        if (!$tableIdentifier && $order->table) {
            $tableIdentifier = $order->table->unique_identifier;
        }

        if (!$tableIdentifier) {
            return response()->json([
                'success' => false,
                'message' => 'Table identifier is required',
            ], 400);
        }

        // Validate that order belongs to the table
        if (!$order->table || $order->table->unique_identifier !== $tableIdentifier) {
            return response()->json([
                'success' => false,
                'message' => 'Order does not belong to this table',
            ], 403);
        }

        // Calculate payment status
        $totalPaid = $order->payments()
            ->where('status', 'completed')
            ->sum('amount');
        $remainingAmount = $order->total_amount - $totalPaid;
        $isPaid = $remainingAmount <= 0;

        return response()->json([
            'success' => true,
            'data' => [
                'is_paid' => $isPaid,
                'total_amount' => $order->total_amount,
                'total_paid' => $totalPaid,
                'remaining_amount' => max(0, $remainingAmount),
                'payment_status' => $order->payment_status,
                'payments' => $order->payments()->where('status', 'completed')->get(),
            ],
        ]);
    }

    /**
     * Process payment for an order.
     */
    public function processPayment(Request $request, string $orderNumber)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,card,transfer,e_wallet,other',
            'amount' => 'required|numeric|min:0',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Get table identifier
        $tableIdentifier = $request->query('table');
        
        $order = Order::with(['store', 'brand', 'table', 'payments'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        // Validate table identifier
        if (!$tableIdentifier && $order->table) {
            $tableIdentifier = $order->table->unique_identifier;
        }

        if (!$tableIdentifier) {
            return response()->json([
                'success' => false,
                'message' => 'Table identifier is required',
            ], 400);
        }

        // Validate that order belongs to the table
        if (!$order->table || $order->table->unique_identifier !== $tableIdentifier) {
            return response()->json([
                'success' => false,
                'message' => 'Order does not belong to this table',
            ], 403);
        }

        // Check if order is cancelled or closed
        if ($order->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot process payment for cancelled order',
            ], 422);
        }

        if ($order->isClosed()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot process payment for closed order',
            ], 422);
        }

        // Calculate total paid amount
        $totalPaid = $order->payments()
            ->where('status', 'completed')
            ->sum('amount');

        $remainingAmount = $order->total_amount - $totalPaid;
        $paymentAmount = $request->amount;

        // Validate payment amount
        if ($paymentAmount > $remainingAmount) {
            return response()->json([
                'success' => false,
                'message' => 'Payment amount exceeds remaining balance',
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Create payment record
            $payment = Payment::create([
                'mdx_store_id' => $order->mdx_store_id,
                'mdx_brand_id' => $order->mdx_brand_id,
                'trx_order_id' => $order->id,
                'payment_number' => Payment::generatePaymentNumber(),
                'amount' => $paymentAmount,
                'payment_method' => $request->payment_method,
                'customer_name' => $order->customer_name,
                'customer_phone' => $order->customer_phone,
                'notes' => $request->notes,
                'reference_number' => $request->reference_number,
                'status' => 'completed',
                'paid_at' => now(),
            ]);

            // Calculate new total paid amount
            $newTotalPaid = $order->payments()
                ->where('status', 'completed')
                ->sum('amount');

            // Update order payment status
            if ($newTotalPaid >= $order->total_amount) {
                $order->payment_status = 'paid';
            } elseif ($newTotalPaid > 0) {
                $order->payment_status = 'partial';
            }

            // Update order payment method if not set
            if (!$order->payment_method) {
                $order->payment_method = $request->payment_method;
            }

            $order->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil diproses',
                'data' => [
                    'payment' => $payment,
                    'order' => $order->fresh(['payments']),
                    'remaining_amount' => $order->total_amount - $newTotalPaid,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to process payment: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Close all orders for a table.
     */
    public function closeTable(Request $request)
    {
        $tableIdentifier = $request->query('table');
        
        if (!$tableIdentifier) {
            return response()->json([
                'success' => false,
                'message' => 'Table identifier is required',
            ], 400);
        }

        $table = Table::findByUniqueIdentifier($tableIdentifier);
        
        if (!$table) {
            return response()->json([
                'success' => false,
                'message' => 'Table not found',
            ], 404);
        }

        // Check if table can be closed
        if (!Order::canCloseTable($table->id)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menutup pesanan. Pastikan semua pesanan sudah dibayar dan selesai.',
            ], 422);
        }

        // Close all orders for this table
        $closedCount = Order::closeTableOrders($table->id);

        return response()->json([
            'success' => true,
            'message' => "Berhasil menutup {$closedCount} pesanan",
            'data' => [
                'closed_count' => $closedCount,
            ],
        ]);
    }
}

