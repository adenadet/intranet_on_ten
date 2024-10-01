<?php

namespace App\Http\Controllers\Api\Hrms;

use App\Http\Controllers\Controller;
use App\Http\Traits\Hrms\LeaveTrait;
use App\Models\EMR\Employee;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    use LeaveTrait;
    public function assign_leave_types(Request $request){
        return response()->json([
            'assigned_leave_types' => $this->hrms_leave_employee_assign_leave_types($request->input('user_id'), $request->input('leave_types')),
        ]);
    }

    public function confirm_leave_request(Request $request, $id){
        return response()->json([
            'leave_request' => $this->hrms_leave_request_confirm_leave($id),
            'my_team_leaves' => $this->hrms_leave_request_get_all('my_team', null,  true, true, $_GET['page'] ?? 1), 
        ]);
    }

    public function destroy($id)
    {
        return response()->json([
            'leave_request'     => $this->hrms_leave_request_delete_leave($id),
            'leave_requests'    => $this->hrms_leave_request_get_all('my_leaves', null, true, true, $_GET['page'] ?? 1),    
        ]);
    }

    public function index()
    {
        return response()->json([
            'requests' => $this->hrms_leave_request_get_all($_GET['type'], null, true, true, $_GET['page']),    
        ]);
    }

    public function initials()
    {
        return response()->json([
            'my_leave_types' => $this->hrms_leave_types_get_my_current_leave_types(null, false, true),    
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'leave_type_id' => 'required|numeric',
            'from_date' => 'required|date',
            'to_date' => 'required|date',
            'reason' => 'sometimes',
            'remarks' => 'sometimes',
            'is_half_day' => 'sometimes|boolean',
            'employee_id' => 'sometimes|numeric',
        ]);

        //$employee = Employee::where('id', '=', auth('api')->id())->with(['user'])->first();
        //$user = User::find($request->input('employee_id') ?? (Auth::id() ?? auth('api')->id()));
        return response()->json([
            'leave_request' => $this->hrms_leave_request_create_leave($request),
            'leave_requests' => $this->hrms_leave_request_get_all('my_leaves', null, true, true, $_GET['page'] ?? 1),    
        ]);

    }

    public function show($id)
    {
        return response()->json([
            'leave_request' => $this->hrms_leave_request_show_leave($id, $_GET['type']),
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'leave_type_id' => 'required|numeric',
            'start_date' => 'required|date',
            'to_date' => 'required|date',
            'description' => 'sometimes',
            'remarks' => 'sometimes',
            'is_half' => 'required|boolean',
        ]);

        return response()->json([
            'leave_request'     => $this->hrms_leave_request_update_leave($request, $id),
            'leave_requests'    => $this->hrms_leave_request_get_all('my_leaves', null, true, true, $_GET['page'] ?? 1),    
        ]);
    }
}
