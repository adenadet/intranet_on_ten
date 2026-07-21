<?php

namespace App\Services\Hrms\Leave;

use App\Mail\Leave\ConfirmMail;
use App\Mail\Leave\RejectMail;
use App\Mail\Leave\RequestMail;
use App\Mail\Leave\SupervisorConfirmMail;
use App\Mail\Leave\SupervisorInfoMail;
use App\Models\Hrms\Employee;
use App\Models\Hrms\LeaveRequest;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    public function sendLeaveRequestNotification(
        LeaveRequest $leaveRequest
    ): void {

        $employee = Employee::with('user')
            ->find($leaveRequest->employee_id);

        $lineManager = Employee::where(
            'employee_id',
            $employee->reports_to
        )->with('user')->first();

        if (
            $lineManager &&
            $lineManager->email
        ) {

            Mail::to($lineManager->email)
                ->send(
                    new RequestMail(
                        $leaveRequest,
                        $employee,
                        $lineManager->user
                    )
                );
        }
    }

    public function sendSupervisorNotification(
        LeaveRequest $leaveRequest
    ): void {

        $employee = Employee::with('user')
            ->find($leaveRequest->employee_id);

        $supervisor = Employee::where(
            'employee_id',
            $employee->supervisor_id
        )->with('user')->first();

        $lineManager = Employee::where(
            'employee_id',
            $employee->reports_to
        )->with('user')->first();

        if (
            $supervisor &&
            $supervisor->email
        ) {

            Mail::to($supervisor->email)
                ->send(
                    new SupervisorInfoMail(
                        $leaveRequest,
                        $employee,
                        $supervisor->user,
                        $lineManager?->user
                    )
                );
        }
    }

    public function sendApprovalNotification(
        LeaveRequest $leaveRequest,
        float $days,
        ?string $message = null
    ): void {

        $employee = Employee::with('user')
            ->find($leaveRequest->employee_id);

        $lineManager = Employee::where(
            'user_id',
            auth()->id()
        )->with('user')->first();

        if ($employee && $employee->email) {

            Mail::to($employee->email)
                ->send(
                    new ConfirmMail(
                        $leaveRequest,
                        $employee->user,
                        $lineManager?->user,
                        $days,
                        $message
                    )
                );
        }

        $supervisor = Employee::where(
            'employee_id',
            $employee->supervisor_id
        )->with('user')->first();

        if ($supervisor && $supervisor->email) {

            Mail::to($supervisor->email)
                ->send(
                    new SupervisorConfirmMail(
                        $leaveRequest,
                        $employee,
                        $supervisor->user,
                        $lineManager?->user
                    )
                );
        }
    }

    public function sendRejectionNotification(
        LeaveRequest $leaveRequest,
        float $days,
        ?string $message = null
    ): void {

        $employee = Employee::with('user')
            ->find($leaveRequest->employee_id);

        $lineManager = Employee::where(
            'user_id',
            auth()->id()
        )->with('user')->first();

        if ($employee && $employee->email) {

            Mail::to($employee->email)
                ->send(
                    new RejectMail(
                        $leaveRequest,
                        $employee->user,
                        $lineManager?->user,
                        $days,
                        $message
                    )
                );
        }
    }
}