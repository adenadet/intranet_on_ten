<?php

namespace App\Services\Hrms\Leave;

use App\Models\Hrms\Employee;
use App\Models\Hrms\EmployeeLeaveType;
use App\Models\Hrms\LeaveRequest;
use App\Models\User;
use App\Services\Hrms\Leave\LeaveTypeService;
use App\Services\Hrms\Leave\NotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RequestService
{
    public function __construct(
        protected CalculationService $calculationService,
        protected LeaveTypeService $leaveTypeService,
        protected AllowanceService $allowanceService,
        protected NotificationService $notificationService,
    ) {}

    public function approve(int $id, array $data): LeaveRequest {
        return DB::transaction(function () use ($id, $data) {
            $leaveRequest = LeaveRequest::findOrFail($id);
            $leaveRequest->update([
                'status' => 1,
                'approval_remark' => $data['remark'] ?? null,
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);
            $days = $this->calculationService->calculateDays($leaveRequest);
            $this->leaveTypeService->updateBalance($leaveRequest->employee_id, $leaveRequest->leave_type_id, $days, 'pending_remove');
            $this->leaveTypeService->updateBalance($leaveRequest->employee_id, $leaveRequest->leave_type_id, $days, 'used_add');
            if ($leaveRequest->leave_allowance) {$this->allowanceService->createRequest($leaveRequest->employee_id, $leaveRequest->id);}
            $this->notificationService->sendApprovalNotification($leaveRequest, $days, $data['message'] ?? null);
            return $leaveRequest->fresh();
        });
    }

    public function create(array $data): LeaveRequest
    {
        return DB::transaction(function () use ($data) {

            $employee = isset($data['employee_id']) ? Employee::findOrFail($data['employee_id']) : Employee::where('user_id', '=', auth('api')->id() ?? Auth::id())->firstOrFail();
            $employeeLeaveType = EmployeeLeaveType::findOrFail($data['leave_type_id']);
            $leaveRequest = LeaveRequest::create([
                'employee_id' => $employee->id,
                'user_leave_type_id' => $employeeLeaveType->id,
                'leave_type_id' => $employeeLeaveType->leave_type_id,
                'from_date' => $data['from_date'],
                'to_date' => $data['to_date'],
                'reason' => $data['reason'],
                'remarks' => $data['remarks'] ?? null,
                'status' => $data['status'] ?? 0,
                'leave_allowance' => $data['leave_allowance'] ?? 0,
                'is_half_day' => $data['is_half_day'] ?? 0,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            $days = $this->calculationService->calculateDays($leaveRequest);
            $this->leaveTypeService->updateBalance($employee->id, $leaveRequest->leave_type_id, $days, 'pending_add');
            $this->notificationService->sendLeaveRequestNotification($leaveRequest);
            $this->notificationService->sendSupervisorNotification($leaveRequest);

            return $leaveRequest->fresh();
        });
    }

    public function reject(int $id, array $data): LeaveRequest {
        return DB::transaction(function () use ($id, $data){
            $leaveRequest = LeaveRequest::findOrFail($id);
            $leaveRequest->update([
                'status' => 10,
                'approval_remark' => $data['remark'] ?? null,
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            $days = $this->calculationService->calculateDays($leaveRequest);
            $this->leaveTypeService->updateBalance($leaveRequest->employee_id, $leaveRequest->leave_type_id, $days, 'pending_remove');
            $this->notificationService->sendRejectionNotification($leaveRequest, $days, $data['message'] ?? null);
            return $leaveRequest->fresh();
        });
    }

    public function cancel(int $id): LeaveRequest
    {
        return DB::transaction(function () use ($id) {

            $leaveRequest = LeaveRequest::findOrFail($id);

            $leaveRequest->update([
                'status' => 2,
                'deleted_by' => auth()->id(),
                'deleted_at' => now(),
            ]);

            return $leaveRequest->fresh();
        });
    }

    public function find(int $id)
    {
        return LeaveRequest::with([
            'approver.user',
            'employee.user',
            'leave_type'
        ])->findOrFail($id);
    }

    public function getAll(string $type = 'all', ?array $filters = null, bool $detailed = false, bool $paginated = true) {
        $query = LeaveRequest::query();
        switch ($type) {
            case 'approved':
                $query->where('status', 1);
            break;
            case 'pending':
                $query->where('status', 0);
            break;
            case 'rejected':
                $query->where('status', 10);
            break;
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

            $query->whereIn(
                'employee_id',
                $employees
            );
        }

        if ($detailed) {

            $query->with([
                'employee.user',
                'leave_type',
                'approver'
            ]);
        }

        $query->latest();

        return $paginated
            ? $query->paginate(50)
            : $query->get();
    }

    public function update(array $data, int|string $id){
        return DB::transaction(function () use ($id, $data) {
            $leaveRequest = LeaveRequest::findOrFail($id);
            $old_days = $this->calculationService->calculateDays($leaveRequest);
            $this->leaveTypeService->updateBalance($leaveRequest->employee_id, $leaveRequest->leave_type_id, $old_days, 'pending_remove');
            
            $leaveRequest->update([
                'status' => 10,
                'approval_remark' => $data['remark'] ?? null,
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);
            $days = $this->calculationService->calculateDays($leaveRequest);
            $this->leaveTypeService->updateBalance($leaveRequest->employee_id, $leaveRequest->leave_type_id, $days, 'pending_add');
            
            return $leaveRequest;
        });
    }
}