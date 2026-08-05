<?php

namespace App\Http\Controllers\Api\ConsultantPractice;

use App\Http\Controllers\Controller;
use App\Http\Traits\ConsultantPractice\ConsultantPracticeTrait;
use App\Models\ConsultantPractice\Patient;
use App\Services\ConsultantPractice\PatientManagerService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    use ConsultantPracticeTrait;
    public function destroy(string $id)
    {
        $patient_manager = new PatientManagerService();
        $patient = $patient_manager->delete($id);

        return response()->json([
            'patient' => $patient,
        ]);
    }

    public function index()
    {
        $query = $this->consultant_practice_patient_get_all($_GET['type'] ?? 'all', $_GET, true, true);

        return response()->json([
            'patients' => $query,
        ], is_string($query) ? 404 : 200);
    }

    public function show(string $id)
    {
        try{
            $query = Patient::where('id', '=', $id)->orWhere('unique_id', '=', $id)->firstOrFail();
        }
        catch(Exception $e){
            $query = $e->getMessage();
        }

        return response()->json([
            'patient' => $query,
        ], is_string($query) ? 404 : 200);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'string|required',
            'unique_id' => 'required',
        ]);

        $patient_manager = new PatientManagerService();
        $patient = $patient_manager->create($request->all());

        return response()->json([
            'patient' => $patient,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'name' => 'string|required',
            'unique_id' => 'required',
        ]);

        $patient_manager = new PatientManagerService();
        $patient = $patient_manager->update($request, $id);
        
        return response()->json([
            'patient' => $patient,
        ], is_string($patient) ? 500 : 201);
    }

}
