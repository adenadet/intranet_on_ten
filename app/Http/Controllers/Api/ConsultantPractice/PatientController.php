<?php

namespace App\Http\Controllers\Api\ConsultantPractice;

use App\Http\Controllers\Controller;
use App\Models\ConsultantPractice\Patient;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
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
            $query = Patient::where('id', '=', $id)->where('unique_id', '=', $id)->firstOrFail();
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

        $query = Patient::create([
            'name' => $request->name,
            'unique_id' => $request->unique_id,
            'created_by' => Auth::id() ?? auth('api')->id(),
            'updated_by' => Auth::id() ?? auth('api')->id(),
        ]);

        return response()->json([
            'patient' => $query,
        ], is_string($query) ? 500 : 201);
    }

    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'name' => 'string|required',
            'unique_id' => 'required',
        ]);

        try{
            $query = Patient::where('id', '=', $id)->where('unique_id', '=', $id)->firstOrFail();
            $query->update([
                'name' => $request->name,
                'unique_id' => $request->unique_id,
                'updated_by' => Auth::id() ?? auth('api')->id(),
            ]);
        }
        catch(Exception $e){
            $query = $e->getMessage();
        }
        
        return response()->json([
            'patient' => $query,
        ], is_string($query) ? 500 : 201);
    }

}
