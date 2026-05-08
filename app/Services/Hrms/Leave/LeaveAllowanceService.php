<?php

namespace App\Services\Hrms\Leave;

use App\Models\Hrms\Employee;
use App\Models\Hrms\LeaveAllowance;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LeaveAllowanceService
{
    public function createRequest(int $employeeId, int $leaveRequestId) {
        return LeaveAllowance::create([
            'employee_id' => $employeeId,
            'leave_request_id' => $leaveRequestId,
            'status' => 0,
            'created_by' => auth('api')->id() ?? Auth::id(),
            'updated_by' => auth('api')->id() ?? Auth::id(),
        ]);
    }

    public function approve(int $id,array $data) {

        $allowance = LeaveAllowance::findOrFail($id);

        $allowance->update([
            'status' => 2,
            'amount' => $data['amount'],
            'approved_by' => auth('api')->id() ?? Auth::id(),
            'approved_at' => now(),
            'approval_remark' => $data['approval_remark'],
        ]);

        return $allowance->fresh();
    }

    public function delete(int $id)
    {
        $allowance = LeaveAllowance::findOrFail($id);

        $allowance->update([
            'deleted_by' => auth('api')->id() ?? Auth::id(),
            'deleted_at' => now(),
        ]);

        return $allowance;
    }

    public function find(int $id)
    {
        return LeaveAllowance::with(['approver', 'employee', 'updater'])->findOrFail($id);
    }

    public function getAll(
        string $type = 'all',
        ?array $filters = null,
        bool $detailed = false,
        bool $paginated = true
    ) {

        $query = LeaveAllowance::query();

        switch ($type) {

            case 'mine':

                $employee = Employee::where(
                    'user_id',
                    auth()->id()
                )->first();

                $query->where(
                    'employee_id',
                    $employee->id
                );

            break;
        }

        if (!empty($filters['query'])) {

            $search = $filters['query'];

            $users = User::where(function ($q) use ($search) {

                $q->where('first_name', 'LIKE', "%$search%")
                    ->orWhere('middle_name', 'LIKE', "%$search%")
                    ->orWhere('last_name', 'LIKE', "%$search%")
                    ->orWhere('email', 'LIKE', "%$search%");
            })->pluck('id');

            $employees = Employee::whereIn(
                'user_id',
                $users
            )->pluck('id');

            $query->whereIn('employee_id', $employees);
        }

        if ($detailed) {
            $query->with([
                'employee.user',
                'employee_leave.leave_type'
            ]);
        }

        $query->latest();

        return $paginated
            ? $query->paginate(50)
            : $query->get();
    }
}