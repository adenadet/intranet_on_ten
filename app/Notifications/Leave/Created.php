<?php

namespace App\Notifications\Leave;

use App\Http\Traits\Hrms\LeaveTrait;
use App\Mail\Leave\ConfirmMail as LeaveConfirmMailable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class Created extends Notification implements ShouldQueue
{
    use Queueable;
    use LeaveTrait;
    private $data;
    private $employee;
    private $leave_request;
    private $line_manager;
    private $supervisor;

    public function __construct($data, $employee, $leave_request, $line_manager, $supervisor)
    {
        $this->data = $data;
        $this->employee = $employee;
        $this->leave_request = $leave_request;
        $this->line_manager = $line_manager;
        $this->supervisor = $supervisor;    
    }

    public function via(object $notifiable): array
    {
        return $notifiable->prefers_mail ? ['mail'] : ['mail', 'database'];
    }

    public function toMail(object $notifiable): Mailable
    {
        $days = $this->hrms_leave_request_number_of_days($this->leave_request);;
        return (new LeaveConfirmMailable(
            $this->leave_request, $this->employee, $this->line_manager, $days, $this->data
        ));
                    
    }

    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
