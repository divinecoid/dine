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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

            // Get unpaid orders (completed but not paid)
            $unpaidOrders = Order::where('mdx_store_id', $selectedStore->id)
                ->where('status', 'completed')
                ->where('payment_status', '!=', 'paid')
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
            'payment_method' => 'required|in:cash,card,transfer,e_wallet,other',
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

        // Validate payment amount
        if ($paymentAmount > $remainingAmount) {
            return back()->withErrors(['error' => 'Jumlah pembayaran melebihi sisa tagihan.']);
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
                'payment_method' => $request->input('payment_method'),
                'customer_name' => $order->customer_name,
                'customer_phone' => $order->customer_phone,
                'notes' => $request->input('notes'),
                'reference_number' => $request->input('reference_number'),
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
                $order->payment_method = $request->input('payment_method');
            }

            $order->save();

            DB::commit();

            return redirect()->route('admin.kasir.index', ['store_id' => $order->mdx_store_id])
                ->with('success', 'Pembayaran berhasil diproses. Sisa tagihan: Rp ' . number_format($order->total_amount - $newTotalPaid, 0, ',', '.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal memproses pembayaran: ' . $e->getMessage()]);
        }
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

