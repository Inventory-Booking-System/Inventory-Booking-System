<?php

namespace App\Mail\Loan;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Loan;

class LoanOrder extends Mailable
{
    use Queueable, SerializesModels;

    public $loan;                               #Eloquent Model with all the information and relations about the loan
    public $status_id;                          #Whether the record was created or modified
    public $bookingType;                        #Whether the booking/reservation was created or modified
    public $bookingTitle;                       #Whether we are dealing with a booking or reservation

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Loan $loan, $status, $reservation = false)
    {
        $this->loan = $loan;

        switch($loan->status_id){
            case(0):
                #Booking
                $this->bookingTitle = "Booking";
                if($reservation){
                    $this->bookingType = "created";
                }else{
                    $this->bookingType = $status ? "created" : "modified";
                }
                break;
            case(1):
                #Reservation
                $this->bookingTitle = "Reservation";
                $this->bookingType = $status ? "created" : "modified";
                break;
            case(2):
                #Overdue Booking
                $this->bookingTitle = "Booking";
                $this->bookingType = "Overdue";
                break;
            case(3):
                #Setup
                $this->bookingTitle = "Setup";
                $this->bookingType = $status ? "created" : "modified";
                break;
            case(4):
                #Reservation Cancelled
                $this->bookingTitle = "Reservation";
                $this->bookingType = "cancelled";
                break;
            case(5):
                #Booking Completed
                $this->bookingTitle = "Booking";
                $this->bookingType = "completed";
                break;
        }

        $emailSubject = $this->bookingTitle . " #" . $this->loan->id . " " . ucfirst($this->bookingType);
        $this->subject($emailSubject);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.loan.order');
    }
}
