<?php
namespace App\Services\ConsultantPractice;

use App\Models\ConsultantPractice\Service;
use App\Models\ConsultantPractice\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ServiceManagerService
{
    public function create(array $data){
        return DB::transaction(function () use ($data) {
            $service = Service::create([
                'name'          => $data['name'],
                'icp_code'      => $data['icp_code'] ?? null,
                'description'   => $data['description'] ?? null,
                'status'        => Service::StatusActive,
                'created_by'    => auth('api')->id() ?? Auth::id(),
                'updated_by'    => auth('api')->id() ?? Auth::id(),
            ]);

            return $service;
        });
    }

    public function update(array $data, int|string $service_id){
        return DB::transaction(function () use ($data, $service_id) {

            //$this->validateUpdate($data, $service_id);

            $service = Service::find($service_id);

            if (!$service) {
                throw ValidationException::withMessages(['message' => 'Service not found.']);
            }
            else{
                $service->update([
                    'name'          => $data['name'] ?? $service->name,
                    'icp_code'      => $data['icp_code'] ?? $service->icp_code,
                    'description'   => $data['description'] ?? $service->description,
                    'status'        => $data['status'] ?? $service->status,
                    'updated_by'    => auth('api')->id() ?? Auth::id(),
                ]);
            }

            return $service;
        });
    }

    public function delete(int|string $service_id){
        return DB::transaction(function () use ($service_id) {
            $service = Service::find($service_id);
            if (!$service) {
                throw ValidationException::withMessages(['message' => 'Service not found.']);
            }
            if ($service->status == Service::StatusInactive) {
                $service->update([
                    'status' => Service::StatusActive,
                    'updated_by' => auth('api')->id() ?? Auth::id(),
                    'deleted_by' => null,
                    'deleted_at' => null,
                ]);
            }
            else{
                $service->update([
                    'status' => Service::StatusInactive,
                    'updated_by' => auth('api')->id() ?? Auth::id(),
                    'deleted_by' => auth('api')->id() ?? Auth::id(),
                    'deleted_at' => now(),
                ]);
            }

            return $service;
        });
    }
}