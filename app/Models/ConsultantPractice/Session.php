<?php

namespace App\Models\ConsultantPractice;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Structure
{
    use HasFactory;

    public const StatusProcessing = 1;
    public const StatusCreated = 0;
    public const StatusAwaitingPayment = 5;
    public const StatusPaid = 10;
    public const StatusRejected = 200;

    protected $primaryKey = 'id';
    protected $table = 'consultant_practice_sessions';

    protected $fillable = array('unique_id', 'consultant_id', 'specialty_id', 'patient_id', 'amount', 'date', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function consultant(){
        return $this->belongsTo('App\Models\ConsultantPractice\Consultant', 'consultant_id', 'id');
    }

    public function patient(){
        return $this->belongsTo('App\Models\ConsultantPractice\Patient', 'patient_id', 'id');
    }

    public function session_payments(){
        return $this->hasMany('App\Models\ConsultantPractice\Sessionpayment', 'session_id', 'id');
    }

    public function specialty(){
        return $this->belongsTo('App\Models\ConsultantPractice\Specialty', 'specialty_id', 'id');
    }
}
