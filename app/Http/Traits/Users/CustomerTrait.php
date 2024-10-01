<?php
namespace App\Http\Traits\Users;

use App\Http\Traits\General\FileTrait;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
trait CustomerTrait {
    use FileTrait;
    public function customer_create_temporary($data){
        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
        ]);
        Mail::send("", $data, function($message){});
    }

    public function customer_my_roles(){}

    public function customer_update_password($data, $id){}
}