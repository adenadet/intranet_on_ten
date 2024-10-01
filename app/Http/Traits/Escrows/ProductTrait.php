<?php
namespace App\Http\Traits\Escrows;

use App\Http\Traits\General\FileTrait;
use App\Models\Escrows\Product;
use App\Models\Escrows\ProductImage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
trait ProductTrait {
    use FileTrait;

    public function escrow_products_all($type, $specific, $detailed, $paginated, $page){
        switch ($type){
            case 'all':
                $query = Product::whereNull('deleted_at');
            break;
            case 'my_products':
                $query = Product::where('owner_id', '=', Auth::id() ?? auth('api')->id());
            break;
            default:
                $query = Product::whereNull('deleted_at');
        }

        $query = $detailed ? $query->with(['images', 'owner', 'transactions']) : $query->select('id', 'amount', 'item_code');
        $query = $paginated ? $query->paginate(12) : $query->get();

        return $query;
    }

    public function escrow_products_create($data){
        $product = Product::create([
            'owner_id' => $data['owner_id'] ?? (Auth::id() ?? auth('api')->id()),
            'item_code' => $data['item_code'],
            'description' => $data['description'],
            'details' => $data['details'],
            'unit_price' => $data['unit_price'],
            'status' => $data['status'] ?? 1,
            'quantity' => $data['quantity'] ?? NULL,
            'created_by' => Auth::id() ?? auth('api')->id(),
            'updated_by' => Auth::id() ?? auth('api')->id(),
        ]);

        return $product;
    }
    public function escrow_products_search($data){}

    public function escrow_products_transactions($data, $transaction_id){}

    public function escrow_products_get_by_id($id){
        return  Product::where('id', '=', $id)->with(['owner', 'transactions', 'images'])->first();
    }
}