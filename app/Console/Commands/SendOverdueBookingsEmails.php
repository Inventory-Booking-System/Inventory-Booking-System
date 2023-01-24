<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Models\Loan;
use App\Models\User;
use App\Mail\Loan\LoanOverdue;

class SendOverdueBookingsEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'booking:send-overdue-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends emails for overdue bookings to users';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $loans = Loan::whereDate('end_date_time', '<=', Carbon::today()->subDays(1))
                        ->whereIn('status_id', [0, 2])->get();

        foreach($loans as $loan){
            $loan->status_id = 2;
            $loan->save();
            
            $user = User::find($loan->user_id);
            if (env('MAIL_CC_ADDRESS')) {
                Mail::to($user->email)->cc(env('MAIL_CC_ADDRESS'))->queue(new LoanOverdue($loan));
            } else {
                Mail::to($user->email)->queue(new LoanOverdue($loan));
            }
        }

        return Command::SUCCESS;
    }
}
