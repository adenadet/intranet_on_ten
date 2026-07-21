<?php

namespace App\Services\Hrms\Leave;

use App\Models\Hrms\LeaveRequest;
use App\Models\Hrms\LeaveType;
use App\Models\Hrms\PublicHoliday;
use Carbon\Carbon;
use DateTime;

class CalculationService
{
    public function calculateDays(LeaveRequest $leaveRequest): float
    {
        $leaveType = LeaveType::findOrFail($leaveRequest->leave_type_id);

        return match ($leaveType->leave_category) {
            'Working' => $this->calculateWorkingDays(
                Carbon::parse($leaveRequest->from_date),
                Carbon::parse($leaveRequest->to_date)
            ),

            default => $this->calculateCalendarDays(
                Carbon::parse($leaveRequest->from_date),
                Carbon::parse($leaveRequest->to_date),
                $leaveRequest->is_half_day
            ),
        };
    }

    public function calculateWorkingDays(
        Carbon $start,
        Carbon $end
    ): int {

        $publicHolidays = PublicHoliday::whereBetween('date', [
            $start->format('Y-m-d'),
            $end->format('Y-m-d')
        ])->pluck('date')->toArray();

        $workdays = 0;

        while ($start <= $end) {

            $isWeekend = $start->isSaturday() || $start->isSunday();

            $isHoliday = in_array(
                $start->format('Y-m-d'),
                $publicHolidays
            );

            if (!$isWeekend && !$isHoliday) {
                $workdays++;
            }

            $start->addDay();
        }

        return $workdays;
    }

    public function calculateCalendarDays(
        Carbon $start,
        Carbon $end,
        bool $halfDay = false
    ): float {

        $days = $start->diffInDays($end) + 1;

        return $halfDay ? ($days / 2) : $days;
    }
}