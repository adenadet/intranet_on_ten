<?php

namespace App\Http\Controllers\Api\Hrms;

use App\Http\Controllers\Controller;
use App\Http\Traits\Hrms\EmployeeTrait;
use App\Http\Traits\Hrms\LeaveTrait;
use App\Mail\Leave\ConfirmMail;
use App\Mail\Leave\RejectMail;
use App\Mail\Leave\SupervisorConfirmMail;
use App\Models\Department;
use App\Services\Hrms\Leave\RequestService;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    use EmployeeTrait, LeaveTrait;

    public function __construct(
        protected RequestService $request_service,
    ) {}

    public function assign_leave_types(Request $request){
        return response()->json([
            'assigned_leave_types' => $this->hrms_leave_employee_assign_leave_types($request->input('user_id'), $request->input('leave_types')),
        ]);
    }

    public function confirm(Request $request, int|string $id){
        return response()->json([
            'leave_request' => $this->request_service->approve($id, $request->all()),
        ]);
    }

    public function destroy(int|string $id){
        return response()->json([
            'request'     => $this->hrms_leave_request_delete_leave($id),
            'requests'    => $this->hrms_leave_request_get_all('my_leaves', null, true, true, $_GET['page'] ?? 1),    
        ]);
    }

    public function index(){
        return response()->json([
            'departments' => Department::select('id', 'name')->orderBy('name', 'ASC')->get(),
            'requests' => $this->hrms_leave_request_get_all($_GET['type'], $_GET ?? null, true, true, $_GET['page']),    
        ]);
    }

    public function initials(){
        return response()->json([
            'employees' => $this->hrms_employee_get_all('active', 'leave', null, false, null), 
            'my_leave_types' => $this->hrms_leave_types_get_my_current_leave_types(null, false, true),    
        ]);
    }

    public function show(int|string $id){
        $leave_request = $this->hrms_leave_request_show_leave($id, $_GET['type']);
        return response()->json([
            'leave_request' => $leave_request,
        ], is_string($leave_request) ? 404 : 200);
    }

    public function store(Request $request){
        $this->validate($request, [
            'leave_type_id' => 'required|numeric',
            'from_date' => 'required|date',
            'to_date' => 'required|date',
            'reason' => 'sometimes',
            'remarks' => 'sometimes',
            'is_half_day' => 'sometimes|boolean',
            'employee_id' => 'sometimes|numeric',
        ]);

        return response()->json(['leave_request' => $this->request_service->create($request->all()),]);
    }

    public function update(Request $request, int|string $id){
        $this->validate($request, [
            'leave_type_id' => 'required|numeric',
            'start_date' => 'required|date',
            'to_date' => 'required|date',
            'description' => 'sometimes',
            'remarks' => 'sometimes',
            'is_half' => 'required|boolean',
        ]);

        //$leave_request = $this->hrms_leave_request_show_leave($id, 'my_leaves');

        return response()->json([
            'leave_request' => $this->request_service->update($request->all(), $id),
        ]);
    }
}
