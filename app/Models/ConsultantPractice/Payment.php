<?php

namespace App\Models\ConsultantPractice;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Structure
{
    use HasFactory;
    
    public const StatusPending = 1;
    public const StatusConfirmed = 10;
    public const StatusCancelled = 100;
    public const StatusReversed = 200;
    
    protected $primaryKey = 'id';
    protected $table = 'consultant_practice_payments';

    protected $fillable = array('company_id', 'account_id', 'reference', 'amount', 'notes', 'confirmed_by', 'confirmed_at','reversed_by', 'reversed_at', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function account(){
        return $this->belongsTo('App\Models\ConsultantPractice\Account', 'account_id', 'id');
    }

    public function confirmer(){
        return $this->belongsTo('App\Models\User', 'confirmed_by', 'id');
    }

    public function company(){
        return $this->belongsTo('App\Models\ConsultantPractice\Company', 'company_id', 'id');
    }

    public function creator(){
    	return $this->belongsTo('App\Models\User', 'created_by', 'id');
	}

    public function deleter(){
        return $this->belongsTo('App\Models\User', 'deleted_by', 'id');
    }

    public function reverser(){
        return $this->belongsTo('App\Models\User', 'reversed_by', 'id');
    }

    public function updater(){
        return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }
}
