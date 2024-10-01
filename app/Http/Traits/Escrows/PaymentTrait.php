<?php
namespace App\Http\Traits\Escrows;

use App\Http\Traits\General\FileTrait;
use Illuminate\Support\Facades\Mail;
trait PaymentTrait {
    use FileTrait;

    public function escrow_payments_all($type, $specific, $detailed, $paginated, $page){}

    public function escrow_payments_search($data){}

    public function escrow_payments_transaction_payment($data, $transaction_id){}

    public function escrow_payments_get_by_id($id){}
}