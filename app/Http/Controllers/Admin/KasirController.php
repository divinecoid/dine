<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\v1\Store;
use App\Models\v1\Order;
use App\Models\v1\OrderDetail;
use App\Models\v1\Payment;
use App\Models\v1\Menu;
use App\Models\v1\Table;
use App\Models\v1\Category;
use App\Services\XenditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KasirController extends Controller
{
    /**
     * Display the kasir page.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get selected store from request or default
        $selectedStoreId = $request->get('store_id');
        
        // Get stores based on user role
        $stores = collect();
        $lockedStoreId = null;
        
        if ($user->isKasir()) {
            // Kasir: locked to their work store
            if ($user->workStore) {
                $lockedStoreId = $user->workStore->id;
                $stores = collect([$user->workStore]);
                $selectedStoreId = $lockedStoreId;
            } else {
                // Kasir without assigned store
                return view('admin.kasir.index', [
                    'stores' => collect(),
                    'selectedStore' => null,
                    'lockedStoreId' => null,
                    'user' => $user,
                ])->with('error', 'Anda belum ditugaskan ke store manapun. Silakan hubungi administrator.');
            }
        } elseif ($user->isStoreManager()) {
            // Store Manager: locked to their managed store
            if ($user->store) {
                $lockedStoreId = $user->store->id;
                $stores = collect([$user->store]);
                $selectedStoreId = $lockedStoreId;
            } else {
                // Store Manager without assigned store
                return view('admin.kasir.index', [
                    'stores' => collect(),
                    'selectedStore' => null,
                    'lockedStoreId' => null,
                    'user' => $user,
                ])->with('error', 'Anda belum ditugaskan ke store manapun. Silakan hubungi administrator.');
            }
        } elseif ($user->isBrandOwner()) {
            // Brand Owner: can select any store from their brands
            $brandIds = $user->getAccessibleBrandIds();
            $stores = Store::whereIn('mdx_brand_id', $brandIds)
                ->active()
                ->orderBy('name')
                ->get();
            
            // Set default selected store if not provided
            if (!$selectedStoreId && $stores->isNotEmpty()) {
                $selectedStoreId = $stores->first()->id;
            }
        }
        
        // Get selected store
        $selectedStore = null;
        if ($selectedStoreId) {
            $selectedStore = Store::find($selectedStoreId);
            
            // Verify access
            if ($selectedStore) {
                if ($user->isKasir() && $selectedStore->id !== $user->mdx_store_id) {
                    abort(403, 'Unauthorized access to this store.');
                }
                if ($user->isStoreManager() && $selectedStore->id !== $user->store?->id) {
                    abort(403, 'Unauthorized access to this store.');
                }
                if ($user->isBrandOwner() && !in_array($selectedStore->mdx_brand_id, $user->getAccessibleBrandIds())) {
                    abort(403, 'Unauthorized access to this store.');
                }
            }
        }

        // Get orders for selected store
        $orders = collect();
        $pendingOrders = collect();
        $unpaidOrders = collect();
        $menus = collect();
        $tables = collect();
        $categories = collect();

        if ($selectedStore) {
            // Get orders that are not cancelled and not closed
            $orders = Order::where('mdx_store_id', $selectedStore->id)
                ->where('status', '!=', 'cancelled')
                ->whereNull('closed_at')
                ->with(['orderDetails.menu', 'table', 'payments'])
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get();

            // Get pending orders (not completed)
            $pendingOrders = Order::where('mdx_store_id', $selectedStore->id)
                ->where('status', '!=', 'completed')
                ->where('status', '!=', 'cancelled')
                ->whereNull('closed_at')
                ->with(['orderDetails.menu', 'table'])
                ->orderBy('created_at', 'desc')
                ->get();

            // Get unpaid orders (pending or completed but not paid)
            $unpaidOrders = Order::where('mdx_store_id', $selectedStore->id)
                ->where(function($query) {
                    $query->where('status', 'pending')
                        ->orWhere(function($q) {
                            $q->where('status', 'completed')
                              ->where('payment_status', '!=', 'paid')
                              ->where('payment_status', '!=', 'cancelled');
                        });
                })
                ->whereNull('closed_at')
                ->with(['orderDetails.menu', 'table', 'payments'])
                ->orderBy('created_at', 'desc')
                ->get();

            // Get available menus for this store with categories
            $menus = $selectedStore->availableMenus()
                ->with('categories')
                ->orderBy('name')
                ->get();

            // Get categories for this brand
            $categories = Category::where('mdx_brand_id', $selectedStore->mdx_brand_id)
                ->active()
                ->ordered()
                ->get();

            // Get tables for this store
            $tables = Table::where('mdx_store_id', $selectedStore->id)
                ->orderBy('table_number')
                ->get();
        }
        
        return view('admin.kasir.index', [
            'stores' => $stores,
            'selectedStore' => $selectedStore,
            'lockedStoreId' => $lockedStoreId,
            'user' => $user,
            'orders' => $orders,
            'pendingOrders' => $pendingOrders,
            'unpaidOrders' => $unpaidOrders,
            'menus' => $menus,
            'tables' => $tables,
            'categories' => $categories,
        ]);
    }

    /**
     * Create a new order.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $selectedStoreId = $request->input('mdx_store_id');

        // Verify store access
        $selectedStore = Store::find($selectedStoreId);
        if (!$selectedStore) {
            return back()->withErrors(['error' => 'Store tidak ditemukan.']);
        }

        if ($user->isKasir() && $selectedStore->id !== $user->mdx_store_id) {
            abort(403, 'Unauthorized access to this store.');
        }
        if ($user->isStoreManager() && $selectedStore->id !== $user->store?->id) {
            abort(403, 'Unauthorized access to this store.');
        }
        if ($user->isBrandOwner() && !in_array($selectedStore->mdx_brand_id, $user->getAccessibleBrandIds())) {
            abort(403, 'Unauthorized access to this store.');
        }

        $request->validate([
            'mdx_store_id' => 'required|exists:mdx_stores,id',
            'order_type' => 'required|in:dine_in,takeaway,delivery',
            'mdx_table_id' => 'nullable|required_if:order_type,dine_in|exists:mdx_tables,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'delivery_address' => 'nullable|required_if:order_type,delivery|string',
            'items' => 'required|array|min:1',
            'items.*.mdx_menu_id' => 'required|exists:mdx_menus,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'nullable|numeric|min:0',
            'items.*.discount_amount' => 'nullable|numeric|min:0',
            'items.*.notes' => 'nullable|string',
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'service_charge' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Create order
            $order = Order::create([
                'mdx_store_id' => $selectedStoreId,
                'mdx_brand_id' => $selectedStore->mdx_brand_id,
                'mdx_table_id' => $request->input('mdx_table_id'),
                'order_type' => $request->input('order_type'),
                'customer_name' => $request->input('customer_name'),
                'customer_phone' => $request->input('customer_phone'),
                'customer_email' => $request->input('customer_email'),
                'delivery_address' => $request->input('delivery_address'),
                'status' => 'pending',
                'payment_status' => 'pending',
                'discount_amount' => $request->input('discount_amount', 0),
                'tax_amount' => $request->input('tax_amount', 0),
                'service_charge' => $request->input('service_charge', 0),
                'notes' => $request->input('notes'),
            ]);

            // Create order details
            foreach ($request->input('items') as $item) {
                $menu = Menu::find($item['mdx_menu_id']);
                if (!$menu) {
                    continue;
                }

                $unitPrice = $item['unit_price'] ?? $menu->price;
                $quantity = $item['quantity'];
                $discountAmount = $item['discount_amount'] ?? 0;
                $subtotal = ($unitPrice * $quantity) - $discountAmount;

                OrderDetail::create([
                    'trx_order_id' => $order->id,
                    'mdx_menu_id' => $menu->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'discount_amount' => $discountAmount,
                    'subtotal' => $subtotal,
                    'menu_name' => $menu->name,
                    'menu_description' => $menu->description,
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            // Calculate totals
            $order->calculateTotals();

            DB::commit();

            return redirect()->route('admin.kasir.index', ['store_id' => $selectedStoreId])
                ->with('success', 'Order berhasil dibuat dengan nomor: ' . $order->order_number);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal membuat order: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Generate QRIS for an order (AJAX).
     */
    public function generateQRIS(Request $request, $orderId)
    {
        $user = Auth::user();
        $order = Order::with(['store', 'payments'])->findOrFail($orderId);

        // Verify store access
        if ($user->isKasir() && $order->mdx_store_id !== $user->mdx_store_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access'], 403);
        }
        if ($user->isStoreManager() && $order->mdx_store_id !== $user->store?->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access'], 403);
        }
        if ($user->isBrandOwner() && !in_array($order->mdx_brand_id, $user->getAccessibleBrandIds())) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access'], 403);
        }

        $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);

        // Calculate total paid amount
        $totalPaid = $order->payments()
            ->where('status', 'completed')
            ->sum('amount');

        $remainingAmount = $order->total_amount - $totalPaid;
        $paymentAmount = $request->input('amount');

        // Validate payment amount
        if ($paymentAmount > $remainingAmount) {
            return response()->json(['success' => false, 'message' => 'Jumlah pembayaran melebihi sisa tagihan.'], 400);
        }

        try {
            // Check if Xendit API key is configured
            $apiKey = config('services.xendit.api_key');
            if (!$apiKey) {
                Log::error('Xendit API key not configured');
                return response()->json(['success' => false, 'message' => 'Xendit API key belum dikonfigurasi. Silakan hubungi administrator.'], 500);
            }

            $xenditService = new XenditService();
            
            $xenditData = [
                'reference_id' => $order->order_number . '-' . time(),
                'amount' => (int) round($paymentAmount),
                'customer_name' => $order->customer_name,
                'customer_email' => $order->customer_phone ?? null,
                'description' => "Payment for Order #{$order->order_number}",
                'metadata' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'store_id' => $order->mdx_store_id,
                ],
            ];

            $xenditResponse = $xenditService->createQRISPayment($xenditData);

            if (!$xenditResponse) {
                Log::error('Xendit QRIS generation failed', [
                    'order_id' => $orderId,
                    'xendit_data' => $xenditData,
                ]);
                return response()->json(['success' => false, 'message' => 'Gagal membuat payment request ke Xendit. Silakan coba lagi atau periksa log untuk detail error.'], 500);
            }

            // Xendit returns payment_request_id, not id
            $xenditPaymentId = $xenditResponse['payment_request_id'] ?? $xenditResponse['id'] ?? null;
            
            // Get QR code from actions array
            // Xendit returns actions as array: [{"type":"PRESENT_TO_CUSTOMER","descriptor":"QR_STRING","value":"..."}]
            $xenditQrCode = null;
            if (isset($xenditResponse['actions']) && is_array($xenditResponse['actions'])) {
                foreach ($xenditResponse['actions'] as $action) {
                    if (isset($action['descriptor']) && $action['descriptor'] === 'QR_STRING' && isset($action['value'])) {
                        $xenditQrCode = $action['value'];
                        break;
                    }
                    // Fallback: if there's a value field, use it
                    if (isset($action['value']) && !$xenditQrCode) {
                        $xenditQrCode = $action['value'];
                    }
                }
            }
            
            // Legacy support: check if actions is an object (old format)
            if (!$xenditQrCode && isset($xenditResponse['actions']) && is_array($xenditResponse['actions'])) {
                $xenditQrCode = $xenditResponse['actions']['qr_string'] ?? 
                               $xenditResponse['actions']['qr_code'] ?? 
                               null;
            }

            if (!$xenditQrCode) {
                Log::error('QR code not found in Xendit response', [
                    'order_id' => $orderId,
                    'xendit_response' => $xenditResponse,
                    'response_structure' => json_encode($xenditResponse, JSON_PRETTY_PRINT),
                ]);
                return response()->json([
                    'success' => false, 
                    'message' => 'QR code tidak ditemukan dalam response Xendit. Silakan cek log untuk detail.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'payment_id' => $xenditPaymentId,
                'qr_code' => $xenditQrCode,
            ]);
        } catch (\Exception $e) {
            Log::error('Error generating QRIS', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['success' => false, 'message' => 'Gagal generate QRIS: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Process payment for an order.
     */
    public function processPayment(Request $request, $orderId)
    {
        $user = Auth::user();
        $order = Order::with(['store', 'payments'])->findOrFail($orderId);

        // Verify store access
        if ($user->isKasir() && $order->mdx_store_id !== $user->mdx_store_id) {
            abort(403, 'Unauthorized access to this order.');
        }
        if ($user->isStoreManager() && $order->mdx_store_id !== $user->store?->id) {
            abort(403, 'Unauthorized access to this order.');
        }
        if ($user->isBrandOwner() && !in_array($order->mdx_brand_id, $user->getAccessibleBrandIds())) {
            abort(403, 'Unauthorized access to this order.');
        }

        $request->validate([
            'payment_method' => 'required|in:cash,card,transfer,e_wallet,qris,other',
            'amount' => 'required|numeric|min:0',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Check if order is cancelled or closed
        if ($order->status === 'cancelled') {
            return back()->withErrors(['error' => 'Tidak dapat memproses pembayaran untuk order yang dibatalkan.']);
        }

        if ($order->isClosed()) {
            return back()->withErrors(['error' => 'Tidak dapat memproses pembayaran untuk order yang sudah ditutup.']);
        }

        // Calculate total paid amount
        $totalPaid = $order->payments()
            ->where('status', 'completed')
            ->sum('amount');

        $remainingAmount = $order->total_amount - $totalPaid;
        $paymentAmount = $request->input('amount');
        $paymentMethod = $request->input('payment_method');

        // Validate payment amount - allow overpayment for cash
        if ($paymentMethod !== 'cash' && $paymentAmount > $remainingAmount) {
            return back()->withErrors(['error' => 'Jumlah pembayaran melebihi sisa tagihan.']);
        }

        DB::beginTransaction();
        try {
            $xenditPaymentId = $request->input('xendit_payment_id');
            $xenditQrCode = $request->input('xendit_qr_code');
            $paymentStatus = 'completed';

            // If payment method is QRIS, use the generated QRIS data
            if ($paymentMethod === 'qris') {
                if (!$xenditPaymentId || !$xenditQrCode) {
                    DB::rollBack();
                    return back()->withErrors(['error' => 'QRIS belum di-generate. Silakan klik tombol Generate QRIS terlebih dahulu.']);
                }
                $paymentStatus = 'pending'; // QRIS payment is pending until confirmed
            }

            // Create payment record
            $payment = Payment::create([
                'mdx_store_id' => $order->mdx_store_id,
                'mdx_brand_id' => $order->mdx_brand_id,
                'trx_order_id' => $order->id,
                'payment_number' => Payment::generatePaymentNumber(),
                'amount' => $paymentAmount,
                'payment_method' => $paymentMethod,
                'customer_name' => $order->customer_name,
                'customer_phone' => $order->customer_phone,
                'notes' => $request->input('notes'),
                'reference_number' => $request->input('reference_number'),
                'xendit_payment_id' => $xenditPaymentId,
                'xendit_qr_code' => $xenditQrCode,
                'status' => $paymentStatus,
                'paid_at' => $paymentStatus === 'completed' ? now() : null,
            ]);

            // Calculate new total paid amount (only completed payments)
            $newTotalPaid = $order->payments()
                ->where('status', 'completed')
                ->sum('amount');

            // Update order payment status
            if ($newTotalPaid >= $order->total_amount) {
                $order->payment_status = 'paid';
            } elseif ($newTotalPaid > 0) {
                $order->payment_status = 'partial';
            } elseif ($paymentMethod === 'qris' && $paymentStatus === 'pending') {
                // For QRIS pending payments, keep payment_status as is
            }

            // Update order payment method if not set
            if (!$order->payment_method) {
                $order->payment_method = $request->input('payment_method');
            }

            $order->save();

            DB::commit();

            if ($paymentMethod === 'qris' && $paymentStatus === 'pending') {
                return redirect()->route('admin.kasir.index', ['store_id' => $order->mdx_store_id])
                    ->with('success', 'QRIS payment request berhasil dibuat. Silakan scan QR code untuk menyelesaikan pembayaran.')
                    ->with('qris_payment_id', $payment->id);
            }

            return redirect()->route('admin.kasir.index', ['store_id' => $order->mdx_store_id])
                ->with('success', 'Pembayaran berhasil diproses. Sisa tagihan: Rp ' . number_format($order->total_amount - $newTotalPaid, 0, ',', '.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal memproses pembayaran: ' . $e->getMessage()]);
        }
    }

    /**
     * Check payment status (for QRIS).
     */
    public function checkPaymentStatus($paymentId)
    {
        $payment = Payment::findOrFail($paymentId);
        
        // If payment has Xendit payment ID, check status from Xendit
        if ($payment->xendit_payment_id) {
            $xenditService = new XenditService();
            $xenditResponse = $xenditService->getPaymentRequest($payment->xendit_payment_id);
            
            if ($xenditResponse) {
                $xenditStatus = $xenditResponse['status'] ?? null;
                
                // Update payment status based on Xendit status
                if ($xenditStatus === 'SUCCEEDED' && $payment->status !== 'completed') {
                    DB::beginTransaction();
                    try {
                        $payment->status = 'completed';
                        $payment->paid_at = now();
                        $payment->save();
                        
                        // Update order payment status
                        $order = $payment->order;
                        $totalPaid = $order->payments()
                            ->where('status', 'completed')
                            ->sum('amount');
                        
                        if ($totalPaid >= $order->total_amount) {
                            $order->payment_status = 'paid';
                        } elseif ($totalPaid > 0) {
                            $order->payment_status = 'partial';
                        }
                        $order->save();
                        
                        DB::commit();
                        
                        return response()->json([
                            'status' => 'completed',
                            'message' => 'Pembayaran berhasil',
                        ]);
                    } catch (\Exception $e) {
                        DB::rollBack();
                        return response()->json([
                            'status' => $payment->status,
                            'message' => 'Error updating payment: ' . $e->getMessage(),
                        ], 500);
                    }
                }
                
                return response()->json([
                    'status' => $payment->status,
                    'xendit_status' => $xenditStatus,
                ]);
            }
        }
        
        return response()->json([
            'status' => $payment->status,
        ]);
    }

    /**
     * Complete an order.
     */
    public function completeOrder($orderId)
    {
        $user = Auth::user();
        $order = Order::findOrFail($orderId);

        // Verify store access
        if ($user->isKasir() && $order->mdx_store_id !== $user->mdx_store_id) {
            abort(403, 'Unauthorized access to this order.');
        }
        if ($user->isStoreManager() && $order->mdx_store_id !== $user->store?->id) {
            abort(403, 'Unauthorized access to this order.');
        }
        if ($user->isBrandOwner() && !in_array($order->mdx_brand_id, $user->getAccessibleBrandIds())) {
            abort(403, 'Unauthorized access to this order.');
        }

        // Check if order can be completed
        if ($order->status === 'cancelled') {
            return back()->withErrors(['error' => 'Tidak dapat menyelesaikan order yang dibatalkan.']);
        }

        if ($order->status === 'completed') {
            return back()->withErrors(['error' => 'Order sudah selesai.']);
        }

        DB::beginTransaction();
        try {
            $order->status = 'completed';
            $order->completed_at = now();
            $order->save();

            DB::commit();

            return redirect()->route('admin.kasir.index', ['store_id' => $order->mdx_store_id])
                ->with('success', 'Order berhasil diselesaikan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menyelesaikan order: ' . $e->getMessage()]);
        }
    }
}

