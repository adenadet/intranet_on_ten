<?php
namespace App\Http\Traits\Users;

use App\Http\Traits\General\FileTrait;
use Illuminate\Support\Facades\Mail;
trait ProfileTrait {
    use FileTrait;
    public function profile_my_details(){}

    public function profile_my_roles(){}

    public function profile_update_password($data, $id){}
}