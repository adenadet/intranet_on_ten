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
    protected $table = 'consultant_practice_ledgers';

    protected $fillable = array('consultant_id', 'balance', 'previous_balance', 'title', 'company_name', 'specialty_id', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function accounts(){
    	return $this->hasMany('App\Models\ConsultantPractice\Account', 'consultant_id', 'id');
	}

}