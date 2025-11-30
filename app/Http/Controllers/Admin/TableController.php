<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\v1\Order;
use App\Models\v1\Store;
use App\Models\v1\Table;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function index(Request $request)
    {
        $query = Table::query();

        // Search with proper grouping
        $search = $request->input('search');
        if ($search && trim($search) !== '') {
            $searchTerm = trim($search);
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('table_number', 'like', '%' . $searchTerm . '%');
            });
        }

        // Active/Inactive status filter
        $status = $request->input('status');
        if ($status && $status !== '') {
            $query->where('is_active', $status === 'active');
        }

        // Store filter
        $storeId = $request->input('store_id');
        if ($storeId && $storeId !== '') {
            $query->where('mdx_store_id', (int)$storeId);
        }

        // Table status filter (available, occupied, etc.)
        $tableStatus = $request->input('table_status');
        if ($tableStatus && $tableStatus !== '') {
            $query->where('status', $tableStatus);
        }

        // Eager load store relationship after filters
        $query->with('store.brand');

        $orderBy = $request->get('order_by', 'created_at');
        $orderDir = $request->get('order_dir', 'desc');
        $query->orderBy($orderBy, $orderDir);

        $perPage = $request->get('per_page', 15);
        $tables = $query->paginate($perPage)->withQueryString();
        $stores = Store::active()->with('brand')->get();

        // Check which tables can be closed (all orders paid and completed)
        $tablesCanClose = [];
        foreach ($tables as $table) {
            $tablesCanClose[$table->id] = Order::canCloseTable($table->id);
        }

        return view('admin.tables.index', compact('tables', 'stores', 'tablesCanClose'));
    }

    public function create()
    {
        $stores = Store::active()->with('brand')->get();
        return view('admin.tables.create', compact('stores'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mdx_store_id' => ['required', 'exists:mdx_stores,id'],
            'table_number' => ['required', 'integer', 'min:1'],
            'name' => ['nullable', 'string', 'max:255'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'status' => ['required', 'in:available,occupied,reserved,maintenance'],
            'zone' => ['nullable', 'string', 'max:255'],
            'floor' => ['nullable', 'integer'],
            'notes' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        // Check unique table_number per store
        $exists = Table::where('mdx_store_id', $validated['mdx_store_id'])
            ->where('table_number', $validated['table_number'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['table_number' => 'Table number already exists for this store.'])->withInput();
        }

        if (empty($validated['name'])) {
            $validated['name'] = 'Meja ' . $validated['table_number'];
        }

        // Handle checkbox
        $validated['is_active'] = (bool)($request->input('is_active', 0));

        Table::create($validated);

        return redirect()->route('admin.tables.index')
            ->with('success', 'Table created successfully.');
    }

    public function edit(Table $table)
    {
        $stores = Store::active()->with('brand')->get();
        return view('admin.tables.edit', compact('table', 'stores'));
    }

    public function update(Request $request, Table $table)
    {
        $validated = $request->validate([
            'mdx_store_id' => ['required', 'exists:mdx_stores,id'],
            'table_number' => ['required', 'integer', 'min:1'],
            'name' => ['nullable', 'string', 'max:255'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'status' => ['required', 'in:available,occupied,reserved,maintenance'],
            'zone' => ['nullable', 'string', 'max:255'],
            'floor' => ['nullable', 'integer'],
            'notes' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        // Check unique table_number per store (exclude current table)
        $exists = Table::where('mdx_store_id', $validated['mdx_store_id'])
            ->where('table_number', $validated['table_number'])
            ->where('id', '!=', $table->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['table_number' => 'Table number already exists for this store.'])->withInput();
        }

        if (empty($validated['name'])) {
            $validated['name'] = 'Meja ' . $validated['table_number'];
        }

        // Handle checkbox
        $validated['is_active'] = (bool)($request->input('is_active', 0));

        $table->update($validated);

        return redirect()->route('admin.tables.index')
            ->with('success', 'Table updated successfully.');
    }

    public function destroy(Table $table)
    {
        $table->delete();
        return redirect()->route('admin.tables.index')
            ->with('success', 'Table deleted successfully.');
    }

    /**
     * Close all orders for a table.
     */
    public function closeOrders(Table $table)
    {
        // Check if table can be closed
        if (!Order::canCloseTable($table->id)) {
            return back()->withErrors([
                'error' => 'Tidak dapat menutup pesanan. Pastikan semua pesanan sudah dibayar dan selesai.',
            ]);
        }

        // Close all orders for this table
        $closedCount = Order::closeTableOrders($table->id);

        return redirect()->route('admin.tables.index')
            ->with('success', "Berhasil menutup {$closedCount} pesanan untuk meja ini.");
    }
}

