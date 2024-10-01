<?php
namespace App\Http\Traits\Escrows;

use App\Http\Traits\Escrow\PaymentTrait;
use App\Http\Traits\General\FileTrait;
use App\Http\Traits\Users\CustomerTrait;

use App\Models\Escrows\Transaction;
use App\Models\Escrows\TransactionMileStone;
use App\Models\Escrows\TransactionRequest;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

trait TransactionTrait {
    use CustomerTrait, FileTrait;

    public function transaction_create_transaction($data){
        //Check if the transaction is a buy or sell
        switch ($data['trans_type_id']){
            case 'buying':
                $buyer = Auth::user() ?? auth('api')->user();
                $vendor = !is_null($data['vendor_id']) ? User::find($data['vendor_id']) : $this->customer_create_temporary([
                    'name' => $data['partner_name'],
                    'email' => $data['partner_email'],
                    'phone' => $data['partner_phone'],
                ]);
            break;
            case 'selling':
                $vendor = Auth::user() ?? auth('api')->user();
                $buyer = !is_null($data['vendor_id']) ? User::find($data['vendor_id']) : $this->customer_create_temporary([
                    'name' => $data['partner_name'],
                    'email' => $data['partner_email'],
                    'phone' => $data['partner_phone'],
                ]);
            break;
            case 'middleman':
            break;
        }

        $image = !is_null($data['image']) ? $this->file_upload_by_type($data['image'], 'image', 'img/uploads/transaction', null) : null;
        //Check the item type and the expiration is within scope

        //Create the transaction
        $transaction = TransactionRequest::create([
            'purpose' => $data['item_name'],
            'trans_type_id' => $data['trans_type_id'],
            'item_type_id' => $data['item_type_id'],
            'amount' => $data['amount'],
            'buyer_id' => $buyer->id,
            'buyer_email' => $buyer->email,
            'buyer_phone' => $buyer->phone,
            'vendor_id' => $vendor->id,
            'vendor_email' => $vendor->email,
            'vendor_phone' => $vendor->phone,
            'description' => $data['description'],
            'image' => $image ?? NULL,
            'repeating' => false,
            'quantity' => $data['quantity'] ?? 1,
            'balance' => null,
            'status_id' => 1,
            'created_by' => Auth::id() ?? auth('api')->id(),
            'updated_by' => Auth::id() ?? auth('api')->id()
        ]);
        //If there are several milestone, create transaction milestones

        //Send a notification to the seller if initiated by buyer and vice versa

        return $transaction;
    }

    public function transaction_cancel_transaction($data, $id){
        //Check that the transaction exists

        //Inform the creator that the transaction has been cancelled
    }

    public function transaction_get_all_transactions($type, $specific, $detailed, $paginated, $page){}

    public function transaction_get_by_id($id){}

    public function transaction_request_create_new($data){
        DB::beginTransaction();

        try{
            $query = TransactionRequest::create([
                ''
            ]);
        }
        catch (Exception $e){

        }
        
        //if type="seller_new_item"
    }

    public function transaction_request_cancel($data){
        
    }

    public function transaction_request_deactivate($id){
        
    }

    public function transaction_request_get_all_requests($type, $specific, $detailed, $paginated, $page){
        
    }

    public function transaction_request_get_by_id($id){}

    public function transaction_request_update_request($data, $id){}



    public function transaction_milestone_create_new($data, $transaction_id){}
}