<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\General\FileManagerTrait;
use App\Models\Department;
use App\Models\Policy\Policy;
use App\Models\Policy\PolicyCategory;
use App\Models\Policy\PolicyDepartment;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PolicyController extends Controller
{
    use FileManagerTrait;
    public function index()
    {
        $policies = Policy::orderBy('name', 'ASC')->with(['depts.department', 'creator'])->paginate(25);
        if ($search = $_GET['search']){
            $policies = Policy::orderBy('name', 'ASC')->where('name', 'LIKE', "%$search%")->with(['depts.department', 'creator'])->paginate(25);
        }
        else{
            $policies = Policy::orderBy('name', 'ASC')->with(['depts.department', 'creator'])->paginate(25);
        }
        return response()->json([
            'policies'      => $policies,       
            'departments'   => Department::all(),       
        ]);
    }

    public function initials()
    {
        return response()->json([
            'departments'   => Department::select('id', 'name')->orderBy('name', 'ASC')->get(),       
        ]);
    }

    public function store(Request $request)
    {
        $fileName = (!is_null($request->input('file')))  ? "upload/policies/".$this->file_upload($request->input('file'), 'pdf', "upload/policies", 5) : null;

        $policy = Policy::create([
            'name' =>  $request->input('name'),
            'file' => $fileName ?? NULL,
            'category_id' => $request->input('category_id'),
            'description' => $request->input('description'),
            'created_by' =>  auth('api')->id(),
            'updated_by' =>  auth('api')->id(),
        ]);

        if (count($request->input('departments')) > 0){
            foreach ($request->input('departments') as $department_id){
                PolicyDepartment::create([
                    'policy_id'     => $policy->id,
                    'department_id' => $department_id,
                    'created_by'    => auth('api')->id(),
                ]);
            }
        }

        $policies = Policy::orderBy('name', 'ASC')->with(['depts.department', 'creator'])->paginate(25);

        return response()->json([
            'policies'      => $policies,           
        ]);         
    }

    public function assign(Request $request)
    {
        foreach ($request->input('departments') as $department){
            $policy_department = PolicyDepartment::where('policy_id', '=', $request->input('policy_id'))->where('department_id', '=', $department)->first();

            if ($policy_department === null){
                $policy_dept = PolicyDepartment::create([
                    'policy_id'     => $request->input('policy_id'),
                    'department_id' => $department,
                    'created_by'    => auth('api')->id(),
                ]);
            }
        }

        $policy_departments = PolicyDepartment::where('policy_id', '=', $request->input('policy_id'))->get();
        foreach ($policy_departments as $pol_dept){
            if (!in_array($pol_dept->department_id, $request->input('departments'))){
                $q = 'DELETE FROM policy_departments where `policy_id` = '.$pol_dept->policy_id.' AND `department_id` = '.$pol_dept->department_id;
                //echo $q;
                DB::delete($q);
                //$pol_dept->delete();
            }
        }

        $policy     = Policy::find($request->input('policy_id'));
        $policies   = Policy::orderBy('name', 'ASC')->with('depts.department')->with('category')->with('creator')->paginate(25);

        return response()->json([
            'policies'      => $policies,       
            'policy'        => $policy,           
        ]);
         
    }

    public function all($id)
    {
        if ($id=='departmental'){
            $policy_id = PolicyDepartment::where('department_id', '=', auth('api')->user()->department_id)->pluck('policy_id');
            if ($search = $_GET['query']){
                $policies = Policy::whereIn('id', $policy_id)->where('name', 'LIKE', "%$search%")->with(['creator'])->orderBy('name', 'ASC')->paginate(25);
            }
            else{
                $policies = Policy::whereIn('id', $policy_id)->with(['creator'])->orderBy('name', 'ASC')->paginate(25);
            }
        }
        else if ($id=='general'){
            if ($search = $_GET['query']){$policies = Policy::where('category_id', '=', 0)->where('name', 'LIKE', "%$search%")->orderBy('name', 'ASC')->paginate(25);}
            else{$policies = Policy::where('category_id', '=', 0)->orderBy('name', 'ASC')->paginate(25);}
        }

        return response()->json([
            'view'          => $id,
            'policies'      => $policies,       
        ]);
    }

    public function search()
    {
        if ($search = $_GET['search']){
           $policies = Policy::orderBy('name', 'ASC')->with('category')->with('state')->with('branch')->with('department')->where(function($query) use ($search){
                $query->where('name', 'LIKE', "%$search%");
                })->paginate(52);
            }
        else{
            $policies = Policy::orderBy('name', 'ASC')->with('area')->with('state')->with('branch')->with('department')->paginate(52);
        }        
        return response()->json(['policies' => $policies,]);
    }

    public function show($id){
        $policy = Policy::find($id);

        return response()->json(['policy' => $policy,]);
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try{
            $policy = Policy::find($id);

            if (base64_decode($request->input('file'))){
                $fileName = $this->file_upload($request->input('file'), 'pdf', "upload/policies", $id);
                $fileName = "upload/policies/".$fileName;
            }
            else{
                $fileName = $policy->file;
            }
            
            $policy->name = $request->input('name');
            $policy->file = $fileName;
            $policy->category_id = $request->input('category_id');
            $policy->description = $request->input('description');
            $policy->updated_by =  auth('api')->id();

            $policy->save();
            //Add New Policy Department
            foreach ($request->input('departments') as $department){
                $policy_department = PolicyDepartment::where('policy_id', '=', $request->input('policy_id'))->where('department_id', '=', $department)->first();
    
                if ($policy_department === null){
                    PolicyDepartment::create([
                        'policy_id'     => $policy->id,
                        'department_id' => $department,
                        'created_by'    => auth('api')->id()
                    ]);
                }
            }

            //Remove Policy Departments that are no more in use
            $policy_departments = PolicyDepartment::where('policy_id', '=', $request->input('policy_id'))->whereNotIn('department_id', $request->input('department_id'))->delete();

            DB::commit();

            return response()->json([
                'policies' => Policy::where('id', '=', $id)->with(['creator', 'departments.dept'])->first()
            ], 200);
        }
        catch(Exception $e){
            DB::rollBack();

            return response()->json([
                'policies' => $e->getMessage()
            ], 500);
        }
        
    }

    public function destroy($id)
    {
        $policy = Policy::find($id);
        
        $policy->deleted_by = auth('api')->id();
        $policy->deleted_at = date('Y-m-d H:i:s');
        $policy->save();

        //$policy->delete();

        $policies = Policy::orderBy('name', 'ASC')->with(['depts', 'creator'])->paginate(25);

        return response()->json([
            'policies'      => $policies,       
        ]);
    }
}
