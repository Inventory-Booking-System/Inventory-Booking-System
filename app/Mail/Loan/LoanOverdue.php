<?php

namespace App\Mail\Loan;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Loan;

class LoanOverdue extends Mailable
{
    use Queueable, SerializesModels;

    public $loan;                               #Eloquent Model with all the information and relations about the loan
    public $bookingType;                        #Whether the booking/reservation was created or modified
    public $bookingTitle;                       #Whether we are dealing with a booking or reservation

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Loan $loan)
    {
        $this->loan = $loan;
        $this->bookingTitle = "Booking";
        $this->bookingType = "overdue";

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
        return $this->view('emails.loan.overdue');
    }
}
