<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\v1\Store;
use App\Traits\HandlesUserAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use HandlesUserAccess;

    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $isBrandOwner = $user->isBrandOwner();
        
        // Build query for users
        // Brand owner can see: store_manager, chef, waiter, kasir
        // Store manager can only see: chef, waiter, kasir
        $roles = $isBrandOwner ? ['store_manager', 'chef', 'waiter', 'kasir'] : ['chef', 'waiter', 'kasir'];
        $query = User::whereIn('role', $roles);

        // Filter by accessible stores/brands
        $accessibleStoreIds = auth()->user()->getAccessibleStoreIds();
        $accessibleBrandIds = auth()->user()->getAccessibleBrandIds();
        
        if ($isBrandOwner) {
            // Brand owner: show store_manager from their brands, and staff from their stores
            $query->where(function($q) use ($accessibleStoreIds, $accessibleBrandIds) {
                // Store managers from accessible brands
                $q->where(function($subQ) use ($accessibleBrandIds) {
                    $subQ->where('role', 'store_manager')
                         ->whereHas('store', function($storeQ) use ($accessibleBrandIds) {
                             $storeQ->whereIn('mdx_brand_id', $accessibleBrandIds);
                         });
                })
                // Staff from accessible stores
                ->orWhere(function($subQ) use ($accessibleStoreIds) {
                    $subQ->whereIn('role', ['chef', 'waiter', 'kasir'])
                         ->whereIn('mdx_store_id', $accessibleStoreIds);
                });
            });
        } else {
            // Store manager: only show staff from their store
            if (!empty($accessibleStoreIds)) {
                $query->whereIn('mdx_store_id', $accessibleStoreIds);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        // Search
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $searchTerm . '%')
                  ->orWhere('phone', 'like', '%' . $searchTerm . '%');
            });
        }

        // Filter by role
        if ($request->has('role') && $request->role !== '') {
            $query->where('role', $request->role);
        }

        // Filter by store
        if ($request->has('store_id') && $request->store_id !== '') {
            $storeId = (int)$request->store_id;
            if (in_array($storeId, $accessibleStoreIds)) {
                if ($isBrandOwner) {
                    // For brand owner: filter store_manager by store, or staff by mdx_store_id
                    $query->where(function($q) use ($storeId) {
                        $q->where(function($subQ) use ($storeId) {
                            $subQ->where('role', 'store_manager')
                                 ->whereHas('store', function($storeQ) use ($storeId) {
                                     $storeQ->where('id', $storeId);
                                 });
                        })
                        ->orWhere(function($subQ) use ($storeId) {
                            $subQ->whereIn('role', ['chef', 'waiter', 'kasir'])
                                 ->where('mdx_store_id', $storeId);
                        });
                    });
                } else {
                    $query->where('mdx_store_id', $storeId);
                }
            }
        }

        // Eager load relationships
        $query->with(['workStore.brand', 'store.brand']);

        // Order by
        $orderBy = $request->get('order_by', 'created_at');
        $orderDir = $request->get('order_dir', 'desc');
        $query->orderBy($orderBy, $orderDir);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $users = $query->paginate($perPage)->withQueryString();

        // Get accessible stores for filter
        $stores = Store::accessibleBy($user)->active()->with('brand')->get();

        return view('admin.users.index', compact('users', 'stores', 'isBrandOwner'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $user = auth()->user();
        $isBrandOwner = $user->isBrandOwner();
        $stores = Store::accessibleBy($user)->active()->with('brand')->get();
        return view('admin.users.create', compact('stores', 'isBrandOwner'));
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $isBrandOwner = $user->isBrandOwner();
        
        // Define allowed roles based on user type
        $allowedRoles = $isBrandOwner ? ['store_manager', 'chef', 'waiter', 'kasir'] : ['chef', 'waiter', 'kasir'];
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:20', 'regex:/^(\+62|62|0)[0-9]{9,12}$/', 'unique:users'],
            'role' => ['required', 'in:' . implode(',', $allowedRoles)],
            'mdx_store_id' => ['required', 'exists:mdx_stores,id'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'phone.regex' => 'Format nomor telepon tidak valid. Gunakan format Indonesia (contoh: 081234567890 atau +6281234567890)',
            'phone.unique' => 'Nomor telepon ini sudah terdaftar.',
            'role.in' => 'Role tidak diizinkan.',
            'mdx_store_id.required' => 'Store harus dipilih.',
        ]);

        // Prevent store manager from creating store manager
        if (!$isBrandOwner && $validated['role'] === 'store_manager') {
            abort(403, 'Store manager tidak dapat membuat store manager lain.');
        }

        // Check if user can access the store
        $accessibleStoreIds = auth()->user()->getAccessibleStoreIds();
        if (!in_array($validated['mdx_store_id'], $accessibleStoreIds)) {
            abort(403, 'Unauthorized access to this store.');
        }

        // Normalize phone number
        $validated['phone'] = $this->normalizePhoneNumber($validated['phone']);

        // Create user
        $newUser = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'role' => $validated['role'],
            'mdx_store_id' => $validated['role'] === 'store_manager' ? null : $validated['mdx_store_id'],
            'password' => Hash::make($validated['password']),
            'email_verified_at' => now(),
        ]);

        // If role is store_manager, assign to store
        if ($validated['role'] === 'store_manager') {
            $store = Store::find($validated['mdx_store_id']);
            if ($store) {
                // Check if store already has a manager
                if ($store->user_id) {
                    return back()->withErrors(['mdx_store_id' => 'Store ini sudah memiliki manager.'])->withInput();
                }
                
                $store->update(['user_id' => $newUser->id]);
            }
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $currentUser = auth()->user();
        $isBrandOwner = $currentUser->isBrandOwner();
        
        // Check if user is editable
        $allowedRoles = $isBrandOwner ? ['store_manager', 'chef', 'waiter', 'kasir'] : ['chef', 'waiter', 'kasir'];
        if (!in_array($user->role, $allowedRoles)) {
            abort(404);
        }

        // Check access
        if ($user->role === 'store_manager') {
            // Check if store manager belongs to accessible brand
            if ($user->store && !in_array($user->store->mdx_brand_id, $currentUser->getAccessibleBrandIds())) {
                abort(403, 'Unauthorized access to this user.');
            }
        } else {
            // Check if staff belongs to accessible store
            $accessibleStoreIds = $currentUser->getAccessibleStoreIds();
            if ($user->mdx_store_id && !in_array($user->mdx_store_id, $accessibleStoreIds)) {
                abort(403, 'Unauthorized access to this user.');
            }
        }

        $stores = Store::accessibleBy($currentUser)->active()->with('brand')->get();
        return view('admin.users.edit', compact('user', 'stores', 'isBrandOwner'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        $currentUser = auth()->user();
        $isBrandOwner = $currentUser->isBrandOwner();
        
        // Check if user is editable
        $allowedRoles = $isBrandOwner ? ['store_manager', 'chef', 'waiter', 'kasir'] : ['chef', 'waiter', 'kasir'];
        if (!in_array($user->role, $allowedRoles)) {
            abort(404);
        }

        // Check access
        if ($user->role === 'store_manager') {
            if ($user->store && !in_array($user->store->mdx_brand_id, $currentUser->getAccessibleBrandIds())) {
                abort(403, 'Unauthorized access to this user.');
            }
        } else {
            $accessibleStoreIds = $currentUser->getAccessibleStoreIds();
            if ($user->mdx_store_id && !in_array($user->mdx_store_id, $accessibleStoreIds)) {
                abort(403, 'Unauthorized access to this user.');
            }
        }

        // Define allowed roles based on user type
        $allowedRoles = $isBrandOwner ? ['store_manager', 'chef', 'waiter', 'kasir'] : ['chef', 'waiter', 'kasir'];
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['required', 'string', 'max:20', 'regex:/^(\+62|62|0)[0-9]{9,12}$/', 'unique:users,phone,' . $user->id],
            'role' => ['required', 'in:' . implode(',', $allowedRoles)],
            'mdx_store_id' => ['required', 'exists:mdx_stores,id'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'phone.regex' => 'Format nomor telepon tidak valid. Gunakan format Indonesia (contoh: 081234567890 atau +6281234567890)',
            'phone.unique' => 'Nomor telepon ini sudah terdaftar.',
            'role.in' => 'Role tidak diizinkan.',
            'mdx_store_id.required' => 'Store harus dipilih.',
        ]);

        // Prevent store manager from changing role to store_manager
        if (!$isBrandOwner && $validated['role'] === 'store_manager') {
            abort(403, 'Store manager tidak dapat mengubah role menjadi store manager.');
        }

        // Check if user can access the store
        $accessibleStoreIds = $currentUser->getAccessibleStoreIds();
        if (!in_array($validated['mdx_store_id'], $accessibleStoreIds)) {
            abort(403, 'Unauthorized access to this store.');
        }

        // Normalize phone number
        $validated['phone'] = $this->normalizePhoneNumber($validated['phone']);

        // Handle store manager assignment
        $oldStoreId = $user->store ? $user->store->id : null;
        $oldRole = $user->role;

        // Remove password if not provided
        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }

        // Update user
        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'role' => $validated['role'],
            'mdx_store_id' => $validated['role'] === 'store_manager' ? null : $validated['mdx_store_id'],
        ];
        if (isset($validated['password'])) {
            $updateData['password'] = $validated['password'];
        }
        $user->update($updateData);

        // Handle store manager store assignment
        if ($validated['role'] === 'store_manager') {
            $store = Store::find($validated['mdx_store_id']);
            if ($store) {
                // If changing store, unassign old store first
                if ($oldStoreId && $oldStoreId != $store->id) {
                    $oldStore = Store::find($oldStoreId);
                    if ($oldStore) {
                        $oldStore->update(['user_id' => null]);
                    }
                }
                
                // Check if new store already has a manager (and it's not this user)
                if ($store->user_id && $store->user_id != $user->id) {
                    return back()->withErrors(['mdx_store_id' => 'Store ini sudah memiliki manager.'])->withInput();
                }
                
                $store->update(['user_id' => $user->id]);
            }
        } elseif ($oldRole === 'store_manager' && $oldStoreId) {
            // If changing from store_manager to other role, unassign from store
            $oldStore = Store::find($oldStoreId);
            if ($oldStore) {
                $oldStore->update(['user_id' => null]);
            }
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        $currentUser = auth()->user();
        $isBrandOwner = $currentUser->isBrandOwner();
        
        // Check if user is editable
        $allowedRoles = $isBrandOwner ? ['store_manager', 'chef', 'waiter', 'kasir'] : ['chef', 'waiter', 'kasir'];
        if (!in_array($user->role, $allowedRoles)) {
            abort(404);
        }

        // Check access
        if ($user->role === 'store_manager') {
            if ($user->store && !in_array($user->store->mdx_brand_id, $currentUser->getAccessibleBrandIds())) {
                abort(403, 'Unauthorized access to this user.');
            }
            // Unassign from store
            if ($user->store) {
                $user->store->update(['user_id' => null]);
            }
        } else {
            $accessibleStoreIds = $currentUser->getAccessibleStoreIds();
            if ($user->mdx_store_id && !in_array($user->mdx_store_id, $accessibleStoreIds)) {
                abort(403, 'Unauthorized access to this user.');
            }
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Normalize Indonesian phone number to standard format (0xxxxxxxxxx).
     */
    private function normalizePhoneNumber(string $phone): string
    {
        // Remove all non-digit characters except +
        $phone = preg_replace('/[^\d+]/', '', $phone);
        
        // Remove +62 or 62 prefix and add 0
        if (preg_match('/^\+?62/', $phone)) {
            $phone = '0' . preg_replace('/^\+?62/', '', $phone);
        }
        
        // Ensure starts with 0
        if (!str_starts_with($phone, '0')) {
            $phone = '0' . $phone;
        }
        
        return $phone;
    }
}
