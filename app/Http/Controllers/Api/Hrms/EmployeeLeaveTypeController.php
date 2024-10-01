<?php

namespace App\Http\Controllers\Api\Hrms;

use App\Http\Controllers\Controller;
use App\Http\Traits\Hrms\LeaveTrait;
use App\Models\HRMS\Employee;
use App\Models\HRMS\EmployeeLeaveType;
use App\Models\HRMS\LeaveRequest;
use App\Models\HRMS\LeaveType;

use Illuminate\Http\Request;

class EmployeeLeaveTypeController extends Controller
{
    use LeaveTrait;
    public function index()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(string $id)
    {
        return response()->json([
            'assigned_leave_types' => $this->hrms_leave_type_get_assigned_by_id($id),
        ]);
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
