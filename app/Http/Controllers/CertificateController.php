<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\EMR\Appointment;
use App\Models\EMR\Referral;

class CertificateController extends Controller
{
    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        if (empty($_GET['p']) || empty($_GET['f'])){
            return view('auth.certificate');
        }
        else{
            //echo $id;
            $appointment = Appointment::where('id',$id)->with(['front_officer', 'medical_officer', 'radiologist','service', 'patient.nationality', 'payment.employee', 'consent', 'consultation', 'laboratory', 'report.findings', 'issuing_officer'])->first();
        
            if ((strtolower($appointment->patient->passport_no) == strtolower($_GET['p'])) && (strtolower($appointment->patient->first_name) == strtolower($_GET['f']))){
                $params = ['appointment' => $appointment];

                //print_r($params);
                if ($appointment->date > '2026-03-31'){
                    return view('certificates.horizontal-new')->with($params);
                }
                else{
                    return view('certificates.horizontal')->with($params);
                }    
            }
            else{
                return view('errors.404');
            }
        }
    }

    public function referral($id){
        $appointment = Appointment::with(['patient'])->findOrFail($id);
        $referral = Referral::with(['creator'])->where('appointment_id', '=', $id)->firstOrFail();
        $params = ['appointment' => $appointment, 'referral' => $referral];

        return view('certificates.referral')->with($params);
    }

    public function edit($id)
    {
        //
    }

    

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
