<?php
namespace App\Services\Hrms;
use App\Models\Hrms\Employee;
use App\Models\Hrms\EmployeeLeaveType;
use App\Models\Hrms\LeaveType;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class EmployeeLeaveTypeService{
    
    protected function userId(){
        return auth('api')->id() ?? Auth::id();
    }

    public function create(array $data){
        return DB::transaction(function () use ($data) {
            foreach ($data['leave_types'] as $leave_type_id){
                $leave_type = LeaveType::findOrFail($leave_type_id);
                $employee_leave_type = EmployeeLeaveType::where('employee_id', '=', $data['employee_id'])->where('leave_type_id', '=', $leave_type_id)->count();
                
                if ($employee_leave_type == 0){
                    EmployeeLeaveType::create([
                        'employee_id' => $data['employee_id'],
                        'leave_type_id' => $leave_type_id,
                        'balance' => $leave_type->no_of_days,
                        'days_used' => 0,
                        'pending_days' => 0,
                        'created_by' => $this->userId(),
                        'updated_by' => $this->userId(),
                    ]);
                }
            }
            
        });
         
    }

    public function delete(int|string $id){
        return DB::transaction(function () use ($id) {
            $query = EmployeeLeaveType::find($id);
            
            $query->update([
                'deleted_by' => Auth::id() ?? auth('api')->id(),
                'deleted_at' => date('Y-m-d H:i:s'),
            ]);

            return $query;
        });
         
    }
    
    public function update(array $data, int|string $id){
        return DB::transaction(function () use ($data, $id) {
            $query = EmployeeLeaveType::find($id);
            
            $query->update([
                'employee_id' => $data['employee_id'],
                'leave_type_id' => $data['leave_type_id'],
                'balance' => $data['balance'],
                'days_used' => $data['no_of_days'],
                'pending_days' => $data['pending'],
                'updated_by' => Auth::id() ?? auth('api')->id(),
            ]);
            return $query; 
        });
    }
}