<?php

namespace App\Models\ConsultantPractice;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Structure
{
    use HasFactory;

    public const StatusActive = 1;
    public const StatusInactive = 0;

    protected $primaryKey = 'id';
    protected $table = 'consultant_practice_patients';

    protected $fillable = array('unique_id', 'name', 'patient_type', 'sex', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function creator(){
    	return $this->belongsTo('App\Models\User', 'created_by', 'id');
	}

    public function deleter(){
        return $this->belongsTo('App\Models\User', 'deleted_by', 'id');
    }

    public function updater(){
        return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }
}
