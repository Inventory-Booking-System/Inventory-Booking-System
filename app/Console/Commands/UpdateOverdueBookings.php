<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Loan;
use App\Models\User;

class UpdateOverdueBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'booking:update-overdue-bookings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates the database which bookings which are now overdue';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $loans = Loan::where('end_date_time', '<=', Carbon::now())
                        ->where('status_id', '=', '0')->get();

        foreach($loans as $loan){
            $loan->status_id = 2;
            $loan->save();
        }

        return Command::SUCCESS;
    }
}
