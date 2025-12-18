<?php
namespace App\Http\Traits\ConsultantPractice;

use App\Models\ConsultantPractice\Consultant;
use App\Models\ConsultantPractice\Payment;
use App\Models\ConsultantPractice\Session;


trait SettingsTrait{

    public function generateRandomString($length = 10){
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function consultant_practice_unique_id($type, $length = 10){
        $code = $this->generateRandomString($length);
        switch($type){
            case 'consultant':
                $prefix = 'CON-';
                $query = Consultant::where('unique_id', '=', $prefix.'-'.$code)->first();
                if($query){
                    return $this->consultant_practice_unique_id('consultant', 10);
                }
                else{
                    return $prefix.'-'.$code;
                }
            case 'payment':
                $prefix = 'PYT-';
                $query = Payment::where('unique_id', '=', $prefix.'-'.$code)->first();
                if($query){
                    return $this->consultant_practice_unique_id('payment', 10);
                }
                else{
                    return $prefix.'-'.$code;
                }
            case 'session':
                $prefix = 'SES-';
                $query = Session::where('unique_id', '=', $prefix.'-'.$code)->first();
                if($query){
                    return $this->consultant_practice_unique_id('session', 10);
                }
                else{
                    return $prefix.'-'.$code;
                }
        }
    }
}