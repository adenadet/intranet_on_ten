<?php

namespace App\Http\Controllers\Api\ConsultantPractice;

use App\Http\Controllers\Controller;
use App\Http\Traits\ConsultantPractice\ConsultantPracticeTrait;
use App\Models\ConsultantPractice\Company;
use App\Models\ConsultantPractice\Consultant;
use App\Models\ConsultantPractice\Service;
use App\Models\ConsultantPractice\Specialty;
use App\Services\ConsultantPractice\ConsultantService;
use Illuminate\Http\Request;

class ConsultantController extends Controller
{
    use ConsultantPracticeTrait;
    public function destroy(string $id)
    {
        $consultant = Consultant::find($id);
        $consultant_manager = new ConsultantService();

        $consultant = $consultant->status == Consultant::StatusActive ? $consultant_manager->deactivate($id) : $consultant_manager->reactivate($id);

        return response()->json([
            'consultant' => $consultant
        ]);
    }


    public function index()
    {
        $consultants = $this->consultant_practice_consultant_get_all($_GET['type'] ?? 'front', $_GET, true, true);
        return response()->json([
            'consultants' => $consultants
        ]);
    }

    public function initials(){
        return response()->json([
            'companies' => Company::query()->orderBy('name', 'ASC')->get(['id', 'name']),
            'specialties' => Specialty::query()->with(['services'])->orderBy('name', 'ASC')->get(['id', 'name']),
            'services' => Service::query()->orderBy('name', 'ASC')->get(['id', 'name']),
        ]);
    }

    public function show(string $id)
    {
        $consultant = $this->consultant_practice_consultant_get_by($id, true);

        if (is_string($consultant)) {
            return response()->json(['message' => 'Consultant not found.'], 404);
        }

        return response()->json([
            'consultant' => $consultant,
            'services' => $consultant->services()->with(['service', 'consultant'])->get(),
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'sex' => 'sometimes',
            'title' => 'sometimes|string|max:20',
            'email' => 'sometimes|email|max:255',
            'phone' => 'sometimes|string|max:20',
            'company_id' => 'required',
            'specialty_id' => 'required|exists:consultant_practice_specialties,id',
            'status' => 'nullable|in:0,1',
        ]);
        $consultant_manager = new ConsultantService();
        $consultant = $consultant_manager->create($request->all());

        return response()->json([
            'consultant' => $consultant
        ]);
    }

    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'sex' => 'sometimes',
            'title' => 'sometimes|string|max:20',
            'email' => 'sometimes|email|max:255',
            'phone' => 'sometimes|string|max:20',
            'company_id' => 'required',
            'specialty_id' => 'required|exists:consultant_practice_specialties,id',
            'status' => 'nullable|in:0,1',
        ]);

        $consultant_manager = new ConsultantService();
        $consultant = $consultant_manager->update($request->all(), $id);

        return response()->json([
            'consultant' => $consultant
        ]);
    }
}
