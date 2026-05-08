<?php

namespace App\Models\ConsultantPractice;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionConfirmation extends Structure
{
    use HasFactory;

    public const StatusConfirmed = 1;
    public const ServiceStatusCreated = 0;
    public const ServiceStatusRejectedConsultant = 100;
    public const ServiceStatusRejectedPatient = 200;
    public const PaymentStatusAwaitingPayment = 0;
    public const PaymentStatusPaid = 10;


    protected $primaryKey = 'id';
    protected $table = 'consultant_practice_session_confirmations';

    protected $fillable = array('session_id', 'decision', 'description', 'created_by', 'created_at', 'updated_at', 'deleted_at');

    public function session(){
        return $this->belongsTo('App\Models\ConsultantPractice\Session', 'session_id', 'id');
    }

    public function creator(){
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }
}
