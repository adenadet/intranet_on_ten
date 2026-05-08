<?php

namespace App\Services\ConsultantPractice;

use App\Models\ConsultantPractice\Consultant;
use App\Models\ConsultantPractice\Session;
use App\Services\ConsultantPractice\CompanyService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ConsultantService
{
    public function create($data){
        if ($data['company_id'] == 'new'){
            $company_manager = new CompanyService();
            $company = $company_manager->create([
                'name' => $data['new_company_name'],
            ]);
            $company_id = $company->id;
        }
        else if (is_int($data['company_id'])){
            $company_id = $data['company_id'];
        }
        else if (is_string ($data['company_id'] )){
            throw ValidationException::withMessages(['message' => 'Invalid Company Id.']);
        }
        
        $consultant = Consultant::create([
            'title' => $data['title'] ?? null,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'company_id' => $company_id,
            'specialty_id' => $data['specialty_id'],
            'sex'=> $data['sex'] ?? null,
            'status' => $data['status'] ?? Consultant::StatusActive,
            'created_by' => auth('api')->id() ?? auth()->id(),
            'updated_by' => auth('api')->id() ?? auth()->id(),
        ]);
        return $consultant;
    }

    public function deactivate($consultant_id){
        $consultant = Consultant::find($consultant_id);

        if (!$consultant) {
            throw ValidationException::withMessages(['message' => 'Consultant not found.']);
        }
        else{
            $consultant->update([
                'status' => Consultant::StatusInactive,
                'updated_by' => auth('api')->id() ?? auth()->id(),
                'deleted_by' => auth('api')->id() ?? auth()->id(),
                'deleted_at' => now(),
            ]);
        }

        return $consultant;
    }

    public function reactivate($consultant_id){
        $consultant = Consultant::find($consultant_id);

        if (!$consultant) {
            throw ValidationException::withMessages(['message' => 'Consultant not found.']);
        }
        else{
            $consultant->update([
                'status' => Consultant::StatusActive,
                'updated_by' => auth('api')->id() ?? auth()->id(),
                'deleted_by' => null,
                'deleted_at' => null,
            ]);
        }

        return $consultant;
    }

    public function update($data, $consultant_id){
        $consultant = Consultant::find($consultant_id);

        if (!$consultant) {
            throw ValidationException::withMessages(['message' => 'Consultant not found.']);
        }
        else{

            if ($data['company_id'] == 'new'){
                $company_manager = new CompanyService();
                $company = $company_manager->create([
                    'name' => $data['company']['name'],
                ]);

                $company_id = $company->id;
            }
            else if (is_int($data['company_id'])){
                $company_id = $data['company_id'];
            }
            else if (is_string ($data['company_id'] )){
                throw ValidationException::withMessages(['message' => 'Invalid Company Id.']);
            }

            $consultant->update([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'company_id' => $company_id,
                'specialty_id' => $data['specialty_id'],
                'sex'=> $data['sex'],
                'status' => $data['status'] ?? Consultant::StatusActive,
                'title' => $data['title'],
                'updated_by' => auth('api')->id() ?? auth()->id(),
            ]);
        }

        return $consultant;
    }
}