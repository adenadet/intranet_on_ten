<?php
namespace App\Http\Controllers\Api\ConsultantPractice;

use App\Http\Controllers\Controller;
use App\Http\Traits\ConsultantPractice\ConsultantPracticeTrait;
use App\Models\ConsultantPractice\Service;
use App\Models\ConsultantPractice\Specialty;
use App\Services\ConsultantPractice\ServiceManagerService;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    use ConsultantPracticeTrait;

    public function __construct(
        protected ServiceManagerService $service_manager_service,
    ){}
    public function destroy(string $id)
    {
        $service = $this->service_manager_service->delete($id);

        return response()->json([
            'message' => $service->status == Service::StatusActive ? 'Service reactivated successfully.' : 'Service deactivated successfully.',
            'service' => $service
        ], 200);
    }

    public function index()
    {
        $services = $this->consultant_practice_service_get_all($_GET['type'] ?? 'front', $_GET, true, true);
        return response()->json(['services' => $services]);
    }

    public function initials(){
        return response()->json([
            'specialties' => $this->consultant_practice_specialty_get_all($_GET['type'] ?? 'front', [], false, false),
        ]);
    }

    public function show(string $id)
    {
        $service = $this->consultant_practice_service_get_by(null, $id, true);
        return response()->json(['service' => $service], is_string($service) ? 404 :200);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'icp_code' => 'string|max:55',
            'description' => 'nullable|string',
            'specialty_id' => 'required|exists:consultant_practice_specialties,id',
        ]);

        $service = $this->service_manager_service->create($request->all());

        return response()->json(['service' => $service], 201);
    }

    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'specialty_id' => 'required|exists:consultant_practice_specialties,id',
        ]);

        $service = $this->service_manager_service->update($request->all(), $id);

        return response()->json(['service' => $service], 200);
    }
}
