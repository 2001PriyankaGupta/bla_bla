<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use Carbon\Carbon;

class PaymentController extends Controller
{
    public function verifyPayment(Request $request)
    {
        // 1. Validate Input
        $validator = Validator::make($request->all(), [
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id' => 'nullable|string',
            'booking_id' => 'required|exists:bookings,id',
            'razorpay_signature' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = JWTAuth::parseToken()->authenticate();
            
            // 2. Validate Booking Ownership
            $booking = Booking::find($request->booking_id);
            if (!$booking) {
                return response()->json(['success' => false, 'message' => 'Booking not found'], 404);
            }
            if ($booking->user_id !== $user->id) {
                return response()->json(['success' => false, 'message' => 'Unauthorized booking access'], 403);
            }

            // 3. Razorpay Configuration
            $key = config('services.razorpay.key') ?? 'rzp_test_oZWpPCp1BkgtEg'; // Fallback to provided key
            $secret = config('services.razorpay.secret') ?? 'oj1PKjL65FEsvMNV3Qko9h3D'; // Fallback to provided secret

            Log::info('Verifying Razorpay payment', [
                'payment_id' => $request->razorpay_payment_id,
                'booking_id' => $booking->id,
                'user_id' => $user->id
            ]);

            // 4. Verify Signature (if order_id present)
            if ($request->filled('razorpay_order_id') && $request->filled('razorpay_signature')) {
                $generatedSignature = hash_hmac('sha256', $request->razorpay_order_id . '|' . $request->razorpay_payment_id, $secret);
                if ($generatedSignature !== $request->razorpay_signature) {
                    return response()->json(['success' => false, 'message' => 'Invalid payment signature'], 400);
                }
            }

            // 5. Fetch Payment Details from Razorpay
            $paymentResponse = Http::withoutVerifying()
                ->withBasicAuth($key, $secret)
                ->get("https://api.razorpay.com/v1/payments/{$request->razorpay_payment_id}");


            if ($paymentResponse->failed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment verification failed with gateway',
                    'details' => $paymentResponse->json()
                ], 400);
            }

            $paymentData = $paymentResponse->json();

            // 6. Check Payment Status & Capture if needed
            if ($paymentData['status'] === 'authorized') {
                $captureResponse = Http::withoutVerifying()
                    ->withBasicAuth($key, $secret)
                    ->post("https://api.razorpay.com/v1/payments/{$request->razorpay_payment_id}/capture", [
                        'amount' => $paymentData['amount']
                    ]);
                
                if ($captureResponse->failed()) {
                    return response()->json(['success' => false, 'message' => 'Payment capture failed'], 400);
                }
                $paymentData = $captureResponse->json();
            }

            if ($paymentData['status'] !== 'captured') {
                return response()->json(['success' => false, 'message' => 'Payment not captured', 'status' => $paymentData['status']], 400);
            }

            $paidAmount = $paymentData['amount'] / 100;
            
            $payment = Payment::create([
                'booking_id' => $booking->id,
                'user_id' => $user->id,
                'transaction_id' => $paymentData['id'],
                'amount' => $paidAmount,
                'currency' => $paymentData['currency'],
                'status' => 'completed',
                'payment_method' => $paymentData['method'],
                'payment_date' => now(),
                'meta' => $paymentData
            ]);

            $booking->update([
                'status' => 'confirmed', 
                'approved_at' => now()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Payment verified and booking confirmed',
                'data' => [
                    'payment_id' => $payment->id,
                    'booking_id' => $booking->id,
                    'amount' => $payment->amount,
                    'status' => 'success'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Payment Verification Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server Error: ' . $e->getMessage()], 500);
        }
    }
}
