<?php

namespace App\Http\Controllers\Api\Hrms;

use App\Http\Controllers\Controller;
use App\Http\Traits\Hrms\EmployeeTrait;
use App\Http\Traits\Ums\UserTrait;
use App\Imports\Hrms\EmployeeImport;
use App\Models\Hrms\Designation;
use App\Models\Area;
use App\Models\Branch;
use App\Models\Country;
use App\Models\Department;
use App\Models\Hrms\Employee;
use App\Models\NextOfKin;
use App\Models\Staff;
use App\Models\State;
use App\Models\User;
use App\Services\Hrms\EmployeeService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeController extends Controller
{
    use EmployeeTrait, UserTrait;

    public function __construct(
        protected EmployeeService $es, 
    ) {}

    public function assign_manager(Request $request, int|string $id)
    {
        $this->validate($request, [
            'supervisor_id' => 'required|numeric',
            'reports_to' => 'required|numeric',
        ]);

        $employee = $this->es->assign_manager($request->all(), $id);
        return response()->json([
            'employee' => $employee,
            'staff' => $this->hrms_employee_get_by_id($id, $_GET['viewer'] ?? null),       
        ]);

    }

    public function destroy(int|string $id){

        $areas = Area::select('id', 'name')->where('state_id', 25)->orderBy('name', 'ASC')->get();
        $branches = Branch::select('id', 'name')->orderBy('name', 'ASC')->get();
        $departments = Department::select('id', 'name')->orderBy('name', 'ASC')->get();
        $nok = NextOfKin::where('user_id', auth('api')->id())->get();
        $states = State::orderBy('name', 'ASC')->get();
        
        return response()->json([
            'areas' => $areas,
            'branches' => $branches,
            'departments' => $departments,
            'employee' => $this->es->deactivate($id),
            'nok' => $nok,
            'states' => $states,       
            'users' => $this->user_get_all(),
        ]);
    }
    
    public function import(Request $request){
        $dent = explode("base64,", $request->input('file'));
        $decodedData = base64_decode($dent[1], true);
        if ($decodedData === false) {
            return response()->json([
                'result' => null,
                'message' => 'The provided string is not valid Base64.',
            ], 500);
        }
        
        $fileSignature = substr($decodedData, 0, 4);
        $validCsvSignature = chr(0xEF) . chr(0xBB) . chr(0xBF); // Optional BOM for UTF-8 CSV
        $validXlsxSignature = chr(0x50) . chr(0x4B) . chr(0x03) . chr(0x04); // XLSX files (PKZIP format)

        if ($fileSignature === $validCsvSignature || strpos($decodedData, ',') !== false) {
            $fileType = "xlsx";
        } 
        elseif ($fileSignature === $validXlsxSignature) {
            $fileType = "xlsx";
        }
        else {
            return response()->json([
                'result' => null,
                'message' => "The Base64 string does not represent a valid CSV or Excel file."
            ]);
        }

        $fileName = 'uploaded_file_'.time().'.'. $fileType;
        $tempPath = public_path('uploads/files/' . $fileName);
        file_put_contents($tempPath, $decodedData);

        try {
            $query = Excel::import(new EmployeeImport, $tempPath);
            @unlink($tempPath);
            return response()->json([
                'result' => $query,
                'message' => 'The file was imported successfully',
            ]);
        }

        catch(Exception $e){
            @unlink($tempPath);
            return response()->json(['error' => 'Failed to process the file', 'details' => $e->getMessage()], 500);
        }
    }

    public function index()
    {
        $areas = Area::select('id', 'name')->where('state_id', 25)->orderBy('name', 'ASC')->get();
        $branches = Branch::select('id', 'name')->orderBy('name', 'ASC')->get();
        $departments = Department::select('id', 'name')->orderBy('name', 'ASC')->get();
        $employees = $this->hrms_employee_get_all($_GET['status'] ?? 'active', $_GET, true, true, $_GET['page'] ?? 1);
        $nok = NextOfKin::where('user_id', auth('api')->id())->get();
        $states = State::orderBy('name', 'ASC')->get();
        
        return response()->json([
            'departments' => $departments,
            'employees' => $employees,
            //'old_employees' => $this->user_staffs_get_all('all', null, true, true, $_GET['page'] ?? 1),
            'users' => $this->user_get_all(),
        ]);
    }

    public function initials(){
        $employees = Employee::pluck('user_id');
        return response()->json([
            'areas' => Area::select('id', 'name')->orderBy('name', 'ASC')->get(),
            'departments'   => Department::select('id', 'name')->orderBy('name', 'ASC')->get(),
            'designations'  => Designation::select('id', 'name')->orderBy('name', 'ASC')->get(),
            'inactive_users'=> User::select('id', 'first_name', 'last_name', 'unique_id')->whereNotIn('id', $employees)->orderBy('first_name', 'ASC')->get(),
            'staffs'        => Employee::select('username', 'employee_id', 'user_id')->where('employment_status', '=', 1)->with(['user'])->orderBy('username', 'ASC')->get(),
            'states' => State::select('id', 'name')->orderBy('name', 'ASC')->with('areas')->get(),
        ]);
    }

    public function latest(){
        return response()->json([
            'staffs' => User::where('user_type', '!=', 'Applicant')->orderBy('created_at', 'DESC')->limit(8)->get(),
        ]);
    }

    public function search(int|string $id){
        return response()->json([
            'employees' => $this->hrms_employee_search_by_query($id, true, true, $_GET['page'] ?? 1),
        ]);
    }


    public function show(int|string $id){
        return response()->json([
            'employee' => $this->hrms_employee_get_by_id($id, $_GET['viewer'] ?? null),
            'staff' => $this->hrms_employee_get_by_id($id, $_GET['viewer'] ?? null),       
        ]);
    }

    public function store(Request $request){
        if (is_null($request->input('user_id'))){
            $this->validate($request, [
                'user.first_name' => 'required',
                'user.last_name' => 'required',
                'user.street' => 'sometimes',
                'user.street2' => 'sometimes',
                'user.city' => 'required',
                'user.state_id' => 'numeric',
                'user.area_id' => 'numeric',
                'user.phone' => 'numeric',
                'user.alt_phone' => 'nullable|numeric',
                'user.sex' => 'required|string',
                'user.dob' => 'required|date',
                'user.supervisor_id' => 'sometimes|numeric',
            ]);
        }
        else{
            $this->validate($request, [
                'user_id' => 'required|numeric',
                'supervisor_id' => 'sometimes|numeric',
                'reports_to' => 'required|numeric',
                'email' => 'sometimes|email',
            ]);
        }

        $employee = $this->es->create($request->all());

        return response()->json([
            'areas' => Area::select('id', 'name')->where('state_id', 25)->orderBy('name', 'ASC')->get(),
            'branches' => Branch::select('id', 'name')->orderBy('name', 'ASC')->get(),
            'departments' => Department::select('id', 'name')->orderBy('name', 'ASC')->get(),
            'employee' => $employee,
            'nok' => NextOfKin::where('user_id', auth('api')->id())->get(),
            'states' => State::orderBy('name', 'ASC')->get(),       
            'message' => 'Your password has been changed successfully',
            'status' => 'success',
        ]);
    }

    public function update(Request $request, int|string $id)
    {
        $this->validate($request, [
            'date_of_joining' => 'sometimes|date',
            'date_of_leaving' => 'sometimes|date',
            'department_id' => 'sometimes|numeric',
            'designation_id' => 'sometimes|numeric',
            'email' => 'sometimes|email',
            'employee_id' => 'sometimes|numeric',
            'office_shift_id' => 'sometimes|numeric',
        ]);

        return response()->json([
            'employee' => $this->es->update($request->all(), $id),
        ]);
    }

    public function update_status(Request $request, int|string $id)
    {
        $employee = $this->es->changeStatus($request->all(), $id);

        return response()->json([
            'employee' => $employee,
            'message' => 'Employment status has been changed successfully',
            'status' => 'success', 
        ]);
    }

    public function user(int|string $id)
    {
        return response()->json([
            'employee' => $this->hrms_employee_get_single('user_id', $id, true), 
        ]);
    }
}