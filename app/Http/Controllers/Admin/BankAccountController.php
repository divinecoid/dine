<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\v1\BankAccount;
use App\Models\v1\Brand;
use App\Models\v1\Store;
use App\Traits\HandlesUserAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BankAccountController extends Controller
{
    use HandlesUserAccess;

    /**
     * Display a listing of the bank accounts.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Build query for bank accounts
        $query = BankAccount::query();

        // Filter by accessible brands/stores
        if ($user->isBrandOwner()) {
            $brandIds = $user->getAccessibleBrandIds();
            $storeIds = $user->getAccessibleStoreIds();
            
            $query->where(function($q) use ($brandIds, $storeIds) {
                if (!empty($brandIds)) {
                    $q->whereIn('mdx_brand_id', $brandIds);
                }
                if (!empty($storeIds)) {
                    $q->orWhereIn('mdx_store_id', $storeIds);
                }
            });
        } elseif ($user->isStoreManager() && $user->store) {
            $query->where('mdx_store_id', $user->store->id);
        } else {
            $query->whereRaw('1 = 0');
        }

        // Search
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('account_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('account_number', 'like', '%' . $searchTerm . '%')
                  ->orWhere('bank_name', 'like', '%' . $searchTerm . '%');
            });
        }

        // Filter by bank
        if ($request->has('bank_name') && $request->bank_name !== '') {
            $query->where('bank_name', $request->bank_name);
        }

        // Filter by verified status
        if ($request->has('is_verified') && $request->is_verified !== '') {
            $query->where('is_verified', $request->is_verified === '1');
        }

        // Filter by owner type
        if ($request->has('owner_type') && $request->owner_type !== '') {
            if ($request->owner_type === 'brand') {
                $query->whereNotNull('mdx_brand_id')->whereNull('mdx_store_id');
            } elseif ($request->owner_type === 'store') {
                $query->whereNull('mdx_brand_id')->whereNotNull('mdx_store_id');
            }
        }

        // Eager load relationships
        $query->with(['brand', 'store.brand']);

        // Order by
        $orderBy = $request->get('order_by', 'created_at');
        $orderDir = $request->get('order_dir', 'desc');
        $query->orderBy($orderBy, $orderDir);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $bankAccounts = $query->paginate($perPage)->withQueryString();

        // Get unique bank names for filter
        $bankNames = BankAccount::select('bank_name')
            ->distinct()
            ->orderBy('bank_name')
            ->pluck('bank_name')
            ->toArray();

        // Get accessible brands and stores for filter
        $brands = Brand::accessibleBy($user)->active()->get();
        $stores = Store::accessibleBy($user)->active()->with('brand')->get();

        return view('admin.bank-accounts.index', compact('bankAccounts', 'bankNames', 'brands', 'stores'));
    }

    /**
     * Show the form for creating a new bank account.
     */
    public function create()
    {
        $user = auth()->user();
        $brands = Brand::accessibleBy($user)->active()->get();
        $stores = Store::accessibleBy($user)->active()->with('brand')->get();
        
        return view('admin.bank-accounts.create', compact('brands', 'stores'));
    }

    /**
     * Store a newly created bank account.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'mdx_brand_id' => ['nullable', 'exists:mdx_brands,id'],
            'mdx_store_id' => ['nullable', 'exists:mdx_stores,id'],
            'account_name' => ['required', 'string', 'max:255'],
            'account_number' => ['required', 'string', 'max:50'],
            'bank_name' => ['required', 'string', 'max:100'],
            'bank_code' => ['nullable', 'string', 'max:20'],
            'branch_name' => ['nullable', 'string', 'max:255'],
            'currency' => ['nullable', 'string', 'max:3'],
            'notes' => ['nullable', 'string'],
        ], [
            'account_name.required' => 'Nama pemilik rekening wajib diisi.',
            'account_number.required' => 'Nomor rekening wajib diisi.',
            'bank_name.required' => 'Nama bank wajib diisi.',
        ]);

        // Validate that either brand_id or store_id is set, but not both
        if (empty($validated['mdx_brand_id']) && empty($validated['mdx_store_id'])) {
            return back()->withErrors(['mdx_brand_id' => 'Pilih Brand atau Store.'])->withInput();
        }
        if (!empty($validated['mdx_brand_id']) && !empty($validated['mdx_store_id'])) {
            return back()->withErrors(['mdx_brand_id' => 'Pilih salah satu: Brand atau Store, tidak boleh keduanya.'])->withInput();
        }

        // Check access
        if (!empty($validated['mdx_brand_id'])) {
            $accessibleBrandIds = $user->getAccessibleBrandIds();
            if (!in_array($validated['mdx_brand_id'], $accessibleBrandIds)) {
                abort(403, 'Unauthorized access to this brand.');
            }
        }
        if (!empty($validated['mdx_store_id'])) {
            $accessibleStoreIds = $user->getAccessibleStoreIds();
            if (!in_array($validated['mdx_store_id'], $accessibleStoreIds)) {
                abort(403, 'Unauthorized access to this store.');
            }
        }

        // Set default currency
        if (empty($validated['currency'])) {
            $validated['currency'] = 'IDR';
        }

        BankAccount::create($validated);

        return redirect()->route('admin.bank-accounts.index')
            ->with('success', 'Rekening berhasil dibuat.');
    }

    /**
     * Show the form for editing the specified bank account.
     */
    public function edit(BankAccount $bankAccount)
    {
        $user = auth()->user();
        
        // Check access
        if ($bankAccount->mdx_brand_id) {
            $accessibleBrandIds = $user->getAccessibleBrandIds();
            if (!in_array($bankAccount->mdx_brand_id, $accessibleBrandIds)) {
                abort(403, 'Unauthorized access to this bank account.');
            }
        }
        if ($bankAccount->mdx_store_id) {
            $accessibleStoreIds = $user->getAccessibleStoreIds();
            if (!in_array($bankAccount->mdx_store_id, $accessibleStoreIds)) {
                abort(403, 'Unauthorized access to this bank account.');
            }
        }

        $brands = Brand::accessibleBy($user)->active()->get();
        $stores = Store::accessibleBy($user)->active()->with('brand')->get();
        
        return view('admin.bank-accounts.edit', compact('bankAccount', 'brands', 'stores'));
    }

    /**
     * Update the specified bank account.
     */
    public function update(Request $request, BankAccount $bankAccount)
    {
        $user = auth()->user();
        
        // Check access
        if ($bankAccount->mdx_brand_id) {
            $accessibleBrandIds = $user->getAccessibleBrandIds();
            if (!in_array($bankAccount->mdx_brand_id, $accessibleBrandIds)) {
                abort(403, 'Unauthorized access to this bank account.');
            }
        }
        if ($bankAccount->mdx_store_id) {
            $accessibleStoreIds = $user->getAccessibleStoreIds();
            if (!in_array($bankAccount->mdx_store_id, $accessibleStoreIds)) {
                abort(403, 'Unauthorized access to this bank account.');
            }
        }

        $validated = $request->validate([
            'mdx_brand_id' => ['nullable', 'exists:mdx_brands,id'],
            'mdx_store_id' => ['nullable', 'exists:mdx_stores,id'],
            'account_name' => ['required', 'string', 'max:255'],
            'account_number' => ['required', 'string', 'max:50'],
            'bank_name' => ['required', 'string', 'max:100'],
            'bank_code' => ['nullable', 'string', 'max:20'],
            'branch_name' => ['nullable', 'string', 'max:255'],
            'currency' => ['nullable', 'string', 'max:3'],
            'notes' => ['nullable', 'string'],
        ], [
            'account_name.required' => 'Nama pemilik rekening wajib diisi.',
            'account_number.required' => 'Nomor rekening wajib diisi.',
            'bank_name.required' => 'Nama bank wajib diisi.',
        ]);

        // Validate that either brand_id or store_id is set, but not both
        if (empty($validated['mdx_brand_id']) && empty($validated['mdx_store_id'])) {
            return back()->withErrors(['mdx_brand_id' => 'Pilih Brand atau Store.'])->withInput();
        }
        if (!empty($validated['mdx_brand_id']) && !empty($validated['mdx_store_id'])) {
            return back()->withErrors(['mdx_brand_id' => 'Pilih salah satu: Brand atau Store, tidak boleh keduanya.'])->withInput();
        }

        // Check access for new brand/store
        if (!empty($validated['mdx_brand_id'])) {
            $accessibleBrandIds = $user->getAccessibleBrandIds();
            if (!in_array($validated['mdx_brand_id'], $accessibleBrandIds)) {
                abort(403, 'Unauthorized access to this brand.');
            }
        }
        if (!empty($validated['mdx_store_id'])) {
            $accessibleStoreIds = $user->getAccessibleStoreIds();
            if (!in_array($validated['mdx_store_id'], $accessibleStoreIds)) {
                abort(403, 'Unauthorized access to this store.');
            }
        }

        // Set default currency
        if (empty($validated['currency'])) {
            $validated['currency'] = 'IDR';
        }

        $bankAccount->update($validated);

        return redirect()->route('admin.bank-accounts.index')
            ->with('success', 'Rekening berhasil diperbarui.');
    }

    /**
     * Remove the specified bank account.
     */
    public function destroy(BankAccount $bankAccount)
    {
        $user = auth()->user();
        
        // Check access
        if ($bankAccount->mdx_brand_id) {
            $accessibleBrandIds = $user->getAccessibleBrandIds();
            if (!in_array($bankAccount->mdx_brand_id, $accessibleBrandIds)) {
                abort(403, 'Unauthorized access to this bank account.');
            }
        }
        if ($bankAccount->mdx_store_id) {
            $accessibleStoreIds = $user->getAccessibleStoreIds();
            if (!in_array($bankAccount->mdx_store_id, $accessibleStoreIds)) {
                abort(403, 'Unauthorized access to this bank account.');
            }
        }

        $bankAccount->delete();

        return redirect()->route('admin.bank-accounts.index')
            ->with('success', 'Rekening berhasil dihapus.');
    }

    /**
     * Verify bank account by calling bank API.
     */
    public function verify(BankAccount $bankAccount)
    {
        $user = auth()->user();
        
        // Check access
        if ($bankAccount->mdx_brand_id) {
            $accessibleBrandIds = $user->getAccessibleBrandIds();
            if (!in_array($bankAccount->mdx_brand_id, $accessibleBrandIds)) {
                abort(403, 'Unauthorized access to this bank account.');
            }
        }
        if ($bankAccount->mdx_store_id) {
            $accessibleStoreIds = $user->getAccessibleStoreIds();
            if (!in_array($bankAccount->mdx_store_id, $accessibleStoreIds)) {
                abort(403, 'Unauthorized access to this bank account.');
            }
        }

        try {
            // Get bank API endpoint based on bank code or bank name
            $apiEndpoint = $this->getBankApiEndpoint($bankAccount->bank_code ?? $bankAccount->bank_name);
            
            if (!$apiEndpoint) {
                return back()->withErrors(['error' => 'API untuk bank ini belum dikonfigurasi.']);
            }

            // Call bank API to verify account
            $response = Http::timeout(30)->post($apiEndpoint, [
                'account_number' => $bankAccount->account_number,
                'account_name' => $bankAccount->account_name,
                'bank_code' => $bankAccount->bank_code,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Check if account is verified (adjust based on actual API response)
                if (isset($data['verified']) && $data['verified'] === true) {
                    $bankAccount->update(['is_verified' => true]);
                    return back()->with('success', 'Rekening berhasil diverifikasi.');
                } else {
                    return back()->withErrors(['error' => 'Rekening tidak dapat diverifikasi. Pastikan nomor rekening dan nama pemilik benar.']);
                }
            } else {
                Log::error('Bank API verification failed', [
                    'bank_account_id' => $bankAccount->id,
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
                
                return back()->withErrors(['error' => 'Gagal memverifikasi rekening. Silakan coba lagi nanti.']);
            }
        } catch (\Exception $e) {
            Log::error('Bank API verification error', [
                'bank_account_id' => $bankAccount->id,
                'error' => $e->getMessage(),
            ]);
            
            return back()->withErrors(['error' => 'Terjadi kesalahan saat memverifikasi rekening.']);
        }
    }

    /**
     * Get bank API endpoint based on bank code or name.
     */
    private function getBankApiEndpoint($bankIdentifier): ?string
    {
        // Map bank codes/names to their API endpoints
        // This should be configured in config file or database in production
        $bankApis = [
            'BCA' => env('BCA_API_ENDPOINT', 'https://api.bca.co.id/v1/account/verify'),
            'BNI' => env('BNI_API_ENDPOINT', 'https://api.bni.co.id/v1/account/verify'),
            'MANDIRI' => env('MANDIRI_API_ENDPOINT', 'https://api.bankmandiri.co.id/v1/account/verify'),
            'BRI' => env('BRI_API_ENDPOINT', 'https://api.bri.co.id/v1/account/verify'),
            'CIMB' => env('CIMB_API_ENDPOINT', 'https://api.cimbniaga.co.id/v1/account/verify'),
            'PERMATA' => env('PERMATA_API_ENDPOINT', 'https://api.permata.co.id/v1/account/verify'),
        ];

        $bankIdentifier = strtoupper($bankIdentifier ?? '');
        
        // Try to find by exact match
        if (isset($bankApis[$bankIdentifier])) {
            return $bankApis[$bankIdentifier];
        }

        // Try to find by partial match
        foreach ($bankApis as $key => $endpoint) {
            if (stripos($bankIdentifier, $key) !== false || stripos($key, $bankIdentifier) !== false) {
                return $endpoint;
            }
        }

        return null;
    }
}
