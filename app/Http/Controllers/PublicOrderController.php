<?php

namespace App\Http\Controllers;

use App\Models\v1\Order;
use App\Models\v1\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PublicOrderController extends Controller
{
    /**
     * Display list of orders for a table.
     */
    public function index(Request $request): View
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
        
        $order = Order::with(['store', 'brand', 'table', 'orderDetails.menu'])
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

