<?php
namespace App\Services\ConsultantPractice;

use App\Models\ConsultantPractice\Service;
use App\Models\ConsultantPractice\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ServiceManagerService
{
    public function create($data){
        return DB::transaction(function () use ($data) {

            //$this->validateCreation($data);

            $service = Service::create([
                'name'          => $data['name'],
                'description'   => $data['description'] ?? null,
                'status'        => Service::StatusActive,
                'created_by'    => auth('api')->id() ?? Auth::id(),
                'updated_by'    => auth('api')->id() ?? Auth::id(),
            ]);

            return $service;
        });
    }

    public function update($data, $service_id){
        return DB::transaction(function () use ($data, $service_id) {

            //$this->validateUpdate($data, $service_id);

            $service = Service::find($service_id);

            if (!$service) {
                throw ValidationException::withMessages(['message' => 'Service not found.']);
            }
            else{
                $service->update([
                    'name'          => $data['name'],
                    'description'   => $data['description'] ?? null,
                    'status'        => $data['status'],
                    'updated_by'    => auth('api')->id() ?? Auth::id(),
                ]);
            }

            return $service;
        });
    }

    public function delete($service_id){
        return DB::transaction(function () use ($service_id) {

            $service = Service::find($service_id);

            if (!$service) {
                throw ValidationException::withMessages(['message' => 'Service not found.']);
            }

            $service->update([
                'status' => Service::StatusInactive,
                'updated_by' => auth('api')->id() ?? Auth::id(),
                'deleted_by' => auth('api')->id() ?? Auth::id(),
                'deleted_at' => now(),
            ]);

            return $service;
        });
    }
}