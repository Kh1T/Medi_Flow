<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use KHQR\BakongKHQR;
use KHQR\Helpers\KHQRData;
use KHQR\Models\IndividualInfo;

class CheckoutController extends Controller
{
    /**
     * Pay invoice by cash
     */
    public function payCash(Billing $billing)
    {
        $billing->update([
            'status' => 'paid',
            'paid_amount' => $billing->total,
            'payment_method' => 'cash',
        ]);

        return redirect()
            ->route('billing.index')
            ->with('success', 'Payment by cash completed successfully.');
    }

    /**
     * Show KHQR page
     */
    public function showKhqr(Billing $billing)
    {
        try {
            $merchant = new IndividualInfo(
                bakongAccountID: 'sem_bunly@bkrt',
                merchantName: 'BUNLY SEM',
                merchantCity: 'Phnom Penh',
                currency: KHQRData::CURRENCY_USD,
                amount: $billing->total
            );

            $qrResponse = BakongKHQR::generateIndividual($merchant);

            $qr = $qrResponse->data['qr'] ?? null;
            $md5 = $qrResponse->data['md5'] ?? null;

            return view('checkout.khqr', compact('billing', 'qr', 'md5'));
        } catch (\Exception $e) {
            Log::error('KHQR generate error: ' . $e->getMessage());

            return redirect()
                ->route('billing.index')
                ->with('error', 'Unable to generate KHQR payment.');
        }
    }

    /**
     * Verify Bakong transaction using md5
     */
    public function verifyTransaction(Request $request)
    {
        $request->validate([
            'md5' => 'required|string',
            'billing_id' => 'required|exists:invoices,id',
        ]);

        try {
            $billing = Billing::findOrFail($request->billing_id);

            if ($billing->status === 'paid') {
                return response()->json([
                    'success' => true,
                    'paid' => true,
                    'message' => 'Invoice already paid.',
                    'redirect' => route('checkout.success', $billing->id),
                ]);
            }

            $token = env('BAKONG_TOKEN');

            if (! $token) {
                return response()->json([
                    'success' => false,
                    'paid' => false,
                    'message' => 'Bakong token not configured.',
                ], 500);
            }

            $bakong = new BakongKHQR($token);
            $result = $bakong->checkTransactionByMD5($request->md5);

            $paid = false;

            if (
                (isset($result['responseCode']) && (int) $result['responseCode'] === 0) ||
                (isset($result['status']) && in_array(strtolower($result['status']), ['success', 'paid', 'completed'])) ||
                (isset($result['data']['status']) && in_array(strtolower($result['data']['status']), ['success', 'paid', 'completed']))
            ) {
                $paid = true;
            }

            if ($paid) {
                $billing->update([
                    'status' => 'paid',
                    'paid_amount' => $billing->total,
                    'payment_method' => 'khqr',
                ]);

                return response()->json([
                    'success' => true,
                    'paid' => true,
                    'message' => 'KHQR payment confirmed successfully.',
                    'redirect' => route('checkout.success', $billing->id),
                    'bakong_response' => $result,
                ]);
            }

            return response()->json([
                'success' => true,
                'paid' => false,
                'message' => 'Payment not completed yet.',
                'bakong_response' => $result,
            ]);
        } catch (\Exception $e) {
            Log::error('Bakong verify error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'paid' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Success page after payment completed
     */
    public function success(Billing $billing)
    {
        return view('checkout.success', compact('billing'));
    }
}