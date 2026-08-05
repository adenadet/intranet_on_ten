<?php
namespace App\Services\Hrms;

use App\Models\Hrms\Employee;
use App\Models\Hrms\LeaveAllowance;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AllowanceService{

    protected function userId(){
        return auth('api')->id() ?? Auth::id();
    }

    public function approve(array $data, int|string $id){
        return DB::transaction(function () use ($data, $id) {
            $query = LeaveAllowance::findOrFail($id);
            $query->update([
                'status' => $data['status'] ?? LeaveAllowance::StatusApproved,
                'amount' => $data['amount'] ?? NULL,
                'approved_by' => $data['approved_by'] ?? $this->userId(),
                'approved_at' => $data['approved_at'] ?? $this->userId(),
                'approval_remark' => $data['approval_remark'] ?? NULL,
                'updated_by' => $this->userId(),
            ]);

            return $query;
        });
    }

    public function create(array $data){
        return DB::transaction(function () use ($data) {
            $query = LeaveAllowance::create([
                'employee_id' => $data['employee_id'],
                'leave_request_id' => $data['leave_request_id'],
                'status' => $data['status'] ?? LeaveAllowance::StatusUnprocessed,
                'amount' => $data['amount'] ?? NULL,
                'approved_by' => $data['approved_by'] ?? NULL,
                'approved_at' => $data['approved_at'] ?? NULL,
                'approval_remark' => $data['approval_remark'] ?? NULL,
                'created_by' => $this->userId(),
                'updated_by' => $this->userId(),
            ]);

            return $query;
        });
    }

    public function update(array $data, int|string $id){
        return DB::transaction(function () use ($data, $id) {
            $query = LeaveAllowance::findOrFail($id);
            $query->update([
                'employee_id' => $data['employee_id'],
                'leave_request_id' => $data['leave_request_id'],
                'status' => $data['status'] ?? LeaveAllowance::StatusUnprocessed,
                'amount' => $data['amount'] ?? NULL,
                'approved_by' => $data['approved_by'] ?? NULL,
                'approved_at' => $data['approved_at'] ?? NULL,
                'approval_remark' => $data['approval_remark'] ?? NULL,
                'updated_by' => $this->userId(),
            ]);

            return $query;
        });
    }
}