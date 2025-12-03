<?php

namespace App\Http\Controllers;

use App\Models\PhoneVerification;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegistrationController extends Controller
{
    /**
     * Normalize Indonesian phone number to standard format (0xxxxxxxxxx).
     */
    private function normalizePhoneNumber(string $phone): string
    {
        $phone = preg_replace('/[^\d+]/', '', $phone);
        
        if (preg_match('/^\+?62/', $phone)) {
            $phone = '0' . preg_replace('/^\+?62/', '', $phone);
        }
        
        if (!str_starts_with($phone, '0')) {
            $phone = '0' . $phone;
        }
        
        return $phone;
    }

    /**
     * Store initial registration data.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:20', 'regex:/^(\+62|62|0)[0-9]{9,12}$/', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'account_type' => ['required', 'in:CORE,SCALE,INFINITE'],
        ], [
            'phone.regex' => 'Format nomor telepon tidak valid.',
            'phone.unique' => 'Nomor telepon ini sudah terdaftar.',
            'email.unique' => 'Email ini sudah terdaftar.',
        ]);

        // Normalize phone number
        $validated['phone'] = $this->normalizePhoneNumber($validated['phone']);

        // Check if email or phone already exists in registrations
        $existingRegistration = Registration::where('email', $validated['email'])
            ->orWhere('phone', $validated['phone'])
            ->whereIn('status', ['pending_verification', 'pending_payment'])
            ->first();

        if ($existingRegistration) {
            return back()->withErrors(['email' => 'Registrasi dengan email/nomor telepon ini sedang dalam proses.'])->withInput();
        }

        DB::beginTransaction();
        try {
            // Create registration record
            $registration = Registration::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => Hash::make($validated['password']),
                'account_type' => $validated['account_type'],
                'status' => 'pending_verification',
                'expires_at' => now()->addHours(24), // Registration expires in 24 hours
            ]);

            // Set payment amount if required
            if ($registration->requiresPayment()) {
                $registration->payment_amount = Registration::getPaymentAmount($validated['account_type']);
                $registration->save();
            }

            DB::commit();

            // Send OTP
            $this->sendOTP($registration);

            // Redirect to OTP verification page
            return redirect()->route('registration.verify', ['registration' => $registration->id])
                ->with('success', 'Kode verifikasi telah dikirim ke nomor telepon Anda.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal membuat registrasi. Silakan coba lagi.'])->withInput();
        }
    }

    /**
     * Send OTP to phone number.
     */
    public function sendOTP(Registration $registration)
    {
        // Invalidate previous OTPs for this phone
        PhoneVerification::where('phone', $registration->phone)
            ->where('status', 'pending')
            ->update(['status' => 'expired']);

        // Generate new OTP
        $otp = PhoneVerification::create([
            'phone' => $registration->phone,
            'code' => PhoneVerification::generateCode(),
            'registration_id' => $registration->id,
            'expires_at' => now()->addMinutes(10), // OTP expires in 10 minutes
        ]);

        // TODO: Send SMS via SMS gateway
        // For now, we'll store it in session for testing
        session(['otp_code_' . $registration->id => $otp->code]);

        return $otp;
    }

    /**
     * Show OTP verification page.
     */
    public function showVerify(Request $request, Registration $registration)
    {
        if ($registration->status === 'completed') {
            return redirect()->route('login')->with('success', 'Registrasi sudah selesai. Silakan login.');
        }

        if ($registration->isExpired()) {
            $registration->update(['status' => 'expired']);
            return redirect()->route('register')->withErrors(['error' => 'Registrasi telah kedaluwarsa. Silakan daftar kembali.']);
        }

        return view('auth.verify-otp', compact('registration'));
    }

    /**
     * Verify OTP.
     */
    public function verifyOTP(Request $request, Registration $registration)
    {
        $request->validate([
            'otp_code' => ['required', 'string', 'size:6'],
        ]);

        if ($registration->status !== 'pending_verification') {
            return back()->withErrors(['otp_code' => 'Status registrasi tidak valid.']);
        }

        // Find active OTP
        $phoneVerification = PhoneVerification::where('registration_id', $registration->id)
            ->where('phone', $registration->phone)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (!$phoneVerification) {
            return back()->withErrors(['otp_code' => 'Kode OTP tidak valid atau telah kedaluwarsa.']);
        }

        if ($phoneVerification->maxAttemptsReached()) {
            return back()->withErrors(['otp_code' => 'Terlalu banyak percobaan. Silakan minta kode baru.']);
        }

        // Verify OTP
        if ($phoneVerification->code !== $request->otp_code) {
            $phoneVerification->incrementAttempts();
            $attemptsLeft = $phoneVerification->max_attempts - $phoneVerification->attempts;
            return back()->withErrors(['otp_code' => "Kode OTP salah. Sisa percobaan: {$attemptsLeft}"]);
        }

        // OTP verified
        $phoneVerification->update([
            'status' => 'verified',
            'verified_at' => now(),
        ]);

        $registration->update([
            'phone_verified' => true,
            'phone_verified_at' => now(),
        ]);

        // Check if payment is required
        if ($registration->requiresPayment()) {
            $registration->update(['status' => 'pending_payment']);
            return redirect()->route('registration.payment', ['registration' => $registration->id]);
        }

        // For CORE, complete registration immediately
        return $this->completeRegistration($registration, $request);
    }

    /**
     * Resend OTP.
     */
    public function resendOTP(Registration $registration)
    {
        $this->sendOTP($registration);
        return back()->with('success', 'Kode verifikasi baru telah dikirim.');
    }

    /**
     * Show payment page.
     */
    public function showPayment(Request $request, Registration $registration)
    {
        if ($registration->status !== 'pending_payment') {
            return redirect()->route('register')->withErrors(['error' => 'Status registrasi tidak valid.']);
        }

        if (!$registration->phone_verified) {
            return redirect()->route('registration.verify', ['registration' => $registration->id])
                ->withErrors(['error' => 'Silakan verifikasi nomor telepon terlebih dahulu.']);
        }

        return view('auth.payment', compact('registration'));
    }

    /**
     * Generate payment QRIS/VA.
     */
    public function generatePayment(Registration $registration)
    {
        if ($registration->status !== 'pending_payment') {
            return response()->json([
                'success' => false,
                'message' => 'Status registrasi tidak valid.',
            ], 422);
        }

        // Generate payment reference if not exists
        if (!$registration->payment_reference) {
            $registration->payment_reference = $registration->generatePaymentReference();
            $registration->save();
        }

        // Generate QRIS dummy (similar to order payment)
        $merchantName = 'DINE.CO.ID';
        $amountFormatted = str_pad((int)($registration->payment_amount), 13, '0', STR_PAD_LEFT);
        
        $qrisContent = '00020101021226650016COM.DINE.CO.ID0104PAY';
        $qrisContent .= '5204000053033605802ID';
        $qrisContent .= '59' . str_pad(strlen($merchantName), 2, '0', STR_PAD_LEFT) . $merchantName;
        $qrisContent .= '60' . str_pad(strlen('JAKARTA'), 2, '0', STR_PAD_LEFT) . 'JAKARTA';
        $qrisContent .= '61' . str_pad(strlen('12345'), 2, '0', STR_PAD_LEFT) . '12345';
        $qrisContent .= '62' . str_pad(strlen($registration->payment_reference), 2, '0', STR_PAD_LEFT) . $registration->payment_reference;
        $qrisContent .= '54' . str_pad(strlen($amountFormatted), 2, '0', STR_PAD_LEFT) . $amountFormatted;
        $qrisContent .= '6304';

        $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($qrisContent);

        // Generate Virtual Account (dummy)
        $virtualAccount = '888' . str_pad($registration->id, 10, '0', STR_PAD_LEFT);

        return response()->json([
            'success' => true,
            'data' => [
                'qris_url' => $qrCodeUrl,
                'virtual_account' => $virtualAccount,
                'payment_reference' => $registration->payment_reference,
                'amount' => $registration->payment_amount,
                'account_type' => $registration->account_type,
            ],
        ]);
    }

    /**
     * Check payment status.
     */
    public function checkPaymentStatus(Registration $registration)
    {
        // Refresh registration data
        $registration->refresh();
        
        // Check if payment is already verified
        if ($registration->payment_verified_at) {
            return response()->json([
                'success' => true,
                'data' => [
                    'is_paid' => true,
                    'status' => $registration->status,
                ],
            ]);
        }
        
        // TODO: Check with payment gateway
        // For now, return pending status (dummy)
        // In production, verify payment with payment gateway API
        
        return response()->json([
            'success' => true,
            'data' => [
                'is_paid' => false,
                'status' => $registration->status,
            ],
        ]);
    }

    /**
     * Complete registration after payment verification.
     */
    public function completeRegistration(Registration $registration, Request $request = null)
    {
        if ($registration->status === 'completed') {
            return redirect()->route('login')->with('success', 'Registrasi sudah selesai. Silakan login.');
        }

        DB::beginTransaction();
        try {
            // Create user account
            $user = User::create([
                'name' => $registration->name,
                'email' => $registration->email,
                'phone' => $registration->phone,
                'password' => $registration->password,
                'role' => 'brand_owner',
                'account_type' => $registration->account_type,
                'email_verified_at' => now(),
            ]);

            // Mark registration as completed
            $registration->update([
                'status' => 'completed',
            ]);

            DB::commit();

            // Auto login
            Auth::login($user);
            if ($request) {
                $request->session()->regenerate();
            } else {
                request()->session()->regenerate();
            }

            return redirect()->route('admin.dashboard')
                ->with('success', 'Registrasi berhasil! Selamat datang di DINE.CO.ID.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('register')->withErrors(['error' => 'Gagal menyelesaikan registrasi. Silakan coba lagi.']);
        }
    }
}
