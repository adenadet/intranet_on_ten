<?php

namespace App\Services\ConsultantPractice;

use Illuminate\Support\Facades\DB;
use App\Models\ConsultantPractice\Payment;
use App\Models\ConsultantPractice\Company;
use Exception;

class PaymentService
{
    public function create(array $data): Payment
    {
        return Payment::create([
            'company_id' => $data['company_id'],
            'amount'     => $data['amount'],
            'status'     => Payment::StatusPending,
            'reference'  => $data['reference'] ?? null,
            'notes'      => $data['notes'] ?? null,
            'created_by' => auth()->id()
        ]);
    }

    public function update(Payment $payment, array $data): Payment
    {
        if ($payment->status !== Payment::StatusPending) {
            throw new Exception('Only pending payments can be updated');
        }

        $payment->update($data);

        return $payment;
    }

    public function confirm(Payment $payment): Payment
    {
        if ($payment->status === Payment::StatusConfirmed) {
            return $payment;
        }
        if (($payment->status === Payment::StatusCancelled) || ($payment->status === Payment::StatusReversed)) {
            throw new Exception('Cancelled or reversed payments cannot be confirmed');
        }

        return DB::transaction(function () use ($payment) {

            $company = $payment->company;

            app(CompanyLedgerService::class)->debit(
                $company,
                $payment->amount,
                'payment',
                $payment->id,
                'Company payout'
            );

            $payment->update([
                'status'       => Payment::StatusConfirmed,
                'confirmed_by' => auth()->id(),
                'confirmed_at' => now()
            ]);

            return $payment;
        });
    }

    public function reverse(Payment $payment): Payment
    {
        if ($payment->status !== Payment::StatusConfirmed) {
            throw new Exception('Only confirmed payments can be reversed');
        }

        return DB::transaction(function () use ($payment) {

            $company = $payment->company;

            app(CompanyLedgerService::class)->credit(
                $company,
                $payment->amount,
                'payment_reverse',
                $payment->id,
                'Payment reversal'
            );

            $payment->update([
                'status' => Payment::StatusReversed
            ]);

            return $payment;
        });
    }

    public function cancel(Payment $payment): Payment
    {
        if ($payment->status !== Payment::StatusPending) {
            throw new Exception('Only pending payments can be cancelled');
        }

        $payment->update([
            'status' => Payment::StatusCancelled
        ]);

        return $payment;
    }
}