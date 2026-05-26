<?php

namespace App\Services\ConsultantPractice;

use Illuminate\Support\Facades\DB;
use App\Models\ConsultantPractice\Payment;
use App\Models\ConsultantPractice\Company;
use Exception;
use Illuminate\Support\Facades\Auth;

class PaymentService
{
    public function __construct(
        protected CompanyLedgerService $company_ledger_service,
    ){}

    public function create(array $data): Payment
    {
        return Payment::create([
            'amount'     => $data['amount'],
            'status'     => Payment::StatusPending,
            'account_id' => $data['account_id'],
            'company_id' => $data['company_id'],
            'date' => $data['date'] ?? date('Y-m-d'),
            'description' => $data['description'] ?? null,
            'created_by' => auth('api')->id() ?? Auth::id(),
            'updated_by' => auth('api')->id() ?? Auth::id(),
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

    public function confirm(Payment $payment, array $data): Payment
    {
        if ($payment->status === Payment::StatusConfirmed) { return $payment; }
        if (($payment->status === Payment::StatusCancelled) || ($payment->status === Payment::StatusReversed)) {
            throw new Exception('Cancelled or reversed payments cannot be confirmed');
        }

        return DB::transaction(function () use ($payment, $data) {
            $company = $payment->company;
            $this->company_ledger_service->debit($company, $payment->amount, 'payment', $payment->id, $payment->description, $payment->date);

            $payment->update([
                'status'       => Payment::StatusConfirmed,
                'confirmed_by' => auth('api')->id() ?? Auth::id(),
                'confirmed_at' => now(),
                'confirmed_note' => $data['description'],
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);

            return $payment;
        });
    }

    public function reverse(Payment $payment, array $data): Payment
    {
        if ($payment->status !== Payment::StatusConfirmed) {
            throw new Exception('Only confirmed payments can be reversed');
        }

        return DB::transaction(function () use ($payment, $data) {
            $company = $payment->company;
            $this->company_ledger_service->credit($company, $payment->amount, 'payment', $payment->id, 'Payment reversal', date('Y-m-d'));
            $payment->update([
                'status' => Payment::StatusReversed,
                'reversed_by' => auth('api')->id() ?? Auth::id(),
                'reversed_at' => now(),
                'reversed_note' => $data['description'],
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);
            return $payment;
        });
    }

    public function cancel(Payment $payment): Payment
    {
        if ($payment->status !== Payment::StatusPending) {throw new Exception('Only pending payments can be cancelled');}
        $payment->update([
            'deleted_by' => auth('api')->id() ?? Auth::id(),
            'deleted_at' => now(),
            'status' => Payment::StatusCancelled,
            'updated_by' => auth('api')->id() ?? Auth::id(),
        ]);
        return $payment;
    }
}