<?php

namespace App\Services\ConsultantPractice;

use App\Models\ConsultantPractice\Company;
use App\Models\ConsultantPractice\Consultant;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CompanyService
{
    public function create($data){
        $query = Company::create([
            'name' => $data['name'],
            'balance' => 0.00,
            'address' => $data['address'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'status' => $data['phone'] ?? Company::StatusActive,
            'created_by' => auth('api')->id() ?? auth()->id(),
            'updated_by' => auth('api')->id() ?? auth()->id(),
        ]);

        return $query;
    }

    public function update($data, $company_id){
        $company = Company::find($company_id);

        if (!$company) {
            throw ValidationException::withMessages(['message' => 'Company not found.']);
        }
        else{
            $company->update([
                'name' => $data['name'],
                'address' => $data['address'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'status' => $data['status'],
                'updated_by' => auth('api')->id() ?? auth()->id(),
            ]);
        }

        return $company;
    }

    public function deactivate($company_id){
        $company = Company::find($company_id);

        if (!$company) {
            throw ValidationException::withMessages(['message' => 'Company not found.']);
        }
        else{
            $company->update([
                'status' => Company::StatusInactive,
                'updated_by' => auth('api')->id() ?? auth()->id(),
                'deleted_by' => auth('api')->id() ?? auth()->id(),
                'deleted_at' => now(),
            ]);

            $consultants = $company->consultants()->where('status', Consultant::StatusActive)->get();
            foreach ($consultants as $consultant) {
                $consultant->update([
                    'status' => Consultant::StatusInactive,
                    'updated_by' => auth('api')->id() ?? auth()->id(),
                    'deleted_by' => auth('api')->id() ?? auth()->id(),
                    'deleted_at' => now(),
                ]);
            }
        }

        return $company;
    }

    public function reactivate($company_id){
        $company = Company::find($company_id);

        if (!$company) {
            throw ValidationException::withMessages(['message' => 'Company not found.']);
        }
        else{
            $company->update([
                'status' => Company::StatusActive,
                'updated_by' => auth('api')->id() ?? auth()->id(),
                'deleted_by' => null,
                'deleted_at' => null,
            ]);

            $consultants = $company->consultants()->where('status', Consultant::StatusInactive)->get();
            foreach ($consultants as $consultant) {
                $consultant->update([
                    'status' => Consultant::StatusActive,
                    'updated_by' => auth('api')->id() ?? auth()->id(),
                    'deleted_by' => null,
                    'deleted_at' => null,
                ]);
            }
        }

        return $company;
    }
}