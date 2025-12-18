<?php

namespace App\Models\ConsultantPractice;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Structure
{
    use HasFactory;
    
    public const StatusActive = 1;
    public const StatusDeleted = 0;
    public const StatusConfirmed = 10;

    protected $primaryKey = 'id';
    protected $table = 'consultant_practice_payments';

    protected $fillable = array('unique_id', 'consultant_id', 'account_id', 'amount', 'balance', 'confirmed_by', 'confirmed_at', 'status', 'description', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function account(){
        return $this->belongsTo('App\Models\ConsultantPractice\Account', 'account_id', 'id');
    }

    public function confirmer(){
        return $this->belongsTo('App\Models\User', 'confirmed_by', 'id');
    }

    public function consultants(){
        return $this->hasMany('App\Models\ConsultantPractice\Consultant', 'specialty_id', 'id');
    }

    public function creator(){
    	return $this->belongsTo('App\Models\User', 'created_by', 'id');
	}

    public function deleter(){
        return $this->belongsTo('App\Models\User', 'deleted_by', 'id');
    }

    public function session_payments(){
        return $this->hasMany('App\Models\ConsultantPractice\Sessionpayment', 'payment_id', 'id');
    }

    public function updater(){
        return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }
}
