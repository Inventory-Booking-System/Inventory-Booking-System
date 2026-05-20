<?php

namespace App\Console\Commands;

use App\Models\DistributionGroupUser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RemoveExpiredGroupMemberships extends Command
{
    protected $signature = 'groups:remove-expired';

    protected $description = 'Remove distribution group memberships that have passed their expiry date';

    public function handle(): int
    {
        $expired = DistributionGroupUser::whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->get();

        $count = $expired->count();

        $expired->each(function ($membership) {
            $membership->delete();
        });

        Log::info("Removed {$count} expired group membership(s).");
        $this->info("Removed {$count} expired group membership(s).");

        return self::SUCCESS;
    }
}
