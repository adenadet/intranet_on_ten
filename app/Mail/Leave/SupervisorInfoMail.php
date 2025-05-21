<?php

namespace App\Mail\Leave;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SupervisorInfoMail extends Mailable
{
    use Queueable, SerializesModels;

    use Queueable, SerializesModels;

    #public $leave_request;
    #public $employee;
    #public $line_manager;

    public $supervisor, $line_manager, $leave_request, $employee;

    public function __construct($leave_request, $employee, $supervisor, $line_manager)
    {
        $this->leave_request = $leave_request;
        $this->employee = $employee;
        $this->line_manager = $line_manager;
        $this->supervisor = $supervisor;
    }


    public function build()
    {
        return $this->subject('New Downline Leave Request')
        ->view('mails.leaves.supervisor_inform');
    }
}
