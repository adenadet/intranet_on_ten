<?php

namespace App\Services\ConsultantPractice;

use App\Models\ConsultantPractice\Patient;
use App\Models\ConsultantPractice\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PatientManagerService
{
    public function create($data)
    {
        $patient = Patient::create([
            'unique_id' => $data['unique_id'],
            'name' => $data['name'],
            'patient_type' => $data['patient_type'],
            'sex' => $data['sex'],
            'status' => Patient::StatusActive,
            'created_by' => auth('api')->id() ?? Auth::id(),
            'updated_by' => auth('api')->id() ?? Auth::id(),
        ]);

        return $patient;
    }

    public function update($data, $patient_id)
    {
        $patient = Patient::find($patient_id);

        if (!$patient) {
            throw ValidationException::withMessages(['message' => 'Patient not found.']);
        }
        else{
            $patient->update([
                'unique_id' => $data['unique_id'],
                'name' => $data['name'],
                'patient_type' => $data['patient_type'],
                'sex' => $data['sex'],
                'status' => $data['status'] ?? Patient::StatusInactive,
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);
        }

        return $patient;
    }

    public function delete(int|string $patient_id)
    {
        $patient = Patient::withTrashed()->find($patient_id);

        if (!$patient) {
            throw ValidationException::withMessages(['message' => 'Patient not found.']);
        }

        if ($patient->status == Patient::StatusActive){
            $patient->update([
                'status' => Patient::StatusInactive,
                'updated_by' => auth('api')->id() ?? Auth::id(),
                'deleted_by' => auth('api')->id() ?? Auth::id(),
                'deleted_at' => now(),
            ]);
        }
        else{
            $patient->update([
                'status' => Patient::StatusActive,
                'updated_by' => auth('api')->id() ?? Auth::id(),
                'deleted_by' => null,
                'deleted_at' => null,
            ]);
        }

        return $patient;
    }
}