<?php

namespace App\Models\ConsultantPractice;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consultant extends Structure
{
    use HasFactory;

    public const StatusActive = 1;
    public const StatusInactive = 0;

    protected $primaryKey = 'id';
    protected $table = 'consultant_practice_consultants';

    protected $fillable = array('unique_id', 'first_name', 'last_name', 'title', 'company_id', 'sex', 'specialty_id', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function accounts(){
    	return $this->hasMany('App\Models\ConsultantPractice\Account', 'consultant_id', 'id');
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

    public function payments(){
    	return $this->hasMany('App\Models\ConsultantPractice\Payment', 'consultant_id', 'id');
	}

    public function services(){
        return $this->hasMany('App\Models\ConsultantPractice\ConsultantService', 'consultant_id', 'id');
    }

    public function sessions(){
        return $this->hasMany('App\Models\ConsultantPractice\Session', 'consultant_id', 'id');
    }

    public function specialty(){
        return $this->belongsTo('App\Models\ConsultantPractice\Specialty', 'specialty_id', 'id');
    }

    public function updater(){
        return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }
}
