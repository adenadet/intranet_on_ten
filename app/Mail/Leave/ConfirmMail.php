<?php

namespace App\Mail\Leave;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConfirmMail extends Mailable
{
    use Queueable, SerializesModels;

    public $leave_request;
    public $employee;
    public $line_manager;
    public $days;

    public $message;

    public function __construct($leave_request, $employee, $line_manager, $days, $message)
    {
        $this->leave_request = $leave_request;
        $this->employee = $employee;
        $this->line_manager = $line_manager;
        $this->message = $message;
        $this->days = $days;
    }

    public function build()
    {
        return $this->subject('Leave Request Confirmed')
        ->view('mails.leaves.confirm');
    }
}
