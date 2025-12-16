<?php

namespace App\Http\Controllers\v1;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    /**
     * Handle a login request.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => ['required', 'string', 'regex:/^(\+62|62|0)[0-9]{9,12}$/'],
            'password' => 'required|string',
        ], [
            'phone.regex' => 'Format nomor telepon tidak valid. Gunakan format Indonesia (contoh: 081234567890 atau +6281234567890)',
        ]);

        $phone = $request->input('phone');
        $password = $request->input('password');

        // Normalize phone number to standard format (0xxxxxxxxxx)
        $normalizedPhone = $this->normalizePhoneNumber($phone);
        
        // Find user by normalized phone number
        $user = User::where('phone', $normalizedPhone)->first();

        // Check if user exists and password is correct
        if (!$user || !Hash::check($password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor telepon atau password yang Anda masukkan salah.',
            ], 401);
        }

        // Check if user has valid role
        if (!in_array($user->role, ['brand_owner', 'store_manager', 'kasir'])) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda tidak memiliki akses ke sistem.',
            ], 403);
        }

        // Create token for API authentication
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'role' => $user->role,
                    'account_type' => $user->account_type,
                ],
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ]);
    }

    /**
     * Handle a registration request.
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:20', 'regex:/^(\+62|62|0)[0-9]{9,12}$/', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'phone.regex' => 'Format nomor telepon tidak valid. Gunakan format Indonesia (contoh: 081234567890 atau +6281234567890)',
            'phone.unique' => 'Nomor telepon ini sudah terdaftar.',
        ]);

        // Normalize phone number
        $validated['phone'] = $this->normalizePhoneNumber($validated['phone']);

        // Create user with brand_owner role
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role' => 'brand_owner',
            'email_verified_at' => now(),
        ]);

        // Create token for API authentication
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'role' => $user->role,
                    'account_type' => $user->account_type,
                ],
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ], 201);
    }

    /**
     * Get the authenticated user.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'role' => $user->role,
                    'account_type' => $user->account_type,
                    'mdx_store_id' => $user->mdx_store_id,
                ],
            ],
        ]);
    }

    /**
     * Handle a logout request.
     */
    public function logout(Request $request): JsonResponse
    {
        // Revoke the token that was used to authenticate the current request
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil',
        ]);
    }

    /**
     * Revoke all tokens for the authenticated user.
     */
    public function logoutAll(Request $request): JsonResponse
    {
        // Revoke all tokens for the user
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout dari semua perangkat berhasil',
        ]);
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

