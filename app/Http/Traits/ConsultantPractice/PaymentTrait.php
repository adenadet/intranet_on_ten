<?php
namespace App\Http\Traits\ConsultantPractice;

use App\Models\ConsultantPractice\Account;
use App\Models\ConsultantPractice\Consultant;
use App\Models\ConsultantPractice\Payment;
use App\Models\ConsultantPractice\Session;
use App\Models\ConsultantPractice\SessionPayment;
use App\Models\ConsultantPractice\Specialty;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait PaymentTrait{
    use SettingsTrait;

    public function consultant_practice_payment_confirmation($id){
        try{
            $payment = Payment::where('id', '=', $id)->orWhere('unique_id', '=', $id)->withTrashed()->firstOrFail();

            if ($payment->status == Payment::StatusConfirmed){
                return "Payment has already been confirmed";
            }

            DB::transaction(function () use ($payment) {

            // Get unpaid sessions for this consultant
            $sessions = Session::where('consultant_id', $payment->consultant_id)
                ->where('status', '!=', 'paid')
                ->orderBy('id') // FIFO is usually safest
                ->lockForUpdate()
                ->get();

            foreach ($sessions as $session) {
                // Stop if payment money is exhausted
                if ($payment->balance <= 0) {
                    break;
                }

                $outstanding = $session->outstanding_amount;

                if ($outstanding <= 0) {
                    // Session already fully paid (edge case)
                    $session->update([
                        'status' => Session::StatusPaid,
                        'updated_by' => Auth::id() ?? auth('api')->id(),
                    ]);
                    continue;
                }

                // Amount to apply to this session
                $amountToPay = min($payment->balance, $outstanding);

                // Create session payment record
                SessionPayment::create([
                    'session_id' => $session->id,
                    'payment_id' => $payment->id,
                    'amount'     => $amountToPay,
                ]);

                // If fully settled, mark session as paid
                if (($outstanding - $amountToPay) == 0) {
                    $session->update([
                        'status' => Session::StatusPaid,
                        'updated_by' => Auth::id() ?? auth('api')->id(),
                    ]);}
                }
            });
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function consultant_practice_payment_confirm($id){
        DB::beginTransaction();

        try{
            $query = Payment::where('id', '=', $id)->orWhere('unique_id', '=', $id)->withTrashed()->firstOrFail();

            if ($query->status == Payment::StatusConfirmed){
                DB::rollback();
                return "Payment has already been confirmed";
            }
            $payment_balance = $query->balance;

            $query->status = Payment::StatusConfirmed;
            $query->updated_by = Auth::id() ?? auth('api')->id();

            $query->save();

            $unpaid_sessions = Session::where('status', '=', Session::StatusAwaitingPayment)->get();

            foreach ($unpaid_sessions as $session){
                //Get total payment made for this session;
                $total_payment = SessionPayment::where('session_id', '=', $session->id)->sum('amount');
                echo "\n";
                echo "Total Payment for the Session is: N".$total_payment;
                echo "\n";
                echo "Available Amount is N".$payment_balance;
                if ($total_payment < $session->amount){
                    $payable = $session->amount - $total_payment;
                    $paid = min($payment_balance, $payable);

                    $payment_session = SessionPayment::create([
                        'session_id' => $session->id,
                        'payment_id' => $id,
                        'amount' => $paid,
                    ]);

                    echo $payment_session->id;

                    if ($paid == $payable){
                        $session->status = Session::StatusPaid;
                        $session->save();
                    }



                }
                else{
                    $session->status = Session::StatusPaid;
                    $session->updated_by = auth('api')->id() ?? Auth::id();

                    $session->save();
                }
            }
        }
        catch(Exception $e){

        }
    }

    public function consultant_practice_payment_create($data){
        DB::beginTransaction();

        try{
            $query = Payment::create([
                'unique_id' => $this->consultant_practice_unique_id('session', 10),
                'consultant_id' => $data['consultant_id'],
                'account_id' => $data['account_id'],
                'amount' => $data['amount'], 
                'balance' => $data['balance'] ?? $data['amount'],
                'description' => $data['description'],
                'status' => Payment::StatusActive,
                'created_by' => Auth::id() ?? auth('api')->id(),
                'updated_by' => Auth::id() ?? auth('api')->id(),
            ]);

            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            return $e->getMessage();
        }
    }

    public function consultant_practice_payment_deactivate($id){
        DB::beginTransaction();

        try{
            $query = Payment::where('id', '=', $id)->orWhere('unique_id', '=', $id)->withTrashed()->firstOrFail();

            if ($query->status == Payment::StatusConfirmed){
                DB::rollback();
                return "Payment has already been confirmed";
            }

            if ($query->status == Payment::StatusActive){
                $query->status = Payment::StatusDeleted;
                $query->deleted_by = Auth::id() ?? auth('api')->id();
                $query->deleted_at = date('Y-m-d H:i:s');
            }
            else{
                $query->status = Payment::StatusActive;
                $query->deleted_by = null;
                $query->deleted_at = null;
            }

            $query->updated_by = Auth::id() ?? auth('api')->id();
            $query->save();

            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            return $e->getMessage();
        }
    }

    public function consultant_practice_payment_get_all($type, $specific, $detailed, $paginated){
        $query = Payment::query();

        switch($type){
            //case ''
        }

        if (is_array($specific)){

        }

        $query = $detailed ? $query->with(['creator', 'payments', 'pricelists', 'specialty', 'updater']) : $query->select('id', 'first_name', 'last_name', 'title', 'company_name');
        $query = $paginated ? $query->paginate(50) : $query->get();
        
        return $query;
    }

    public function consultant_practice_payment_get_by($id, $detailed){
        try{
            $query = Payment::where('id', '=', $id)->orWhere('unique_id', '=', $id);
            $query = $detailed ? $query->with(['account', 'confirmer', 'consultant', 'creator', 'session_payments', 'updater']) : $query->select('id', 'first_name', 'last_name', 'title', 'company_name');
            $query = $query->firstOrFail();
            return $query;
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function consultant_practice_payment_update($data, $id){
        DB::beginTransaction();

        try{
            $query = Payment::where('id', '=', $id)->orWhere('unique_id', '=', $id)->firstOrFail();

            if ($query->status == Payment::StatusConfirmed){
                DB::rollback();
                return "Payment has already been confirmed";
            }

            $query->consultant_id = $data['consultant_id'] ?? $query->consultant_id;
            $query->account_id = $data['account_id'] ?? $query->account_id;
            $query->amount = $data['amount'] ?? $query->amount;
            $query->balance = $data['balance'] ?? $data['amount'];
            $query->description = $data['description'] ?? $query->description;
            $query->status = $data['status'] ?? Payment::StatusActive;
            $query->updated_by = Auth::id() ?? auth('api')->id();
            
            $query->save();

            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            return $e->getMessage();
        }
    }

}