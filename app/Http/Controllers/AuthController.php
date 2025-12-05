<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        // Redirect to dashboard if already authenticated
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }
        
        return view('auth.login');
    }

    /**
     * Handle a login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'string', 'regex:/^(\+62|62|0)[0-9]{9,12}$/'],
            'password' => 'required',
        ], [
            'phone.regex' => 'Format nomor telepon tidak valid. Gunakan format Indonesia (contoh: 081234567890 atau +6281234567890)',
        ]);

        $phone = $request->input('phone');
        $password = $request->input('password');
        $remember = $request->filled('remember');

        // Normalize phone number to standard format (0xxxxxxxxxx)
        $normalizedPhone = $this->normalizePhoneNumber($phone);
        
        // Find user by normalized phone number
        $user = User::where('phone', $normalizedPhone)->first();

        // Check if user exists and password is correct
        if ($user && Hash::check($password, $user->password)) {
            // Check if user has valid role (brand_owner or store_manager)
            if (!in_array($user->role, ['brand_owner', 'store_manager'])) {
                throw ValidationException::withMessages([
                    'phone' => ['Akun Anda tidak memiliki akses ke dashboard admin.'],
                ]);
            }

            // Log in the user
            Auth::login($user, $remember);
            $request->session()->regenerate();
            
            return redirect()->intended(route('admin.dashboard'));
        }

        throw ValidationException::withMessages([
            'phone' => ['Nomor telepon atau password yang Anda masukkan salah.'],
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

    /**
     * Show the registration form.
     */
    public function showRegisterForm(Request $request)
    {
        // Redirect to dashboard if already authenticated
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }
        
        // Get package parameter from query string
        $package = $request->query('package');
        
        // Validate package parameter
        $validPackages = ['CORE', 'SCALE', 'INFINITE'];
        if ($package && in_array(strtoupper($package), $validPackages)) {
            $package = strtoupper($package);
        } else {
            $package = null;
        }
        
        return view('auth.register', ['selectedPackage' => $package]);
    }

    /**
     * Handle a registration request.
     */
    public function register(Request $request)
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

        // Automatically log in the user
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('admin.dashboard')
            ->with('success', 'Registrasi berhasil! Selamat datang di DINE.CO.ID.');
    }

    /**
     * Handle a logout request.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
