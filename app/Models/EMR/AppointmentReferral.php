<?php

namespace App\Models\EMR;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Structure;

class AppointmentReferral extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'emr_appointment_referrals';
    protected $fillable = array('appointment_id', 'details', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function creator(){
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

}
