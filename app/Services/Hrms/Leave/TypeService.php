<?php

namespace App\Services\Hrms\Leave;

use App\Models\Hrms\LeaveType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LeaveTypeService
{
    public function create(array $data): LeaveType
    {
        return DB::transaction(function () use ($data) {

            return LeaveType::create([
                'name' => $data['name'],
                'no_of_days' => $data['no_of_days'],
                'leave_category' => $data['leave_category'] ?? 'Calendar',
                'status' => $data['status'] ?? 1,
                'start_date' => $data['start_date'] ?? now(),
                'end_date' => $data['end_date'],
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);
        });
    }

    public function update(array $data, int $id): LeaveType {
        return DB::transaction(function () use ($data, $id){
            $leaveType = LeaveType::findOrFail($id);
            $leaveType->update([
                'name' => $data['name'] ?? $leaveType->name,
                'no_of_days' => $data['no_of_days'] ?? $leaveType->no_of_days,
                'leave_category' => $data['leave_category'] ?? $leaveType->leave_category,
                'status' => $data['status'] ?? $leaveType->status,
                'start_date' => $data['start_date'] ?? $leaveType->start_date,
                'end_date' => $data['end_date'] ?? $leaveType->end_date,
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);

            return $leaveType->fresh();
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $leaveType = LeaveType::findOrFail($id);
            $leaveType->update([
                'deleted_by' => auth('api')->id() ?? Auth::id(),
                'deleted_at' => now(),
            ]);

            return true;
        });
    }
}