<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewAbsenceWaitingForApproval extends Mailable
{
    use Queueable, SerializesModels;

    protected $absence;

    protected $employee;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($absence, $employee)
    {
        $this->absence = $absence;
        $this->employee = $employee;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(__("New absence waiting for approval"))
            ->markdown('mail.absence-waiting-for-approval',[
                'employee' => $this->employee,
                'absence' => $this->absence
            ]);
    }
}
