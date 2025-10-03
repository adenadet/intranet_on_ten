<?php
namespace App\Http\Traits\EService;

use App\Models\EMR\Appointment;
use App\Models\EMR\Patient;
use App\Models\EMR\Laboratory;
use App\Models\EMR\RadFinding;
use App\Models\EMR\Schedule;
use App\Models\EMR\Service;
use App\Models\Area;
use App\Models\State;
use App\Models\Country;
use App\Models\EMR\Consultation;
use App\Models\User;
use Illuminate\Support\Facades\DB;

trait AppointmentTrait{
    public function appointment_admin_summarized_report($type, $start_date, $end_date, $detailed){
        switch ($type){
            
            case "all":
                $query = Appointment::select(
                    'emr_appointments.date', DB::raw('count(emr_appointments.date) as total'),
                    DB::raw('SUM(CASE WHEN emr_consultations.decision = 6 THEN 1 ELSE 0 END) as x_ray'),
                    DB::raw('SUM(CASE WHEN emr_consultations.decision = 7 THEN 1 ELSE 0 END) as sputum'),
                    DB::raw('SUM(CASE WHEN emr_consultations.decision = 8 THEN 1 ELSE 0 END) as kid_under_11'),
                    DB::raw('SUM(CASE WHEN emr_consultations.decision = 10 THEN 1 ELSE 0 END) as postponed'),
                    DB::raw('SUM(CASE WHEN emr_appointments.doctor_at IS NULL THEN 1 ELSE 0 END) as missed')
                )
                ->leftJoin('emr_consultations', function($query){
                    $query->on('emr_appointments.id','=','emr_consultations.appointment_id')
                    ->whereRaw('emr_consultations.id IN (select MAX(a2.id) from emr_consultations as a2 join emr_appointments as u2 on u2.id = a2.appointment_id group by u2.id)');
                })
                ->where('status', '>=', 1)
                ->where('date', '>=', $start_date)
                ->where('date', '<=', $end_date)
                ->groupBy('date');
            break;
            case "started":
                $query = Appointment::select(
                    'emr_appointments.date', DB::raw('count(emr_appointments.date) as total'),
                    DB::raw('SUM(CASE WHEN emr_consultations.decision = 6 THEN 1 ELSE 0 END) as x_ray'),
                    DB::raw('SUM(CASE WHEN emr_consultations.decision = 7 THEN 1 ELSE 0 END) as sputum'),
                    DB::raw('SUM(CASE WHEN emr_consultations.decision = 8 THEN 1 ELSE 0 END) as kid_under_11'),
                    DB::raw('SUM(CASE WHEN emr_consultations.decision = 10 THEN 1 ELSE 0 END) as postponed'),
                    DB::raw('SUM(CASE WHEN emr_appointments.doctor_at IS NULL THEN 1 ELSE 0 END) as missed')
                )
                ->leftJoin('emr_consultations', function($query){
                    $query->on('emr_appointments.id','=','emr_consultations.appointment_id')
                    ->whereRaw('emr_consultations.id IN (select MAX(a2.id) from emr_consultations as a2 join emr_appointments as u2 on u2.id = a2.appointment_id group by u2.id)');
                })
                ->where('status', '>', 1)
                ->where('date', '>=', $start_date)
                ->where('date', '<=', $end_date)
                ->groupBy('date');
            break;
            case "pending":
            $reports = Appointment::select(DB::raw('max(emr_appointments.date) as date'),
                    DB::raw('count(emr_appointments.date) as total_no'),
                    DB::raw('sum(emr_payments.amount) as total_amount'),
                    DB::raw('SUM(CASE WHEN emr_payments.amount = 60000 THEN 1 ELSE 0 END) as no_adult'),
                    DB::raw('SUM(CASE WHEN emr_payments.amount = 30000 THEN 1 ELSE 0 END) as no_kids'),
                    DB::raw('SUM(CASE WHEN emr_payments.amount <> 30000 AND emr_payments.amount <> 60000 THEN 1 ELSE 0 END) as no_strange'),
                    DB::raw('SUM(CASE WHEN emr_payments.amount = 60000 THEN 60000 ELSE 0 END) as total_adult'),
                    DB::raw('SUM(CASE WHEN emr_payments.amount = 30000 THEN 30000 ELSE 0 END) as total_kids'),
                    DB::raw('SUM(CASE WHEN emr_payments.amount <> 30000 AND emr_payments.amount <> 60000 THEN emr_payments.amount ELSE 0 END) as total_strange'),
                )
                ->leftJoin('emr_payments', function($query){
                    $query->on('emr_appointments.id','=','emr_payments.appointment_id')
                    ->whereRaw('emr_payments.id IN (select MAX(a2.id) from emr_payments as a2 join emr_appointments as u2 on u2.id = a2.appointment_id group by u2.id)');
                })
                ->where('status', '=', 1)
                ->where('date', '>=', $start_date)
                ->where('date', '<=', $end_date)
                ->groupBy('date');
            break;
        }

        $query = $query->orderBy('date', 'ASC')->get();
    
        return $query;   
    }

