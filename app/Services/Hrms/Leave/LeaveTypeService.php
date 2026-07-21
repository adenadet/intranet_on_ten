<?php

namespace App\Services\Hrms\Leave;

use App\Models\Hrms\Employee;
use App\Models\Hrms\EmployeeLeaveType;
use App\Models\Hrms\LeaveType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LeaveTypeService
{
    public function assign(int $employeeId, array $leaveTypeIds)
    {
        return DB::transaction(function () use ($employeeId, $leaveTypeIds) {
            foreach ($leaveTypeIds as $leaveTypeId) {
                $leaveType = LeaveType::findOrFail($leaveTypeId);
                EmployeeLeaveType::firstOrCreate(
                    ['employee_id' => $employeeId, 'leave_type_id' => $leaveTypeId,],
                    [
                        'balance' => $leaveType->no_of_days, 
                        'days_used' => 0, 
                        'pending_days' => 0,
                        'created_by' => auth('api')->id() ?? Auth::id(),
                        'updated_by' => auth('api')->id() ?? Auth::id(),
                    ]
                );
            }

            return true;
        });
    }

    public function delete(int $id)
    {
        $record = EmployeeLeaveType::findOrFail($id);

        $record->update([
            'deleted_by' => auth()->id(),
            'deleted_at' => now(),
        ]);

        return $record;
    }

    public function find(int $id)
    {
        return EmployeeLeaveType::with([
            'employee.user',
            'leave_type'
        ])->findOrFail($id);
    }

    public function sync(int $employeeId, array $leaveTypeIds){
        return DB::transaction(function () use ($employeeId, $leaveTypeIds) {

            EmployeeLeaveType::where('employee_id', $employeeId)->whereNotIn('leave_type_id', $leaveTypeIds)->delete();

            foreach ($leaveTypeIds as $leaveTypeId) {
                $leaveType = LeaveType::findOrFail($leaveTypeId);
                EmployeeLeaveType::withTrashed()
                    ->updateOrCreate(
                        ['employee_id' => $employeeId, 'leave_type_id' => $leaveTypeId,],
                        [
                            'deleted_at' => null,
                            'balance' => $leaveType->no_of_days,
                            'updated_by' => auth()->id(),
                        ]
                    );
            }

            return EmployeeLeaveType::where(
                'employee_id',
                $employeeId
            )->get();
        });
    }

    public function updateBalance(int $employeeId, int $leaveTypeId, float $days, string $operation) {
        $employeeLeaveType = EmployeeLeaveType::where(['employee_id' => $employeeId, 'leave_type_id' => $leaveTypeId])->firstOrFail();

        switch ($operation) {

            case 'pending_add':
                $employeeLeaveType->increment(
                    'pending_days',
                    $days
                );
            break;

            case 'pending_remove':
                $employeeLeaveType->decrement(
                    'pending_days',
                    $days
                );
            break;

            case 'used_add':
                $employeeLeaveType->increment(
                    'days_used',
                    $days
                );
            break;

            case 'used_remove':
                $employeeLeaveType->decrement(
                    'days_used',
                    $days
                );
            break;
        }

        return $employeeLeaveType->fresh();
    }

}