<?php

namespace App\Http\Controllers\Api\EMR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\EMR\Appointment;
use App\Models\EMR\Patient;
use App\Models\EMR\Service;
use App\Models\Area;
use App\Models\State;
use App\Models\Country;
use App\Models\User;
use Intervention\Image\Facades\Image;



class PatientController extends Controller
{
    public function index()
    {
        return response()->json([
            'areas' => Area::select('id', 'name')->where('state_id', 25)->orderBy('name', 'ASC')->get(),
            'nations' => Country::orderBy('name', 'ASC')->get(), 
            'applicants' => Patient::orderBy('created_at', 'DESC')->with('nationality')->paginate(50),     
        ]);
    }

    public function initials()
    {
        return response()->json([
            'areas' => Area::select('id', 'name')->where('state_id', 25)->orderBy('name', 'ASC')->get(),
            'nations' => Country::orderBy('name', 'ASC')->get(), 
            'applicants' => Patient::orderBy('created_at', 'DESC')->with('nationality')->paginate(50),     
        ]);
    }

    public function store(Request $request)
    {

        $image_url = $currentPhoto = null;
        $passport_image_url = $currentPassportPhoto = null;
        $destination = $_SERVER['DOCUMENT_ROOT'].'/img/applicants';
        if (($request['image'] != $currentPhoto) && ($request['image'] != '')){
            $image = $request['id']."-".time().".".explode('/',explode(':', substr( $request['image'], 0, strpos($request['image'], ';')))[1])[1];
            Image::make($request['image'])->save($destination.'/'.$image);
            $image_url = $image;
            $old_image = $destination.'/'.$currentPhoto;

            if (file_exists($old_image)){ @unlink($old_image); }
        }

        if (($request['passport_image'] != $currentPhoto) && ($request['passport_image'] != '')){
            $image = $request['id']."-".time().".".explode('/',explode(':', substr( $request['passport_image'], 0, strpos($request['passport_image'], ';')))[1])[1];
            Image::make($request['passport_image'])->save(public_path('img/passports/').$image);
            //$passport_image_url = $passport_image;
            $old_image = public_path('img/passports/').$currentPassportPhoto;

            if (file_exists($old_image)){ @unlink($old_image); }
        }
        
        Patient::create([
            'last_name'     => $request->input('last_name'),
            'first_name'    => $request->input('first_name'),
            'middle_name'   => $request->input('middle_name'),
            'dob' => $request->input('dob'),
            'sex' => $request->input('sex'),
            'image' => $image_url,
            'passport_page' => $passport_image_url,
            'lmp' => $request->input('lmp'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'alt_phone' => $request->input('alt_phone'),
            'nigerian_address' => $request->input('nigerian_address'),
            'nigerian_address_street' =>  $request->input('nigerian_address_street'),
            'nigerian_address_street2' => $request->input('nigerian_address_street2'),
            'nigerian_address_city' => $request->input('nigerian_address_city'),
            'nigerian_address_country' => $request->input('nigerian_address_country'), 
            'uk_address' => $request->input('uk_address'),
            'uk_address_street' =>$request->input('uk_address_street'),
            'uk_address_street2' => $request->input('uk_address_street2'),
            'uk_address_city' =>$request->input('uk_address_city'),
            'uk_address_postcode' => $request->input('uk_address_postcode'),
                
            'accompanying_kids' => $request->input('accompanying_kids'),
            'nationality_id' => $request->input('nationality_id'),
            'passport_no' => $request->input('passport_no'),
            'visa_type' => $request->input('visa_type'),
            'created_by' => auth('api')->id(),
        ]);

        return response()->json([
            'areas' => Area::select('id', 'name')->where('state_id', 25)->orderBy('name', 'ASC')->get(),
            'nations' => Country::orderBy('name', 'ASC')->get(), 
            'applicants' => Patient::orderBy('created_at', 'DESC')->with('nationality')->paginate(50),     
        ]);
    }

    public function show($id)
    {
        return response()->json([
            //'patient' => $this->emr_     
        ]);
    }

    public function update(Request $request, $id)
    {
        $patient = Patient::find($id);

        $image_url = $currentPhoto = $patient->image;
        $passport_image_url = $currentPassportPhoto = $patient->passport_image;

        $destination = $_SERVER['DOCUMENT_ROOT'].'/img/applicants';
        if (($request['image'] != $currentPhoto) && ($request['image'] != '')){
            $image = $request['id']."-".time().".".explode('/',explode(':', substr( $request['image'], 0, strpos($request['image'], ';')))[1])[1];
            Image::make($request['image'])->save($destination.'/'.$image);
            $image_url = $image;
            $old_image = $destination.'/'.$currentPhoto;

            if (file_exists($old_image)){ @unlink($old_image); }
        }

        $patient->last_name     = $request->input('last_name');
        $patient->first_name    = $request->input('first_name');
        $patient->middle_name   = $request->input('middle_name');
        $patient->dob = $request->input('dob');
        $patient->sex = $request->input('sex');
        $patient->image = $image_url;
        $patient->passport_page = $passport_image_url;
        $patient->lmp = $request->input('lmp');
        $patient->email = $request->input('email');
        $patient->phone = $request->input('phone');
        $patient->alt_phone = $request->input('alt_phone');
        $patient->nigerian_address = null;
        $patient->nigerian_address_street = $request->input('nigerian_address_street');
        $patient->nigerian_address_street2 = $request->input('nigerian_address_street2');
        $patient->nigerian_address_city = $request->input('nigerian_address_city');
        $patient->nigerian_address_country = $request->input('nigerian_address_country');
        $patient->uk_address = null;
        $patient->uk_address_street = $request->input('uk_address_street');
        $patient->uk_address_street2 = $request->input('uk_address_street2');
        $patient->uk_address_city = $request->input('uk_address_city');
        $patient->uk_address_postcode = $request->input('uk_address_postcode');
        $patient->accompanying_kids = $request->input('accompanying_kids');
        $patient->nationality_id = $request->input('nationality_id');
        $patient->passport_no = $request->input('passport_no');
        $patient->visa_type = $request->input('visa_type');
        
        $patient->save();

        return response()->json([
            'areas' => Area::select('id', 'name')->where('state_id', 25)->orderBy('name', 'ASC')->get(),
            'nations' => Country::orderBy('name', 'ASC')->get(), 
            'applicants' => Patient::orderBy('created_at', 'DESC')->with('nationality')->paginate(50),        
        ]);
    }

    public function destroy($id)
    {
        

    }

    public function search()
    {
        if ($search = $_GET['q']){
            $applicants = Patient::orderBy('first_name', 'ASC')->where(function($query) use ($search){
                $query->where('first_name', 'LIKE', "%$search%")
                ->orWhere('middle_name', 'LIKE', "%$search%")
                ->orWhere('last_name', 'LIKE', "%$search%")
                ->orWhere('email', 'LIKE', "%$search%");
                })->paginate(100);
            }
        else{
            $applicants = Patient::orderBy('first_name', 'ASC')->paginate(100);
        }
        
        return response()->json(['applicants' => $applicants,]);
    }
}