    public function appointment_admin_summary_report($type, $start_date, $end_date, $detailed){
        $query = Appointment::whereDate('date', '>=', $start_date)->whereDate('date', '<=', $end_date);
        $appointments = $query->pluck('id');
        $consultations = Consultation::whereIn('appointment_id', $appointments);
        switch($type){
            case 'all':
                $query = $query->where('status', '>=', 1); 
            break;
            case 'child':
                $consultations = $consultations->where('decision', '=', 8)->pluck('appointment_id');
                $query = $query->whereIn('id', $consultations);
            break;  
            case 'missed':
                $query = $query->where('status', '=', 1); 
            break;
            case 'postponed':
                $consultations = $consultations->where('decision', '=', 10)->pluck('appointment_id');
                $query = $query->whereIn('id', $consultations);
            break;
            case 'sputum':  
                $consultations = $consultations->where('decision', '=', 7)->pluck('appointment_id');
                $query = $query->whereIn('id', $consultations);
            break;
            case 'started':
                $query = $query->where('status', '>', 4); 
            break;
            case 'xray':
                $consultations = $consultations->where('decision', '=', 6)->pluck('appointment_id');
                $query = $query->whereIn('id', $consultations);
            break;
        }

        $query = $detailed ? $query->get() : $query->count();

        return $query;
        
    }
    public function appointment_get_all($type, $page, $paginated, $sort_order){
        switch ($type){
            case 'admin':
                $query = Appointment::whereDate('date', '>=', date('Y-m-d'))->with(['service', 'patient', 'payment']);
            break;
            case null:
                $query = Appointment::whereDate('date', '>=', date('Y-m-d'))->with(['service', 'patient', 'payment']);
            break;
            case 'certificate':
                $query = Appointment::whereDate('date', '>=', date('Y-m-d', strtotime('-3 month')))->whereDate('date', '<=', date('Y-m-d'))->whereNotNull(['doctor_id', 'front_office_id',])->Where('status', '>=', 7)->with(['consent', 'consultation', 'front_officer', 'issuing_officer', 'patient', 'medical_officer', 'radiologist', 'referral', 'report.findings']);
            break;
            case 'consultation':
                $query = Appointment::whereDate('date', '=', date('Y-m-d'))->whereIn('status', [4, 5])->with(['service', 'patient', 'payment']);
            break;
            case 'missed':
                $query = Appointment::whereDate('date', '<=', date('Y-m-d'))->whereNull(['front_office_id',])->where('status', '=', 1)->with(['service', 'patient', 'payment']);
            break;
            case 'office':
                $query = Appointment::whereDate('date', '>=', date('Y-m-d'))->where('status', '>=', 1)->with(['service', 'patient', 'payment']);
            break;
            case 'pending':
                $query = Appointment::whereDate('date', '>=',date('Y-m-d', strtotime('-1 month')))->whereDate('date', '<=', date('Y-m-d'))->whereIn('status', [6, 7, 8])->with(['service', 'patient',]);
            break;
            case 'radiology':
                $query = Appointment::whereNull('radiologist_id')->Where('status', '=', 6)->with(['service', 'patient', 'front_officer', 'medical_officer']);
            break;
            case 'review':
                $query = Appointment::whereDate('date', '>=',date('Y-m-d', strtotime('-1 month')))->whereDate('date', '<=', date('Y-m-d'))->whereIn('status', [6, 7, 8, 9, 10])->with(['service', 'patient', 'front_officer', 'medical_officer', 'radiologist'])->orderBy('radiologist_at', 'DESC');
            break;
            case 'xray':
                $query = Appointment::where('status', '=', 6)->whereDate('date', '=', date('Y-m-d'))->where('status_end', '!=', 1)->with(['service', 'patient']);
            break;
        }
        
        if($paginated){return $query->orderBy('date', $sort_order)->orderBy('schedule', 'ASC')->paginate(50);}
        else{return $query->orderBy('date', $sort_order)->orderBy('schedule', 'ASC')->get();}
    }

