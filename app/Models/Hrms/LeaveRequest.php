<?php

namespace App\Models\Hrms;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Structure;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LeaveRequest extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'hrms_leave_requests';
    protected $fillable = array('employee_id', 'user_leave_type_id', 'leave_type_id', 'leave_allowance', 'from_date', 'to_date', 'reason', 'remarks', 'status', 'is_half_day', 'leave_attachment', 'approved_by', 'approved_at', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');
    protected $appends = ['leave_days'];

    public const StatusPending = 1;
    public const StatusApproved = 5;
    public const StatusCancelled = 10;
    
    public function approver(){
        return $this->belongsTo('App\Models\Hrms\Employee', 'approved_by', 'employee_id');
    }
    public function employee(){
        return $this->belongsTo('App\Models\Hrms\Employee', 'employee_id', 'id');
    }

    public function employee_leave_type(){
        return $this->belongsTo('App\Models\Hrms\EmployeeLeaveType', 'user_leave_type_id', 'id');
    }

    public function leave_type(){
        return $this->belongsTo('App\Models\Hrms\LeaveType', 'leave_type_id', 'id');
    }

    public function getLeaveDaysAttribute()
    {
        if (!$this->from_date || !$this->to_date || !$this->leave_type) {
            return 0;
        }

        $startDate = Carbon::parse($this->from_date);
        $endDate = Carbon::parse($this->to_date);
        
        if ($this->leave_type->leave_category === 'Calendar') {
            // Count all days including weekends
            return $startDate->diffInDays($endDate) + 1;
        }
        
        if ($this->leave_type->leave_category === 'Working') {
            // Count working days only
            return $this->calculateWorkingDays($startDate, $endDate);
        }

        return 0;
    }

    // Helper method to calculate working days
    protected function calculateWorkingDays(Carbon $startDate, Carbon $endDate)
    {
        $days = 0;
        $current = $startDate->copy();
        
        // Get public holidays in the date range
        $publicHolidays = DB::table('hrms_public_holidays')
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->pluck('date')
            ->map(fn($date) => Carbon::parse($date)->format('Y-m-d'))
            ->toArray();

        while ($current->lte($endDate)) {
            // Check if it's not a weekend (Saturday = 6, Sunday = 0)
            $isWeekend = $current->dayOfWeek === Carbon::SATURDAY || $current->dayOfWeek === Carbon::SUNDAY;
            
            // Check if it's not a public holiday
            $isPublicHoliday = in_array($current->format('Y-m-d'), $publicHolidays);

            if (!$isWeekend && !$isPublicHoliday) {
                $days++;
            }

            $current->addDay();
        }

        return $days;
    }
}
