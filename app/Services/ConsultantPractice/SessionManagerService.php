<?php

namespace App\Services\ConsultantPractice;

use App\Models\ConsultantPractice\Session;
use App\Models\ConsultantPractice\SessionConfirmation;
use App\Models\ConsultantPractice\SessionItem;
use App\Models\ConsultantPractice\SessionPayment;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SessionManagerService
{
    public function create($data): Session
    {
        return DB::transaction(function () use ($data) {

            $session = Session::create([
                'amount'           => $data['amount'],
                'consultant_id'   => $data['consultant_id'],
                'date'            => $data['date'],
                'patient_id'      => $data['patient_id'],
                'service_status'  => Session::ServiceStatusCreated,
                'payment_status'  => Session::PaymentStatusAwaiting,
                'created_by'      => auth('api')->id() ?? Auth::id(),
                'updated_by'      => auth('api')->id() ?? Auth::id(),
            ]);

            foreach ($data['services'] as $service) {
                SessionItem::create([
                    'session_id'      => $session->id,
                    'service_id'      => $service['service_id'],
                    'price'           => $service['price'],
                    'adjusted_price'  => $service['adjusted_price'],
                ]);
            }

            return $session->load('session_items');
        });
    }

    public function delete($id): Session
    {
        return DB::transaction(function () use ($id) {

            $session = Session::findOrFail($id);

            $session->update([
                'updated_by' => auth('api')->id() ?? Auth::id(),
                'deleted_by' => auth('api')->id() ?? Auth::id(),
                'deleted_at' => now(),
            ]);
            
            return $session->load('session_items');
        });
    }

    public function payment_confirm(Session $session, array $data): Session
    {
        if ($session->payment_status === Session::PaymentStatusPaid) {
            return $session;
        }

        if ($session->service_status === Session::ServiceStatusRejected) {
            throw new Exception('Cannot pay for a rejected session');
        }

        return DB::transaction(function () use ($session, $data) {

            SessionPayment::create([
                'session_id'   => $session->id,
                'decision'     => $data['decision'],
                'description'  => $data['description'] ?? null,
                'created_by'   => auth('api')->id() ?? Auth::id(),
            ]);

            $session->update([
                'payment_status' => $data['decision'] == 'confirm' ? Session::PaymentStatusPaid : Session::PaymentStatusCancelled,
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);

            return $session;
        });
    }

    public function process(Session $session){
        if ($session->service_status != Session::ServiceStatusCompleted) {
            throw new Exception('Completed session cannot be processed');
        }

        if ($session->payment_status != Session::PaymentStatusPaid) {
            throw new Exception('Unpaid session cannot be processed');
        }

        return DB::transaction(function () use ($session) {
            $session->update([
                'finance_status' => Session::FinanceStatusProcessing,
            ]);

            return $session;
        });
    }

    public function reject(Session $session, array $data = []): Session
    {
        if ($session->service_status === Session::ServiceStatusCompleted) {
            throw new Exception('Completed session cannot be rejected');
        }

        return DB::transaction(function () use ($session, $data) {
            $session->update([
                'service_status' => Session::ServiceStatusRejected,
                'rejection_reason' => $data['rejection_reason'] ?? null,
            ]);

            return $session;
        });
    }

    public function service_confirm(Session $session, array $data = []): Session
    {
        if ($session->service_status === Session::ServiceStatusCompleted) {return $session;}
        if ($session->service_status === Session::ServiceStatusRejected) {throw new Exception('Rejected session cannot be confirmed');}

        return DB::transaction(function () use ($session, $data) {
            SessionConfirmation::create([
                'session_id'    => $session->id,
                'decision'      => $data['decision'],
                'description'   => $data['description'] ?? null,
                'created_by'    => auth('api')->id() ?? Auth::id(),
            ]);

            $session->update([
                'service_status' => $data['decision'] == 'confirm' ? Session::ServiceStatusCompleted : Session::ServiceStatusRejected,
                'updated_by' => auth('api')->id() ?? Auth::id(), 
            ]);

            $company = $session->consultant->company;

            $ledger = app(CompanyLedgerService::class)->credit(
                $company,
                $session->amount,
                'session',
                $session->id,
                'Session completed',
                date('Y-m-d'),
            );
            //echo $ledger->id; 

            return $session;
        });
    }


    public function update(Session $session, array $data): Session
    {
        if ($session->service_status === Session::ServiceStatusCompleted) {
            throw new Exception('Cannot update a completed session');
        }

        if ($session->service_status === Session::ServiceStatusRejected) {
            throw new Exception('Cannot update a rejected session');
        }

        return DB::transaction(function () use ($session, $data) {

            $session->update([
                'patient_id'     => $data['patient_id'],
                'consultant_id'  => $data['consultant_id'],
                'date'           => $data['date'],
                'total'          => $data['total'],
            ]);

            SessionItem::where('session_id', $session->id)->delete();

            foreach ($data['services'] as $service) {
                SessionItem::create([
                    'session_id'      => $session->id,
                    'service_id'      => $service['service_id'],
                    'price'  => $service['price'],
                    'adjusted_price' => $service['adjusted_price'],
                ]);
            }

            return $session->load('items');
        });
    }
}