    public function appointment_get_all_services(){
        return Service::orderBy('name', 'ASC')->get();
    }

    public function appointment_get_by_id($id, $type = NULL){
        $query = Appointment::where('id',$id);
        if(is_null($type)){
            return $query->with(['consultation', 'front_officer', 'medical_officer', 'radiologist', 'service', 'patient.nationality', 'payment.employee', 'report.findings'])->first();
        }
        else if($type == 'consultation'){
            return $query->with(['consent', 'consultation', 'front_officer', 'issuing_officer', 'laboratory.creator', 'medical_officer', 'patient.nationality', 'payment.employee', 'radiologist', 'report.findings', 'referral.creator', 'service',])->first();
        }
        else if($type == 'radiologist'){
            return $query->with(['consent', 'consultation', 'front_officer', 'medical_officer', 'service', 'patient.nationality', 'payment.employee', 'radiologist', 'report.findings'])->first();
        }
        else if($type == 'referral'){
            return $query->with(['front_officer', 'medical_officer', 'service', 'patient', 'referral.creator',])->first();
        }
        else if ($type == 'xray'){
            $query = Appointment::where('status', '=', 6)->whereDate('date', '=', date('Y-m-d'))->where('status_end', '!=', 1)->with(['service', 'patient', 'payment']);
        }
    }

    public function appointment_get_by_period($type, $start, $end, $detailed, $paginated){
        $query = Appointment::whereDate('date', '>=', $start)->whereDate('date', '<=', $end);
        switch ($type){
            case 'all':
                $query = $query->where('status', '>=', 5);
            break;
            case 'sputum':
                $sputum_tests = Laboratory::pluck('appointment_id');
                $query = $query->whereIn('id', $sputum_tests);
            break;
        }

        $query = $detailed ? $query->with(['consultation', 'laboratory', 'patient', 'referral.creator', 'radiologist', 'report.findings',]) : $query;

        $appointments = $paginated ? $query->paginate(52) : $query->get();

        return $appointments;
    }

    public function appointment_search($request, $type){
        $search = $request->input('patient');

        $patients = Patient::select('id')->orderBy('first_name', 'ASC')->where(function($query) use ($search){
            $query->where('first_name', 'LIKE', "%$search%")
            ->orWhere('middle_name', 'LIKE', "%$search%")
            ->orWhere('last_name', 'LIKE', "%$search%")
            ->orWhere('email', 'LIKE', "%$search%");
        })->get();

        $query = Appointment::whereIn('patient_id', $patients);
        if (!is_null($request->input('start_date'))){$query->whereDate('date', '>=', $request->input('start_date'));}
        if (!is_null($request->input('end_date'))){$query->whereDate('date', '<=', $request->input('end_date'));}
        $appointments = $query->with(['front_officer', 'medical_officer', 'radiologist','service', 'patient.nationality', 'payment.employee', 'consent', 'consultation', 'report.findings', 'issuing_officer'])->paginate(30);

        if(is_null($type)){
            return $query->with(['consultation', 'front_officer', 'medical_officer', 'radiologist', 'service', 'patient.nationality', 'payment.employee', 'report.findings'])->first();
        }
        else if($type == 'consultation'){
            return $query->with(['consent', 'consultation', 'front_officer', 'issuing_officer', 'medical_officer', 'patient.nationality', 'payment.employee', 'radiologist', 'report.findings', 'referral.creator', 'service',])->first();
        }
        else if($type == 'radiologist'){
            return $query->with(['certificate', 'consent', 'consultation', 'front_officer', 'medical_officer', 'service', 'patient.nationality', 'payment.employee', 'radiologist',])->first();
        }
        else if($type == 'referral'){
            return $query->with(['front_officer', 'medical_officer', 'service', 'patient', 'referral.creator',])->first();
        }
        else if ($type == 'xray'){
            $query = Appointment::where('status', '=', 6)->whereDate('date', '=', date('Y-m-d'))->where('status_end', '!=', 1)->with(['service', 'patient', 'payment']);
        }
    }

}