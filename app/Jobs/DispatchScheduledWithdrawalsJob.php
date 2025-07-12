<?php

namespace App\Jobs;

use App\Models\Withdrawal;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DispatchScheduledWithdrawalsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $timeout = 300;
    public int $batchSize = 50;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->onQueue('scheduler');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $dueWithdrawals = Withdrawal::where('status', 'PENDING')
                ->where('requested_at', '<=', now())
                ->limit(50)
                ->get();

            Log::info('Dispatching scheduled withdrawals', [
                'count' => $dueWithdrawals->count(),
                'timestamp' => now()
            ]);

            if ($dueWithdrawals->isEmpty()) {
                Log::info('No pending withdrawals found');
                return;
            }

            //  $delayInSeconds = 0;

            foreach ($dueWithdrawals as $withdrawal) {
                ProcessWithdrawalJob::dispatch($withdrawal->id);
                    // ->delay(now()->addMilliseconds($delayInSeconds));

                // $delayInSeconds += 500;
            }

            Log::info('All scheduled withdrawal jobs dispatched', [
                'total_count' => $dueWithdrawals->count(),
                // 'max_delay' => $delayInSeconds . 's'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to dispatch scheduled withdrawals', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
