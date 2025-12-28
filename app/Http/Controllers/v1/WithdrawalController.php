<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\v1\Withdrawal;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    /**
     * Display a listing of withdrawals.
     */
    public function index()
    {
        $user = auth()->user();

        // Brand owner/Admin sees all requests
        if ($user->isBrandOwner() || $user->hasRole('admin')) {
            $withdrawals = Withdrawal::with(['user', 'store', 'bankAccount'])->latest()->get();
        } else {
            $withdrawals = Withdrawal::with(['store', 'bankAccount'])
                ->where('user_id', $user->id)
                ->latest()
                ->get();
        }

        return response()->json(['data' => $withdrawals]);
    }

    /**
     * Store a newly created withdrawal request.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'mdx_store_id' => 'required|exists:mdx_stores,id',
            'mdx_bank_account_id' => 'required|exists:mdx_bank_accounts,id',
            'amount' => 'required|numeric|min:10000',
        ]);

        // Validate that store belongs to user (if store manager)
        $user = auth()->user();
        if (!$user->isBrandOwner() && !$user->hasRole('admin')) {
            if ($user->store && $user->store->id != $validated['mdx_store_id']) {
                return response()->json(['message' => 'Unauthorized store access'], 403);
            }
        }

        $withdrawal = Withdrawal::create([
            'user_id' => $user->id,
            'mdx_store_id' => $validated['mdx_store_id'],
            'mdx_bank_account_id' => $validated['mdx_bank_account_id'],
            'amount' => $validated['amount'],
            'status' => 'pending',
        ]);

        return response()->json(['data' => $withdrawal, 'message' => 'Withdrawal request submitted successfully.'], 201);
    }

    /**
     * Display the specified withdrawal.
     */
    public function show($id)
    {
        $user = auth()->user();
        $withdrawal = Withdrawal::with(['user', 'store', 'bankAccount'])->findOrFail($id);

        if (!$user->isBrandOwner() && !$user->hasRole('admin') && $withdrawal->user_id !== $user->id) {
            abort(403);
        }

        return response()->json(['data' => $withdrawal]);
    }

    /**
     * Update status (Admin only).
     */
    public function updateStatus(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user->isBrandOwner() && !$user->hasRole('admin')) {
            abort(403, 'Only admins can update withdrawal status.');
        }

        $validated = $request->validate([
            'status' => 'required|in:approved,rejected,processed',
            'admin_notes' => 'nullable|string',
        ]);

        $withdrawal = Withdrawal::findOrFail($id);

        $updateData = [
            'status' => $validated['status'],
        ];

        if ($request->has('admin_notes')) {
            $updateData['admin_notes'] = $validated['admin_notes'];
        }

        if ($validated['status'] === 'processed') {
            $updateData['processed_at'] = now();
        }

        $withdrawal->update($updateData);

        return response()->json(['data' => $withdrawal, 'message' => 'Withdrawal status updated.']);
    }
}
