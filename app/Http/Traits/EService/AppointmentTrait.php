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
use App\Models\User;

trait AppointmentTrait{
    public function appointment_get_all($type, $page, $paginated, $sort_order){
        switch ($type){
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