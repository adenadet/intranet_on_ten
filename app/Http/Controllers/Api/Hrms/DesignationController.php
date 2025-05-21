<?php

namespace App\Http\Controllers\Api\Hrms;

use App\Http\Controllers\Controller;
use App\Models\Hrms\Designation;
use Illuminate\Http\Request;

class DesignationController extends Controller
{
    public function destroy(string $id)
    {
        $designation = Designation::find($id);

        $designation->deleted_by = auth('api')->id();
        $designation->designation_at = date('Y-m-d H:i:s');

        $designation->save();
        return response()->json([
            'designation' => $designation,
        ]);
    }
 
    public function index()
    {
        return response()->json([
            'designations' => Designation::with(['department', 'employees.user', 'employees.department', 'unit'])->orderBy('name', 'ASC')->paginate(40),
        ]);
    }

    public function search(string $id)
    {
        return response()->json([
            'designations' => Designation::where('name', 'LIKE', "%$id%")->with(['department', 'employees.user', 'employees.department', 'unit'])->paginate(40)
        ]);
    }
    public function show(string $id)
    {
        return response()->json([
            'designation' => Designation::where('id', '=', $id)->with(['department', 'employees.user', 'employees.department', 'unit'])->first()
        ]);
    }

    public function store(Request $request)
    {
        $designation = Designation::create([
            'department_id' => $request->input('department_id'),
            'sub_department_id' => $request->input('sub_department_id'),
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'created_by' => auth('api')->id(),
            'updated_by' => auth('api')->id(),
        ]);

        return response()->json([
            'designation' => $designation,
            'designations' => Designation::with(['department', 'employees.user', 'employees.department', 'unit'])->orderBy('name', 'ASC')->paginate(40),
        ]);
    }

    public function update(Request $request, string $id)
    {
        $designation = Designation::find($id);
        
        $designation->department_id = $request->input('department_id');
        $designation->sub_department_id = $request->input('sub_department_id');
        $designation->name = $request->input('name');
        $designation->description = $request->input('description');
        $designation->created_by = auth('api')->id();
        $designation->updated_by = auth('api')->id();
        
        $designation->save();
        
        return response()->json([
            'designation' => $designation,
            'designations' => Designation::with(['department', 'employees.user', 'employees.department', 'unit'])->orderBy('name', 'ASC')->paginate(40),
        ]);
    }

}
