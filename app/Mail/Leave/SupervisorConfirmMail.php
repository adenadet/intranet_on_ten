<?php

namespace App\Mail\Leave;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SupervisorConfirmMail extends Mailable
{
    use Queueable, SerializesModels;

    use Queueable, SerializesModels;

    public $employee, $leave_request, $line_manager, $supervisor;
    public function __construct($leave_request, $employee, $supervisor, $line_manager)
    {
        $this->leave_request = $leave_request;
        $this->employee = $employee;
        $this->line_manager = $line_manager;
        $this->supervisor = $supervisor;
    }


    public function build()
    {
        return $this->subject('New Downline Leave Request Confirmed')->view('mails.leaves.supervisor_confirm_inform');
    }
}
