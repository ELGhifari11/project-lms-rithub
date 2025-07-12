<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Withdrawal;
use Exception;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProcessWithdrawalJob implements ShouldQueue, ShouldBeUnique
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    public $tries = 3;
    public $backoff = [60, 300, 900];
    public $timeout = 120;
    public $uniqueFor = 3600;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $withdrawalId)
    {
        $this->onQueue('withdrawals');
    }

    public function uniqueId(): string
    {
        if (!$this->withdrawalId) {
            throw new \RuntimeException('Withdrawal ID cannot be null');
        }
        return (string) $this->withdrawalId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            DB::beginTransaction();
            $withdrawal = Withdrawal::lockForUpdate()->findOrFail($this->withdrawalId);
            $mentor = User::lockForUpdate()->findOrFail($withdrawal->mentor_id);
            $wallet = $mentor->wallet;

            if ($withdrawal->status !== "PENDING") {
                Log::info('Withdrawal already processed or not in PENDING status.', [
                    'withdrawal_id' => $withdrawal->id,
                    'current_status' => $withdrawal->status
                ]);
                DB::rollBack();
                return;
            }

            if ($wallet->balance < $withdrawal->amount) {
                $withdrawal->update([
                    'status' => 'FAILED',
                    'failure_code' => 'INSUFFICIENT_BALANCE',
                    'processed_at' => now(),
                ]);

                DB::commit();

                Log::warning('Withdrawal failed due to insufficient balance (from job)', [
                    'withdrawal_id' => $withdrawal->id,
                    'user_id' => $withdrawal->mentor->id,
                    'amount' => $withdrawal->amount
                ]);

                return;
            }

            $withdrawal->update([
                'status' => 'PROCESSING',
                'processed_at' => now(),
            ]);

            $wallet->balance -= $withdrawal->amount;
            $wallet->save();

            $success = $this->processWithdrawal($withdrawal);

            if (!$success) throw new Exception("Disbursement failed");

            DB::commit();

            Log::info('Withdrawal processing job completed successfully', [
                'withdrawal_id' => $withdrawal->id,
                'user_id' => $withdrawal->mentor->id,
                'amount' => $withdrawal->amount
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error processing withdrawal job', [
                'withdrawal_id' => $this->withdrawalId,
                'attempt' => $this->attempts(),
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    private function processWithdrawal(Withdrawal $withdrawal): bool
    {
        $data = [
            'external_id' => (string) $withdrawal->id,
            'amount' => (int) $withdrawal->amount,
            'bank_code' => $withdrawal->wallet->bank_name,
            'account_holder_name' => $withdrawal->wallet->account_holder_name,
            'account_number' => $withdrawal->wallet->bank_account_number,
            'description' => 'Mentor Withdrawal - ID: ' . $withdrawal->id,
            'email_to' => [$withdrawal->mentor->email ?? ''],
            'email_cc' => [],
            'email_bcc' => [],
        ];

        $response = Http::timeout(30)
            ->withBasicAuth(config("app.xendit.server_key"), ':')
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->post('https://api.xendit.co/disbursements', $data);

        if (!$response->successful()) {
            Log::error('Xendit disbursement API error', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return false;
        }

        return true;
    }

    public function failed(Exception $exception): void
    {
        Log::error('Withdrawal job failed permanently', [
            'withdrawal_id' => $this->withdrawalId,
            'attempts' => $this->attempts(),
            'error' => $exception->getMessage()
        ]);

        try {
            DB::beginTransaction();

            $withdrawal = Withdrawal::findOrFail($this->withdrawalId);
            $mentor = $withdrawal->mentor;
            if (!$withdrawal) {
                DB::rollBack();
                return;
            }

            $withdrawal->update([
                'status' => 'FAILED',
                'failure_code' => 'JOB_FAILED_AFTER_RETRIES',
            ]);

            $mentor->wallet->balance += $withdrawal->amount;
            Log::info('Balance refunded due to permanent job failure (from failed handler)', [
                'withdrawal_id' => $withdrawal->id,
                'amount' => $withdrawal->amount,
                'original_status' => $withdrawal->getOriginal('status')
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to handle job failure (rollback error or other issues)', [
                'withdrawal_id' => $this->withdrawalId,
                'error' => $e->getMessage()
            ]);
        }
    }
}
