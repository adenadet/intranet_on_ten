<?php

namespace App\Models\ConsultantPractice;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Structure
{
    use HasFactory;

    public const FinanceStatusPending = 0;
    public const FinanceStatusProcessing = 10;
    public const FinanceStatusCompleted = 20;

    public const PaymentStatusAwaiting = 0;
    public const PaymentStatusPaid = 10;
    public const PaymentStatusCancelled = 20;

    public const ServiceStatusCompleted = 1;
    public const ServiceStatusCreated = 0;
    public const ServiceStatusRejectedConsultant = 100;
    public const ServiceStatusRejected = 200;
    
    protected $primaryKey = 'id';
    protected $table = 'consultant_practice_sessions';

    protected $fillable = array('unique_id', 'consultant_id', 'specialty_id', 'patient_id', 'amount', 'date', 'service_status', 'payment_status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function consultant(){
        return $this->belongsTo('App\Models\ConsultantPractice\Consultant', 'consultant_id', 'id');
    }

    public function creator(){
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function deleter(){
        return $this->belongsTo('App\Models\User', 'deleted_by', 'id');
    }

    public function patient(){
        return $this->belongsTo('App\Models\ConsultantPractice\Patient', 'patient_id', 'id');
    }

    public function session_confirm(){
        return $this->belongsTo('App\Models\ConsultantPractice\SessionConfirmation', 'session_id', 'id');
    }

    public function session_items(){
        return $this->hasMany('App\Models\ConsultantPractice\SessionItem', 'session_id', 'id');
    }

    public function session_payment(){
        return $this->belongsTo('App\Models\ConsultantPractice\SessionPayment', 'session_id', 'id');
    }

    public function specialty(){
        return $this->belongsTo('App\Models\ConsultantPractice\Specialty', 'specialty_id', 'id');
    }

    public function updater(){
        return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }
}
