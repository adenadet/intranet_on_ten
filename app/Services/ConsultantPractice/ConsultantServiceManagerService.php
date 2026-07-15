<?php

namespace App\Services\ConsultantPractice;

use App\Models\ConsultantPractice\ConsultantService;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ConsultantServiceManagerService
{
    public function create(int $consultantId, int $serviceId, float $price): ConsultantService
    {
        return DB::transaction(function () use ($consultantId, $serviceId, $price) {
            $existing = ConsultantService::withTrashed()
                ->where('consultant_id', $consultantId)
                ->where('service_id', $serviceId)
                ->first();

            if ($existing) {
                if ($existing->trashed()) {$existing->restore(); $existing->status = ConsultantService::StatusActive;}

                $existing->price = $price;
                $existing->save();

                return $existing;
            }

            // Create new
            return ConsultantService::create([
                'consultant_id' => $consultantId,
                'service_id'    => $serviceId,
                'price'         => $price,
                'status'        => ConsultantService::StatusActive,
            ]);
        });
    }

    public function update(array $data, $id): ConsultantService{
        return DB::transaction(function () use ($data, $id) {

            $consultantService = ConsultantService::findOrFail($id);

            // Optional: restrict fields
            $consultantService->fill([
                'price' => $data['price'] ?? $consultantService->price,
            ]);

            $consultantService->save();

            return $consultantService;
        });
    }

    public function deactivate(int $id): ConsultantService
    {
        return DB::transaction(function () use ($id) {

            $consultantService = ConsultantService::findOrFail($id);

            $consultantService->status = ConsultantService::StatusInactive;
            $consultantService->save();

            $consultantService->delete(); // soft delete

            return $consultantService;
        });
    }

    public function reactivate(int $id): ConsultantService
    {
        return DB::transaction(function () use ($id) {

            $consultantService = ConsultantService::withTrashed()
                ->where('id', $id)
                ->first();

            if (!$consultantService) {
                throw new ModelNotFoundException("ConsultantService not found");
            }

            if ($consultantService->trashed()) {
                $consultantService->restore();
            }

            $consultantService->status = ConsultantService::StatusActive;
            $consultantService->save();

            return $consultantService;
        });
    }
}