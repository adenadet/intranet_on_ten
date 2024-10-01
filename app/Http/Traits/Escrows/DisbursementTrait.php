<?php
namespace App\Http\Traits\Escrows;

use App\Http\Traits\General\FileTrait;
use Illuminate\Support\Facades\Mail;
trait DisbursementTrait {
    use FileTrait;

    public function escrow_disbursement_create_new($data){
        $query = [];
        //Create a new Disbursement

        //Send an email/sms to the customers

        //Send a real notification

        //Log the transaction
        return $query;
    }

    public function escrow_disbursement_get_all($type, $specific, $detailed, $paginated, $page){}

    public function escrow_disbursement_get_by_id($id){}
}