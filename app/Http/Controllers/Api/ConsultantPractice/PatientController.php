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
        //
    }

    public function index()
    {
        $query = Patient::query();

        if (!empty($_GET['query'])){
            $search = $_GET['query'];
            $query = $query->where('unique_id', 'LIKE', "%$search%")->orWhere('name', 'LIKE', "%$search%");
        }

        $query = $query->paginate(50);

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
        $patient = $patient_manager->create($request);

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
