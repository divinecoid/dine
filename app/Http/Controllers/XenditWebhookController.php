<?php

namespace App\Http\Controllers;

use App\Models\v1\Payment;
use App\Services\XenditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class XenditWebhookController extends Controller
{
    /**
     * Handle Xendit webhook for payment status updates.
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('X-Callback-Token');
        
        $xenditService = new XenditService();
        
        // Verify webhook signature if webhook token is configured
        if (config('services.xendit.webhook_token')) {
            if (!$xenditService->verifyWebhookSignature($signature, $payload)) {
                Log::warning('Xendit webhook signature verification failed', [
                    'signature' => $signature,
                ]);
                return response()->json(['error' => 'Invalid signature'], 401);
            }
        }
        
        $data = json_decode($payload, true);
        
        if (!$data) {
            Log::error('Xendit webhook: Invalid JSON payload', [
                'payload' => $payload,
            ]);
            return response()->json(['error' => 'Invalid payload'], 400);
        }
        
        $event = $data['event'] ?? null;
        $paymentRequestId = $data['data']['id'] ?? null;
        
        if (!$event || !$paymentRequestId) {
            Log::error('Xendit webhook: Missing event or payment request ID', [
                'data' => $data,
            ]);
            return response()->json(['error' => 'Missing required fields'], 400);
        }
        
        // Find payment by Xendit payment ID
        $payment = Payment::where('xendit_payment_id', $paymentRequestId)->first();
        
        if (!$payment) {
            Log::warning('Xendit webhook: Payment not found', [
                'payment_request_id' => $paymentRequestId,
            ]);
            return response()->json(['error' => 'Payment not found'], 404);
        }
        
        // Handle different event types
        switch ($event) {
            case 'payment_request.succeeded':
            case 'payment_request.paid':
                return $this->handlePaymentSucceeded($payment, $data);
                
            case 'payment_request.failed':
            case 'payment_request.expired':
                return $this->handlePaymentFailed($payment, $data);
                
            default:
                Log::info('Xendit webhook: Unhandled event', [
                    'event' => $event,
                    'payment_request_id' => $paymentRequestId,
                ]);
                return response()->json(['message' => 'Event not handled'], 200);
        }
    }
    
    /**
     * Handle successful payment.
     */
    private function handlePaymentSucceeded(Payment $payment, array $data)
    {
        if ($payment->status === 'completed') {
            return response()->json(['message' => 'Payment already processed'], 200);
        }
        
        DB::beginTransaction();
        try {
            $payment->status = 'completed';
            $payment->paid_at = now();
            $payment->save();
            
            // Update order payment status
            $order = $payment->order;
            $totalPaid = $order->payments()
                ->where('status', 'completed')
                ->sum('amount');
            
            if ($totalPaid >= $order->total_amount) {
                $order->payment_status = 'paid';
            } elseif ($totalPaid > 0) {
                $order->payment_status = 'partial';
            }
            $order->save();
            
            DB::commit();
            
            Log::info('Xendit webhook: Payment succeeded', [
                'payment_id' => $payment->id,
                'order_id' => $order->id,
            ]);
            
            return response()->json(['message' => 'Payment processed successfully'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Xendit webhook: Error processing payment', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['error' => 'Error processing payment'], 500);
        }
    }
    
    /**
     * Handle failed payment.
     */
    private function handlePaymentFailed(Payment $payment, array $data)
    {
        // Don't update status to cancelled automatically
        // Let admin handle it manually if needed
        Log::info('Xendit webhook: Payment failed', [
            'payment_id' => $payment->id,
            'event' => $data['event'] ?? 'unknown',
        ]);
        
        return response()->json(['message' => 'Payment failure logged'], 200);
    }
}

