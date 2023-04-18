<?php

namespace App\Mail\Setup;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Setup;

class SetupOrder extends Mailable
{
    use Queueable, SerializesModels;

    public $tries = 3;
    public $retryAfter = 60;

    public $setup;                               #Eloquent Model with all the information and relations about the loan
    public $status_id;                          #Whether the record was created or modified
    public $bookingType;                        #Whether the booking/reservation was created or modified
    public $bookingTitle;                       #Whether we are dealing with a booking or reservation

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Setup $setup, $isCreateOperation)
    {
        $this->setup = Setup::find($setup->id);
        $this->bookingTitle = "Setup";

        switch($setup->loan->status_id){
            case(3):
                #Setup
                $this->bookingType = $isCreateOperation ? "created" : "modified";
                break;
            case(4):
                #Reservation Cancelled
                $this->bookingType = "cancelled";
                break;
            case(5):
                #Booking Completed
                $this->bookingType = "completed";
                break;
        }

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
        return $this->view('emails.setup.order');
    }
}
