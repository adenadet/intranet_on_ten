<?php
namespace App\Http\Controllers\Api\ConsultantPractice;

use App\Http\Controllers\Controller;
use App\Http\Traits\ConsultantPractice\ConsultantPracticeTrait;
use App\Models\ConsultantPractice\Service;
use App\Models\ConsultantPractice\Specialty;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    use ConsultantPracticeTrait;
    public function destroy(string $id)
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json(['message' => 'Service not found.'], 404);
        }

        $service->status == Service::StatusActive ? $service->update([
            'status' => Service::StatusInactive,
            'deleted_at' => now(),
        ]) : $service->update([
            'status' => Service::StatusActive,
            'deleted_at' => null,
        ]);

        return response()->json([
            'message' => $service->status == Service::StatusActive ? 'Service reactivated successfully.' : 'Service deactivated successfully.',
            'service' => $service
        ]);
    }

    public function index()
    {
        $services = $this->consultant_practice_service_get_all($_GET['type'] ?? 'front', $_GET, true, true);
        return response()->json([
            'services' => $services
        ]);
    }

    public function initials(){
        return response()->json([
            'specialties' => $this->consultant_practice_specialty_get_all($_GET['type'] ?? 'front', null, false, false),
        ]);
    }

    public function show(string $id)
    {
        $service = $this->consultant_practice_service_get_by(null, $id, true);
        return response()->json([
            'service' => $service
        ], is_string($service) ? 404 :200);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'specialty_id' => 'required|exists:consultant_practice_specialties,id',
        ]);

        $service = Service::create([
            'name' => $request->name,
            'description' => $request->description,
            'specialty_id' => $request->specialty_id,
            'status' => $request->status ?? Service::StatusActive,
        ]);

        return response()->json([
            'service' => $service
        ], 201);
    }

    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'specialty_id' => 'required|exists:consultant_practice_specialties,id',
        ]);

        $service = Service::findOrFail($id);

        $service->update([
            'name' => $request->name,
            'description' => $request->description,
            'specialty_id' => $request->specialty_id,
            'status' => $request->status ?? Service::StatusActive,
        ]);

        return response()->json([
            'service' => $service
        ], 200);
    }
}
