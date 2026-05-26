<?php

namespace App\Models\ConsultantPractice;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Structure
{
    use HasFactory;

    public const StatusActive = 1;
    public const StatusInactive = 0;

    protected $primaryKey = 'id';
    protected $table = 'consultant_practice_companies';

    protected $fillable = array('name', 'balance', 'address', 'email', 'phone', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function accounts(){
    	return $this->hasMany('App\Models\ConsultantPractice\Account', 'company_id', 'id');
	}

    public function consultants(){
    	return $this->hasMany('App\Models\ConsultantPractice\Consultant', 'company_id', 'id');
	}

    public function creator(){
    	return $this->belongsTo('App\Models\User', 'created_by', 'id');
	}

    public function deleter(){
        return $this->belongsTo('App\Models\User', 'deleted_by', 'id');
    }

    public function ledgers()
    {
        return $this->hasMany('App\Models\ConsultantPractice\CompanyLedger', 'company_id', 'id')->latest('id')->limit(20);
    }

    public function payments(){
    	return $this->hasMany('App\Models\ConsultantPractice\Payment', 'company_id', 'id');
	}

    public function updater(){
        return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }
}
