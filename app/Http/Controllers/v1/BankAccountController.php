<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\v1\BankAccount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BankAccountController extends Controller
{
    /**
     * Display a listing of the bank accounts.
     */
    public function index(Request $request): JsonResponse
    {
        $query = BankAccount::query();

        // Filter by brand_id
        if ($request->has('brand_id')) {
            $query->forBrand($request->brand_id);
        }

        // Filter by store_id
        if ($request->has('store_id')) {
            $query->forStore($request->store_id);
        }

        // Filter by is_active
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Filter by owner type
        if ($request->has('owner_type')) {
            if ($request->owner_type === 'brand') {
                $query->whereNotNull('mdx_brand_id')->whereNull('mdx_store_id');
            } elseif ($request->owner_type === 'store') {
                $query->whereNull('mdx_brand_id')->whereNotNull('mdx_store_id');
            }
        }

        // Search by account name or account number
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('account_name', 'like', '%' . $search . '%')
                  ->orWhere('account_number', 'like', '%' . $search . '%')
                  ->orWhere('bank_name', 'like', '%' . $search . '%');
            });
        }

        // Load relationships
        $with = $request->get('with', '');
        if ($with) {
            $relationships = array_filter(explode(',', $with));
            $validRelationships = ['brand', 'store'];
            $relationships = array_intersect($relationships, $validRelationships);
            if (!empty($relationships)) {
                $query->with($relationships);
            }
        } else {
            // Default load owner relationship
            $query->with(['brand', 'store']);
        }

        // Order by
        $orderBy = $request->get('order_by', 'created_at');
        $orderDir = $request->get('order_dir', 'desc');
        $query->orderBy($orderBy, $orderDir);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $bankAccounts = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $bankAccounts->items(),
            'meta' => [
                'current_page' => $bankAccounts->currentPage(),
                'last_page' => $bankAccounts->lastPage(),
                'per_page' => $bankAccounts->perPage(),
                'total' => $bankAccounts->total(),
            ],
        ]);
    }

    /**
     * Store a newly created bank account.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'mdx_brand_id' => ['nullable', 'required_without:mdx_store_id', 'exists:mdx_brands,id'],
            'mdx_store_id' => ['nullable', 'required_without:mdx_brand_id', 'exists:mdx_stores,id'],
            'account_name' => ['required', 'string', 'max:255'],
            'account_number' => ['required', 'string', 'max:255'],
            'bank_name' => ['required', 'string', 'max:255'],
            'bank_code' => ['nullable', 'string', 'max:255'],
            'branch_name' => ['nullable', 'string', 'max:255'],
            'currency' => ['nullable', 'string', 'size:3'],
            'balance' => ['nullable', 'numeric', 'min:0'],
            'minimum_balance' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Validate that exactly one of brand_id or store_id is set
        $data = $validator->validated();
        if (empty($data['mdx_brand_id']) && empty($data['mdx_store_id'])) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => [
                    'mdx_brand_id' => ['Either mdx_brand_id or mdx_store_id must be provided.'],
                    'mdx_store_id' => ['Either mdx_brand_id or mdx_store_id must be provided.'],
                ],
            ], 422);
        }

        if (!empty($data['mdx_brand_id']) && !empty($data['mdx_store_id'])) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => [
                    'mdx_brand_id' => ['Cannot set both mdx_brand_id and mdx_store_id. Only one should be provided.'],
                    'mdx_store_id' => ['Cannot set both mdx_brand_id and mdx_store_id. Only one should be provided.'],
                ],
            ], 422);
        }

        try {
            $bankAccount = BankAccount::create($data);
            $bankAccount->load(['brand', 'store']);

            return response()->json([
                'success' => true,
                'message' => 'Bank account created successfully',
                'data' => $bankAccount,
            ], 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => [
                    'general' => [$e->getMessage()],
                ],
            ], 422);
        }
    }

    /**
     * Display the specified bank account.
     */
    public function show(BankAccount $bankAccount): JsonResponse
    {
        $bankAccount->load(['brand', 'store']);

        return response()->json([
            'success' => true,
            'data' => $bankAccount,
        ]);
    }

    /**
     * Update the specified bank account.
     */
    public function update(Request $request, BankAccount $bankAccount): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'mdx_brand_id' => ['nullable', 'exists:mdx_brands,id'],
            'mdx_store_id' => ['nullable', 'exists:mdx_stores,id'],
            'account_name' => ['sometimes', 'required', 'string', 'max:255'],
            'account_number' => ['sometimes', 'required', 'string', 'max:255'],
            'bank_name' => ['sometimes', 'required', 'string', 'max:255'],
            'bank_code' => ['nullable', 'string', 'max:255'],
            'branch_name' => ['nullable', 'string', 'max:255'],
            'currency' => ['nullable', 'string', 'size:3'],
            'balance' => ['nullable', 'numeric', 'min:0'],
            'minimum_balance' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Validate that exactly one of brand_id or store_id is set
        $data = $validator->validated();
        
        // If updating owner, ensure only one is set
        if (isset($data['mdx_brand_id']) || isset($data['mdx_store_id'])) {
            $brandId = $data['mdx_brand_id'] ?? $bankAccount->mdx_brand_id;
            $storeId = $data['mdx_store_id'] ?? $bankAccount->mdx_store_id;

            if (empty($brandId) && empty($storeId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => [
                        'mdx_brand_id' => ['Either mdx_brand_id or mdx_store_id must be provided.'],
                        'mdx_store_id' => ['Either mdx_brand_id or mdx_store_id must be provided.'],
                    ],
                ], 422);
            }

            if (!empty($brandId) && !empty($storeId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => [
                        'mdx_brand_id' => ['Cannot set both mdx_brand_id and mdx_store_id. Only one should be provided.'],
                        'mdx_store_id' => ['Cannot set both mdx_brand_id and mdx_store_id. Only one should be provided.'],
                    ],
                ], 422);
            }
        }

        try {
            $bankAccount->update($data);
            $bankAccount->load(['brand', 'store']);

            return response()->json([
                'success' => true,
                'message' => 'Bank account updated successfully',
                'data' => $bankAccount->fresh(),
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => [
                    'general' => [$e->getMessage()],
                ],
            ], 422);
        }
    }

    /**
     * Remove the specified bank account.
     */
    public function destroy(BankAccount $bankAccount): JsonResponse
    {
        $bankAccount->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bank account deleted successfully',
        ]);
    }

    /**
     * Restore the specified bank account.
     */
    public function restore(int $id): JsonResponse
    {
        $bankAccount = BankAccount::withTrashed()->findOrFail($id);
        $bankAccount->restore();

        return response()->json([
            'success' => true,
            'message' => 'Bank account restored successfully',
            'data' => $bankAccount,
        ]);
    }

    /**
     * Update balance (add or subtract).
     */
    public function updateBalance(Request $request, BankAccount $bankAccount): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'amount' => ['required', 'numeric'],
            'type' => ['required', 'string', 'in:add,subtract'],
            'notes' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $amount = abs($data['amount']);
        $type = $data['type'];

        DB::beginTransaction();
        try {
            if ($type === 'add') {
                $bankAccount->addBalance($amount);
                $message = "Balance added successfully. New balance: {$bankAccount->fresh()->balance}";
            } else {
                if (!$bankAccount->hasSufficientBalance($amount)) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Insufficient balance',
                        'errors' => [
                            'amount' => ['Insufficient balance. Available: ' . $bankAccount->balance],
                        ],
                    ], 422);
                }

                $bankAccount->subtractBalance($amount);
                $message = "Balance subtracted successfully. New balance: {$bankAccount->fresh()->balance}";
            }

            // Update notes if provided
            if (isset($data['notes'])) {
                $currentNotes = $bankAccount->notes ?? '';
                $newNote = date('Y-m-d H:i:s') . ': ' . $data['notes'] . "\n";
                $bankAccount->update(['notes' => $newNote . $currentNotes]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $bankAccount->fresh(['brand', 'store']),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to update balance',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get balance information.
     */
    public function getBalance(BankAccount $bankAccount): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'balance' => $bankAccount->balance,
                'minimum_balance' => $bankAccount->minimum_balance,
                'available_balance' => $bankAccount->minimum_balance 
                    ? ($bankAccount->balance - $bankAccount->minimum_balance) 
                    : $bankAccount->balance,
                'currency' => $bankAccount->currency,
                'is_active' => $bankAccount->is_active,
            ],
        ]);
    }
}

