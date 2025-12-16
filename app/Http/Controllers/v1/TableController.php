<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\v1\Order;
use App\Models\v1\Table;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TableController extends Controller
{
    /**
     * Display a listing of the tables.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Table::accessibleBy($user);

        // Filter by store
        if ($request->has('store_id')) {
            $query->where('mdx_store_id', $request->store_id);
        }

        // Filter by is_active
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Filter by status (available, occupied, reserved, maintenance)
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by zone
        if ($request->has('zone')) {
            $query->where('zone', $request->zone);
        }

        // Filter by floor
        if ($request->has('floor')) {
            $query->where('floor', $request->floor);
        }

        // Search by name or table_number
        if ($request->has('search')) {
            $searchTerm = trim($request->search);
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('table_number', 'like', '%' . $searchTerm . '%');
            });
        }

        // Load relationships
        $with = $request->get('with', '');
        if ($with) {
            $query->with(explode(',', $with));
        } else {
            $query->with('store.brand');
        }

        // Order by
        $orderBy = $request->get('order_by', 'sort_order');
        $orderDir = $request->get('order_dir', 'asc');
        $query->orderBy($orderBy, $orderDir);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $tables = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $tables->items(),
            'meta' => [
                'current_page' => $tables->currentPage(),
                'last_page' => $tables->lastPage(),
                'per_page' => $tables->perPage(),
                'total' => $tables->total(),
            ],
        ]);
    }

    /**
     * Store a newly created table.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'mdx_store_id' => ['required', 'exists:mdx_stores,id'],
            'table_number' => ['required', 'integer', 'min:1'],
            'name' => ['nullable', 'string', 'max:255'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'status' => ['required', 'in:available,occupied,reserved,maintenance'],
            'zone' => ['nullable', 'string', 'max:255'],
            'floor' => ['nullable', 'integer'],
            'notes' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        // Check unique table_number per store
        $exists = Table::where('mdx_store_id', $data['mdx_store_id'])
            ->where('table_number', $data['table_number'])
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => [
                    'table_number' => ['Table number already exists for this store.'],
                ],
            ], 422);
        }

        // Auto-generate name if not provided
        if (empty($data['name'])) {
            $data['name'] = 'Meja ' . $data['table_number'];
        }

        $table = Table::create($data);
        $table->load('store.brand');

        return response()->json([
            'success' => true,
            'message' => 'Table created successfully',
            'data' => $table,
        ], 201);
    }

    /**
     * Display the specified table.
     */
    public function show(Table $table): JsonResponse
    {
        $table->load(['store.brand', 'orders']);

        return response()->json([
            'success' => true,
            'data' => $table,
        ]);
    }

    /**
     * Update the specified table.
     */
    public function update(Request $request, Table $table): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'mdx_store_id' => ['sometimes', 'required', 'exists:mdx_stores,id'],
            'table_number' => ['sometimes', 'required', 'integer', 'min:1'],
            'name' => ['nullable', 'string', 'max:255'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'status' => ['sometimes', 'required', 'in:available,occupied,reserved,maintenance'],
            'zone' => ['nullable', 'string', 'max:255'],
            'floor' => ['nullable', 'integer'],
            'notes' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        // Check unique table_number per store (exclude current table)
        if (isset($data['mdx_store_id']) && isset($data['table_number'])) {
            $exists = Table::where('mdx_store_id', $data['mdx_store_id'])
                ->where('table_number', $data['table_number'])
                ->where('id', '!=', $table->id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => [
                        'table_number' => ['Table number already exists for this store.'],
                    ],
                ], 422);
            }
        }

        // Auto-generate name if not provided and table_number is being updated
        if (isset($data['table_number']) && empty($data['name'])) {
            $data['name'] = 'Meja ' . $data['table_number'];
        }

        $table->update($data);
        $table->load('store.brand');

        return response()->json([
            'success' => true,
            'message' => 'Table updated successfully',
            'data' => $table->fresh(),
        ]);
    }

    /**
     * Remove the specified table.
     */
    public function destroy(Table $table): JsonResponse
    {
        $table->delete();

        return response()->json([
            'success' => true,
            'message' => 'Table deleted successfully',
        ]);
    }

    /**
     * Restore the specified table.
     */
    public function restore(int $id): JsonResponse
    {
        $table = Table::withTrashed()->findOrFail($id);
        $table->restore();

        return response()->json([
            'success' => true,
            'message' => 'Table restored successfully',
            'data' => $table,
        ]);
    }

    /**
     * Check if table can be closed (all orders paid and completed).
     */
    public function canClose(Table $table): JsonResponse
    {
        $canClose = Order::canCloseTable($table->id);

        return response()->json([
            'success' => true,
            'data' => [
                'can_close' => $canClose,
                'table_id' => $table->id,
            ],
        ]);
    }

    /**
     * Close all orders for a table.
     */
    public function closeOrders(Table $table): JsonResponse
    {
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
            'message' => "Berhasil menutup {$closedCount} pesanan untuk meja ini.",
            'data' => [
                'closed_count' => $closedCount,
                'table_id' => $table->id,
            ],
        ]);
    }

    /**
     * Get orders for a table.
     */
    public function orders(Table $table, Request $request): JsonResponse
    {
        $query = $table->orders();

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter closed orders
        if ($request->has('closed')) {
            if ($request->boolean('closed')) {
                $query->whereNotNull('closed_at');
            } else {
                $query->whereNull('closed_at');
            }
        }

        // Order by
        $orderBy = $request->get('order_by', 'ordered_at');
        $orderDir = $request->get('order_dir', 'desc');
        $query->orderBy($orderBy, $orderDir);

        // Load relationships
        $query->with(['orderDetails.menu', 'payments']);

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
}

