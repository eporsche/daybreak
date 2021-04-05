<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\URL;
use Illuminate\Queue\SerializesModels;
use App\Models\LocationInvitation as LocationInvitationModel;
use Illuminate\Contracts\Queue\ShouldQueue;

class LocationInvitation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The team invitation instance.
     *
     * @var  \App\Models\LocationInvitationModel
     */
    public $invitation;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\LocationInvitationModel  $invitation
     * @return void
     */
    public function __construct(LocationInvitationModel $invitation)
    {
        $this->invitation = $invitation;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mail.location-invitation', ['acceptUrl' => URL::signedRoute('location-invitations.accept', [
            'invitation' => $this->invitation,
        ])]);
    }
}
