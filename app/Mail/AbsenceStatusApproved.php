<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AbsenceStatusApproved extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;

    protected $absence;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $absence)
    {
        $this->absence = $absence;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(__("Absence Status Changed."))
            ->markdown('mail.absence-approved', [
                'employee' => $this->user,
                'absence' => $this->absence
            ]);
    }
}
