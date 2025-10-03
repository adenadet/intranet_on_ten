<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\EMR\Appointment;
use App\Models\EMR\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaystackController extends Controller
{
    public function handle(Request $request)
    {
        // 1. Verify source ---------------------------------------------------
        $payload      = $request->getContent();           // raw body
        $theirHash    = $request->header('x-paystack-signature');
        $ourHash      = hash_hmac('sha512', $payload, env('PAYSTACK_SECRET_KEY'));

        if (! hash_equals($ourHash, $theirHash)) {
            //Log::warning('Paystack webhook: signature mismatch');
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        // 2. Parse and filter -----------------------------------------------
        $event = $request->input('event');
        $data  = $request->input('data');

        if ($event !== 'charge.success') {
            return response()->json(['message' => 'Event ignored']);
        }

        // 3. Double-check via Verify endpoint (optional but recommended) ----
        $verify = Http::withToken(env('PAYSTACK_SECRET_KEY'))->get("https://api.paystack.co/transaction/verify/{$data['reference']}");

        if ($verify->failed() || $verify['data']['status'] !== 'success') {
            Log::warning('Paystack verification failed', $verify->json());
            return response()->json(['message' => 'Verification failed'], 400);
        }

        // 4. Idempotent upsert into DB --------------------------------------
        Payment::updateOrCreate(
            ['reference' => $data['reference']],
            [
                'amount'       => $data['amount'] / 100,  // kobo → naira
                'status'       => 'success',
                'paid_at'      => $data['paid_at'],
                'customer_id'  => $data['customer']['id'],
                'raw_payload'  => $request->all(),        // optional JSON column
            ]
        );

        // 5. Idempotent upsert into Appointment -----------------------------
        Appointment::updateOrCreate(
            ['reference' => $data['reference']],
            [
                'amount'       => $data['amount'] / 100,  // kobo → naira
                'status'       => 'success',
                'paid_at'      => $data['paid_at'],
                'customer_id'  => $data['customer']['id'],
                'raw_payload'  => $request->all(),        // optional JSON column
            ]
        );

        // 5. Trigger internal business logic (queues, events, mail…) --------
        // e.g. event(new PaymentConfirmed($transaction));

        return response()->json(['status' => 'ok']);
    }
}
