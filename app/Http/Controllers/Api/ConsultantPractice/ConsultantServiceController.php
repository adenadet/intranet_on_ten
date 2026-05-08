<?php

namespace App\Http\Controllers\Api\ConsultantPractice;

use App\Http\Controllers\Controller;
use App\Http\Traits\ConsultantPractice\ConsultantPracticeTrait;
use App\Models\ConsultantPractice\Consultant;
use App\Models\ConsultantPractice\ConsultantService;
use App\Models\ConsultantPractice\Service;
use App\Services\ConsultantPractice\ConsultantServiceManagerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConsultantServiceController extends Controller
{
    use ConsultantPracticeTrait;
    public function consultant(string $id)
    {
        $consultant_services = ConsultantService::select('id', 'service_id', 'price')->where('consultant_id', '=', $id)->with(['service'])->get();
        
        return response()->json([
            'consultant_services' => $consultant_services,
            'consultant_payment_type' => Consultant::find($id)->billing_type,
        ]);
    }
    public function destroy(string $id)
    {
        $consultant_service = ConsultantService::findOrFail($id);

        $consultant_manager_service = new ConsultantServiceManagerService();

        $consultant_service = $consultant_service->status == ConsultantService::StatusInactive ? $consultant_manager_service->deactivate($id) : $consultant_manager_service->reactivate($id);
        
        return response()->json([
            'consultant_service' => $consultant_service,
            'message' => 'Consultant service has been ' . ($consultant_service->status == ConsultantService::StatusInactive ? 'deactivated' : 'reactivated') . ' successfully.',
        ], 200);
    
    }

    public function index()
    {
        $consultant_service = $this->consultant_practice_consultant_service_get_all($_GET['type'], $_GET, true, true);
        return response()->json([
            'consultant_services' => $consultant_service,
        ]);
    }
    
    public function initials()
    {
        return response()->json([
            'consultants' => $this->consultant_practice_consultant_service_get_all('front', null, false, false),
            'services' => $this->consultant_practice_consultant_service_get_all('front', null, false, false),
        ]);
    }

    public function multiple(Request $request)
    {
        $request->validate([
            'consultant_id' => 'required|integer',
            'services' => 'required|array',
            'services.*.service_id' => 'required|integer',
            'services.*.price' => 'required|numeric',
            'services.*.status' => 'nullable|integer',
        ]);

        $consultant_manager_service = new ConsultantServiceManagerService();

        return DB::transaction(function () use ($request, $consultant_manager_service) {

            $consultantId = $request->consultant_id;
            $incomingServices = collect($request->services);

            // Extract incoming service_ids
            $incomingServiceIds = $incomingServices->pluck('service_id')->toArray();

            /*
            |--------------------------------------------------------------------------
            | 1. CREATE OR UPDATE (UPSERT-LIKE VIA SERVICE)
            |--------------------------------------------------------------------------
            */
            foreach ($incomingServices as $service) {

                $existing = ConsultantService::withTrashed()
                    ->where('consultant_id', $consultantId)
                    ->where('service_id', $service['service_id'])
                    ->first();

                if ($existing) {
                    // Reactivate if needed + update price
                    if ($existing->trashed()) {
                        $consultant_manager_service->reactivate($existing->id);
                    }

                    $consultant_manager_service->update(['price' => $service['price']], $existing->id );

                } else {
                    // Create new
                    $consultant_manager_service->create(
                        $consultantId,
                        $service['service_id'],
                        $service['price']
                    );
                }
            }

            $existingServices = ConsultantService::where('consultant_id', $consultantId)->get();

            foreach ($existingServices as $existing) {
                if (!in_array($existing->service_id, $incomingServiceIds)) {
                    $consultant_manager_service->deactivate($existing->id);
                }
            }

            $final = ConsultantService::where('consultant_id', $consultantId)->with(['consultant', 'service'])->get();


            return response()->json([
                'consultant_services' => $final,
                'message' => 'Consultant services have been created successfully.',
            ], 201);
        });
    }

    public function show(string $id)
    {
        $consultant_service = $this->consultant_practice_consultant_service_get_by($id, true);
        return response()->json([
            'consultant_service' => $consultant_service,
        ], is_string($consultant_service) ? 404 : 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'consultant_id' => 'required|integer',
            'service_id' => 'required|integer',
            'price' => 'required|numeric',
        ]);

        $consultant_manager_service = new ConsultantServiceManagerService();

        $consultant_service = $consultant_manager_service->create($request->consultant_id, $request->service_id, $request->price);

        return response()->json([
            'consultant_service' => $consultant_service,
            'message' => 'Consultant service has been created successfully.',
        ], 201);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'consultant_id' => 'required|integer',
            'service_id' => 'required|integer',
            'price' => 'required|numeric',
        ]);
        $consultant_manager_service = new ConsultantServiceManagerService();
        $consultant_service = $consultant_manager_service->update($request->all(), $id);
        return response()->json([
            'consultant_service' => $consultant_service,
            'message' => 'Consultant service has been updated successfully.',
        ], 200);    
    }
}
