<?php

namespace App\Models\ConsultantPractice;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionItem extends Structure
{
    use HasFactory;

    public const StatusConfirmed = 1;
    public const ServiceStatusCreated = 0;
    public const ServiceStatusRejectedConsultant = 100;
    public const ServiceStatusRejectedPatient = 200;
    public const PaymentStatusAwaitingPayment = 0;
    public const PaymentStatusPaid = 10;


    protected $primaryKey = 'id';
    protected $table = 'consultant_practice_session_items';

    protected $fillable = array('session_id', 'service_id', 'price', 'adjusted_price', 'created_at', 'updated_at', 'deleted_at');

    public function session(){
        return $this->belongsTo('App\Models\ConsultantPractice\Session', 'session_id', 'id');
    }

    public function service(){
        return $this->belongsTo('App\Models\ConsultantPractice\Service', 'service_id', 'id');
    }
}