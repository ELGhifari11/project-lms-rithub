<?php

namespace App\Console\Commands;

use App\Models\UserSubscription;
use Illuminate\Console\Command;

class ExpireSubscriptions extends Command
{
    protected $signature = 'subscriptions:expire';

    protected $description = 'Expire subscriptions that are past their end date';

    public function handle()
    {
        $expiredSubscriptions = UserSubscription::where('status', 'active')
            ->where('end_date', '<=', now())
            ->get();

        $count = 0;

        foreach ($expiredSubscriptions as $expired) {
            $expired->update(['status' => 'expired']);
            $count++;
        }

        $this->info("{$count} Subscription(s) expired");

        return 0;
    }
}
