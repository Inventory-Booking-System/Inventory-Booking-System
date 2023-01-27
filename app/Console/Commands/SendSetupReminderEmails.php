<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Models\Setup;
use App\Models\User;
use App\Mail\Setup\SetupReminder;

class SendSetupReminderEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'booking:send-setup-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email reminder when setups are close to starting';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $setups = Setup::select('setups.*')->join('loans', 'setups.loan_id', '=', 'loans.id')
                        ->whereDate('start_date_time', '=', Carbon::today())
                        ->whereTime('start_date_time', '<=', Carbon::now()->addMinutes(30))
                        ->where('email_reminder_sent', '=', 0)    
                        ->get();

        foreach($setups as $setup){
            $user = User::find($setup->loan->user_id);

            if (env('MAIL_CC_ADDRESS')) {
                Mail::to($user->email)->cc(env('MAIL_CC_ADDRESS'))->queue(new SetupReminder($setup));
            } else {
                Mail::to($user->email)->queue(new SetupReminder($setup));
            }

            $setup->email_reminder_sent = 1;
            $setup->save();
        }

        return Command::SUCCESS;
    }
}
