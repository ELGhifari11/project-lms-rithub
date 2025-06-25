<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessWithdrawalJob;
use App\Models\Withdrawal;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class DisbursementController extends Controller
{
    public function triggerWithdrawalProcessing(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'withdrawal_id' => 'required|integer|exists:withdrawals,id'
            ]);

            $withdrawal = Withdrawal::findOrFail($validated['withdrawal_id']);

            if (!$withdrawal) {
                throw new Exception("Withdrawal record with ID {$withdrawal->id} not found.");
            }

            if ($withdrawal->status !== 'PENDING') {
                Log::warning('Attempted to dispatch a non-pending withdrawal.', [
                    'withdrawal_id' => $withdrawal->id,
                    'status' => $withdrawal->status
                ]);
                throw new Exception('Withdrawal is not in PENDING status.');
            }

            // Masukan ke antrean
            ProcessWithdrawalJob::dispatch($withdrawal->id);

            return response()->json([
                'success' => true,
                'message' => 'Withdrawal processing request dispatched successfully.',
                'data' => [
                    'withdrawal_id' => $withdrawal->id,
                    'status' => $withdrawal->status,
                ]
            ], 200);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to trigger withdrawal processing', [
                'request_data' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
