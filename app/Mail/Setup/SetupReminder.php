<?php

namespace App\Mail\Setup;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Setup;

class SetupReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $setup;                              #Eloquent Model with all the information and relations about the loan
    public $bookingType;                        #Whether the booking/reservation was created or modified
    public $bookingTitle = 'Setup';                       #Whether we are dealing with a booking or reservation

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Setup $setup)
    {
        $this->setup = $setup;
        $this->bookingTitle = "Setup";
        $this->bookingType = "reminder";


        $emailSubject = $this->bookingTitle . " #" . $this->setup->id . " " . ucfirst($this->bookingType);
        $this->subject($emailSubject);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.setup.reminder');
    }
}
