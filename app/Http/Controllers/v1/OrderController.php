<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\v1\Menu;
use App\Models\v1\Order;
use App\Models\v1\OrderDetail;
use App\Models\v1\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * Check if there's an incomplete order for a table.
     */
    public function checkIncompleteOrder(Request $request): JsonResponse
    {
        $request->validate([
            'mdx_table_id' => ['required', 'exists:mdx_tables,id'],
        ]);

        $order = Order::where('mdx_table_id', $request->mdx_table_id)
            ->where('status', '!=', 'completed')
            ->where('status', '!=', 'cancelled')
            ->notClosed()
            ->orderBy('created_at', 'desc')
            ->first();

        if ($order) {
            return response()->json([
                'success' => true,
                'has_incomplete_order' => true,
                'data' => [
                    'order' => $order->load(['store', 'brand', 'table', 'orderDetails.menu']),
                    'customer_name' => $order->customer_name,
                    'customer_phone' => $order->customer_phone,
                    'customer_email' => $order->customer_email,
                ],
            ]);
        }

        return response()->json([
            'success' => true,
            'has_incomplete_order' => false,
            'data' => null,
        ]);
    }

    /**
     * Display a listing of the orders.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Order::query();

        // Filter by store_id
        if ($request->has('store_id')) {
            $query->forStore($request->store_id);
        }

        // Filter by brand_id
        if ($request->has('brand_id')) {
            $query->forBrand($request->brand_id);
        }

        // Filter by table_id
        if ($request->has('table_id')) {
            $query->where('mdx_table_id', $request->table_id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->withStatus($request->status);
        }

        // Filter by payment_status
        if ($request->has('payment_status')) {
            $query->withPaymentStatus($request->payment_status);
        }

        // Filter by order_type
        if ($request->has('order_type')) {
            $query->withOrderType($request->order_type);
        }

        // Filter by date range
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->dateRange($request->start_date, $request->end_date);
        }

        // Search by order number or customer name
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', '%' . $search . '%')
                  ->orWhere('customer_name', 'like', '%' . $search . '%')
                  ->orWhere('customer_phone', 'like', '%' . $search . '%');
            });
        }

        // Load relationships
        $with = $request->get('with', '');
        if ($with) {
            $relationships = array_filter(explode(',', $with));
            $validRelationships = ['store', 'brand', 'table', 'orderDetails', 'orderDetails.menu'];
            $relationships = array_intersect($relationships, $validRelationships);
            if (!empty($relationships)) {
                $query->with($relationships);
            }
        } else {
            // Default load relationships
            $query->with(['store', 'brand', 'table', 'orderDetails.menu']);
        }

        // Order by
        $orderBy = $request->get('order_by', 'ordered_at');
        $orderDir = $request->get('order_dir', 'desc');
        $query->orderBy($orderBy, $orderDir);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $orders = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $orders->items(),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ],
        ]);
    }

    /**
     * Store a newly created order.
     */
    public function store(Request $request): JsonResponse
    {
        // Check if there's an incomplete order for this table first
        $hasIncompleteOrder = false;
        if ($request->has('mdx_table_id') && !empty($request->mdx_table_id)) {
            $existingOrder = Order::where('mdx_table_id', $request->mdx_table_id)
                ->where('status', '!=', 'completed')
                ->where('status', '!=', 'cancelled')
                ->notClosed()
                ->first();
            
            if ($existingOrder) {
                $hasIncompleteOrder = true;
            }
        }

        // Customer name is required only if no incomplete order exists
        $validator = Validator::make($request->all(), [
            'mdx_store_id' => ['required', 'exists:mdx_stores,id'],
            'mdx_brand_id' => ['nullable', 'exists:mdx_brands,id'],
            'mdx_table_id' => ['nullable', 'exists:mdx_tables,id'],
            'order_type' => ['nullable', 'in:dine_in,takeaway,delivery'],
            'customer_name' => [$hasIncompleteOrder ? 'nullable' : 'required', 'string', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:255'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'delivery_address' => ['nullable', 'required_if:order_type,delivery', 'string'],
            'delivery_latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'delivery_longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'service_charge' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'admin_notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.mdx_menu_id' => ['required', 'exists:mdx_menus,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['nullable', 'numeric', 'min:0'],
            'items.*.discount_amount' => ['nullable', 'numeric', 'min:0'],
            'items.*.notes' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $data = $validator->validated();
            $items = $data['items'];
            unset($data['items']);

            // Check if there's an incomplete order for this table
            $existingOrder = null;
            if (!empty($data['mdx_table_id'])) {
                $existingOrder = Order::where('mdx_table_id', $data['mdx_table_id'])
                    ->where('status', '!=', 'completed')
                    ->where('status', '!=', 'cancelled')
                    ->notClosed()
                    ->orderBy('created_at', 'desc')
                    ->first();
            }

            if ($existingOrder) {
                // Add items to existing order
                $order = $existingOrder;
                
                // Update customer info if provided (optional update)
                if (isset($data['customer_name']) && !empty($data['customer_name'])) {
                    $order->customer_name = $data['customer_name'];
                }
                if (isset($data['customer_phone']) && !empty($data['customer_phone'])) {
                    $order->customer_phone = $data['customer_phone'];
                }
                if (isset($data['customer_email']) && !empty($data['customer_email'])) {
                    $order->customer_email = $data['customer_email'];
                }
                
                // Update notes if provided (append or replace)
                if (isset($data['notes']) && !empty($data['notes'])) {
                    $existingNotes = $order->notes;
                    $order->notes = $existingNotes 
                        ? $existingNotes . "\n" . $data['notes'] 
                        : $data['notes'];
                }
                
                $order->save();
            } else {
                // Create new order
                $order = Order::create($data);
            }

            // Create order details
            $subtotal = 0;
            foreach ($items as $item) {
                $menu = Menu::find($item['mdx_menu_id']);

                // Use provided unit_price or menu price
                $unitPrice = $item['unit_price'] ?? $menu->price;
                $quantity = $item['quantity'];
                $itemDiscount = $item['discount_amount'] ?? 0;
                $itemSubtotal = ($unitPrice * $quantity) - $itemDiscount;

                OrderDetail::create([
                    'trx_order_id' => $order->id,
                    'mdx_menu_id' => $menu->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'discount_amount' => $itemDiscount,
                    'subtotal' => $itemSubtotal,
                    'menu_name' => $menu->name,
                    'menu_description' => $menu->description,
                    'notes' => $item['notes'] ?? null,
                ]);

                $subtotal += $itemSubtotal;
            }

            // Recalculate totals (add new items to existing subtotal if order exists)
            if ($existingOrder) {
                $order->subtotal += $subtotal;
            } else {
                $order->subtotal = $subtotal;
            }
            
            $order->total_amount = $order->subtotal 
                - ($order->discount_amount ?? 0) 
                + ($order->tax_amount ?? 0) 
                + ($order->service_charge ?? 0);
            $order->save();

            DB::commit();

            $order->load(['store', 'brand', 'table', 'orderDetails.menu']);

            return response()->json([
                'success' => true,
                'message' => $existingOrder ? 'Items added to existing order successfully' : 'Order created successfully',
                'data' => $order,
            ], $existingOrder ? 200 : 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to create order',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order): JsonResponse
    {
        $order->load(['store', 'brand', 'table', 'orderDetails.menu']);

        return response()->json([
            'success' => true,
            'data' => $order,
        ]);
    }

    /**
     * Update the specified order.
     */
    public function update(Request $request, Order $order): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => ['sometimes', 'in:pending,confirmed,preparing,ready,completed,cancelled'],
            'payment_status' => ['sometimes', 'in:pending,paid,partial,refunded'],
            'payment_method' => ['nullable', 'in:cash,card,transfer,e_wallet,other'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'service_charge' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'admin_notes' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Check if order can be cancelled
        if (isset($request->status) && $request->status === 'cancelled' && !$order->canBeCancelled()) {
            return response()->json([
                'success' => false,
                'message' => 'Order cannot be cancelled at this stage',
            ], 422);
        }

        $order->update($validator->validated());

        // Recalculate totals if financial fields changed
        if ($request->hasAny(['discount_amount', 'tax_amount', 'service_charge'])) {
            $order->calculateTotals();
        }

        $order->load(['store', 'brand', 'table', 'orderDetails.menu']);

        return response()->json([
            'success' => true,
            'message' => 'Order updated successfully',
            'data' => $order->fresh(),
        ]);
    }

    /**
     * Remove the specified order.
     */
    public function destroy(Order $order): JsonResponse
    {
        if (!$order->canBeCancelled()) {
            return response()->json([
                'success' => false,
                'message' => 'Order cannot be deleted at this stage',
            ], 422);
        }

        $order->delete();

        return response()->json([
            'success' => true,
            'message' => 'Order deleted successfully',
        ]);
    }

    /**
     * Restore the specified order.
     */
    public function restore(int $id): JsonResponse
    {
        $order = Order::withTrashed()->findOrFail($id);
        $order->restore();

        return response()->json([
            'success' => true,
            'message' => 'Order restored successfully',
            'data' => $order,
        ]);
    }

    /**
     * Add item to order.
     */
    public function addItem(Request $request, Order $order): JsonResponse
    {
        if ($order->isCompleted() || $order->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot add item to completed or cancelled order',
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'mdx_menu_id' => ['required', 'exists:mdx_menus,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit_price' => ['nullable', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $data = $validator->validated();
            $menu = Menu::find($data['mdx_menu_id']);

            $unitPrice = $data['unit_price'] ?? $menu->price;
            $quantity = $data['quantity'];
            $discountAmount = $data['discount_amount'] ?? 0;
            $subtotal = ($unitPrice * $quantity) - $discountAmount;

            $orderDetail = OrderDetail::create([
                'trx_order_id' => $order->id,
                'mdx_menu_id' => $menu->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'discount_amount' => $discountAmount,
                'subtotal' => $subtotal,
                'menu_name' => $menu->name,
                'menu_description' => $menu->description,
                'notes' => $data['notes'] ?? null,
            ]);

            $order->calculateTotals();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Item added successfully',
                'data' => $orderDetail->load('menu'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to add item',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update order detail item.
     */
    public function updateItem(Request $request, Order $order, OrderDetail $orderDetail): JsonResponse
    {
        if ($orderDetail->trx_order_id !== $order->id) {
            return response()->json([
                'success' => false,
                'message' => 'Order detail does not belong to this order',
            ], 422);
        }

        if ($order->isCompleted() || $order->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot update item in completed or cancelled order',
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'quantity' => ['sometimes', 'required', 'integer', 'min:1'],
            'unit_price' => ['nullable', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $orderDetail->update($validator->validated());
        $orderDetail->updateSubtotal();

        $order->calculateTotals();

        return response()->json([
            'success' => true,
            'message' => 'Item updated successfully',
            'data' => $orderDetail->fresh('menu'),
        ]);
    }

    /**
     * Remove item from order.
     */
    public function removeItem(Order $order, OrderDetail $orderDetail): JsonResponse
    {
        if ($orderDetail->trx_order_id !== $order->id) {
            return response()->json([
                'success' => false,
                'message' => 'Order detail does not belong to this order',
            ], 422);
        }

        if ($order->isCompleted() || $order->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot remove item from completed or cancelled order',
            ], 422);
        }

        $orderDetail->delete();
        $order->calculateTotals();

        return response()->json([
            'success' => true,
            'message' => 'Item removed successfully',
        ]);
    }

    /**
     * Cancel an order by order number.
     */
    public function cancel(string $orderNumber): JsonResponse
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        // Check if order can be cancelled
        if (!$order->canBeCancelled()) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak dapat dibatalkan pada tahap ini. Hanya pesanan dengan status pending, confirmed, atau preparing yang dapat dibatalkan.',
            ], 422);
        }

        // Update order status to cancelled
        $order->update([
            'status' => 'cancelled',
        ]);

        $order->load(['store', 'brand', 'table', 'orderDetails.menu']);

        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil dibatalkan',
            'data' => $order,
        ]);
    }
}

