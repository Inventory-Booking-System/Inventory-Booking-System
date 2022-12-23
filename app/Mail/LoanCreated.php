<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Loan;

class LoanCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $loan;
    public $newRecord;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Loan $loan, $newRecord)
    {
        $this->loan = $loan;
        $this->newRecord = $newRecord;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.created');
    }
}
