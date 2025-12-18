<?php 

namespace App\Http\Traits\Hrms;

use App\Http\Traits\General\LogTrait;
use App\Http\Traits\General\FileManagerTrait;

use App\Models\Hrms\Employee;
use App\Models\Hrms\LeaveAllowance;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Session;

trait LeaveAllowanceTrait{
    use FileManagerTrait, LogTrait;

    public function hrms_leave_allowance_confirm_request($data, $id){
        $leave_allowance = LeaveAllowance::find($id);

        $leave_allowance->status = 2;
        $leave_allowance->amount = $data['amount'];
        $leave_allowance->approved_by = Auth::id() ?? auth('api')->id(); 
        $leave_allowance->approved_at = date('Y-m-d H:i:s'); 
        $leave_allowance->approval_remark = $data['approval_remark'];

        $leave_allowance->save();

        return $leave_allowance;
    }
    
    public function hrms_leave_allowance_create_request($employee_id, $leave_request_id){
        $leave_allowance = LeaveAllowance::create([
            'employee_id' => $employee_id,
            'leave_request_id' => $leave_request_id,
            'status' => 0,
            'created_by' => Auth::id() ?? auth('api')->id(),
            'updated_by' => Auth::id() ?? auth('api')->id(),
        ]);

        return $leave_allowance;
    }

    public function hrms_leave_allowance_delete_request($id){
        $leave_allowance = LeaveAllowance::find($id);

        $leave_allowance->deleted_by = Auth::id() ?? auth('api')->id(); 
        $leave_allowance->deleted_at = date('Y-m-d H:i:s'); 
    
        $leave_allowance->save();

        return $leave_allowance;
    }

    public function hrms_leave_allowance_get_all($type, $specific, $detailed, $paginated, $page){
        $query = LeaveAllowance::query();
        switch ($type){
            case 'all':
                $query = $query->withTrashed();
            break;
            case 'mine':
                $employee = Employee::where('user_id', '=', (Auth::id() ?? auth('api')->id()))->first();
                $query = $query->where('employee_id', '=', $employee->id);
            break;
            /*case 'status':
                $query = LeaveAllowance::where('status', '=', $specific);
            break;*/ 
        }

        if (is_array($specific)){
            if (!empty($specific['query'])){
                $search = $specific['query'];

                $users = User::where('first_name', 'LIKE', "%$search%")
                    ->orWhere('middle_name', 'LIKE', "%$search%")
                    ->orWhere('last_name', 'LIKE', "%$search%")
                    ->orWhere('email', 'LIKE', "%$search%")
                    ->pluck('id');
                
                $employees = Employee::whereIn('user_id', $users)->orWhere('username', 'LIKE', "%$search%")->orderBy('username', 'ASC')->pluck('id');

                $query = $query->whereIn('employee_id', $employees);
            }
        }

        $query = $detailed ? $query->with(['employee.user', 'employee_leave.leave_type']) : $query;
        $query = $query->latest();
        $query = $paginated ? $query->paginate(50) : $query->get(); 

        return $query;
    }

    public function hrms_leave_allowance_get_by_id($id){
        $leave_allowance = LeaveAllowance::where('id', '=', $id)->with(['approver', 'employee', 'updater'])->first();
        return $leave_allowance;
    }

}