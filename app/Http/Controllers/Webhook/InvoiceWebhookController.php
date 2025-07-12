<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\CommissionEarning;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InvoiceWebhookController extends Controller
{
    public function invoiceHandler(Request $request): JsonResponse
    {
        $externalId = $request->input('external_id');
        $status = $request->input('status');
        $paymentMethod = $request->input('payment_method');
        $paymentProvider = $request->input('payment_channel');
        $order = Order::findOrFail($externalId);

        try {
            if ($status === 'PAID') {
                $order->update([
                    'payment_method' => $paymentMethod,
                    'payment_provider' => $paymentProvider,
                    'status' => $status
                ]);

                $this->addToSubscription($order);

                $this->calculateCommision($order);

                return response()->json([
                    'message' => 'Order paid successfully',
                ], 200);
            }

            $order->update(['status' => $status]);
            return response()->json([
                'message' => 'Order status updated to {$status}'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error processing paid order:', [
                'order_id' => $externalId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'An error occurred while processing the order',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    public function calculateCommision(Order $order): bool
    {
        try {
            $order->load('items.item');

            $itemObject = $order->items->first();

            if (!$itemObject) {
                Log::warning('Item not found', [
                    'item_id' => $order->items->first()->id,
                    'item_type' => $order->items->first()->item_type
                ]);
                return false;
            }

            $mentor = null;

            if ($order->items->first()->item_type === User::class) {
                $mentor = $itemObject;
            } elseif (isset($itemObject->item->mentor_id)) {
                $mentor = User::find($itemObject->item->mentor_id);
            }

            if (!$mentor) {
                Log::warning('Mentor not found for item', [
                    'item_id' => $order->items->first()->id,
                    'item_type' => $order->items->first()->item_type
                ]);
                return false;
            }

            $mentor->load('wallet');

            if (!$mentor->wallet) {
                Log::warning('Mentor has no wallet', ['mentor_id' => $mentor->id]);
                return false;
            }

            $price = data_get($itemObject, 'price', 0);
            if ($price <= 0) {
                Log::info('info: ', ['price' => $price, 'item' => $itemObject]);
            }

            $commission = $price - $order->total_admin_fee;

            $commissionEarning = CommissionEarning::create([
                'mentor_id' => $mentor->id,
                'order_id' => $order->id,
                'amount' => $commission,
                'status' => 'success'
            ]);

            if (!$commissionEarning) {
                Log::info('Failed to create commission earning record', [
                    'mentor_id' => $mentor->id,
                    'order_id' => $order->id,
                    'amount' => $commission
                ]);
                return false;
            }

            $wallet = $mentor->wallet;
            if ($wallet) {
                $wallet->balance += $commission;
                $wallet->save();
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Error processing single item commission:', [
                'item_id' => $order->items->first()->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }

    public function addToSubscription(Order $order): bool
    {
        try {
            $order->load(['items.item']);

            if (!$order->items || $order->items->isEmpty()) {
                Log::warning('Cannot add subscription: Order has no items', [
                    'order_id' => $order->id
                ]);
                return false;
            }

            $item = $order->items->first();
            $userSubscription = UserSubscription::create([
                'user_id' => $order->user_id,
                'order_id' => $order->id,
                'interval' => $item->interval,
                'start_date' => now(),
                'end_date' => $this->calculateEndDate($item->interval, $item->amount),
                'status' => 'active'
            ]);

            $enrollableType = $item->item_type;
            $enrollableId = $item->item_id;

            if (!in_array($enrollableType, [ClassModel::class, User::class])) {
                Log::warning('Unsupported enrollable type', ['type' => $enrollableType]);
                return false;
            }

            $enrollment = Enrollment::create([
                'user_id' => $order->user_id,
                'subscription_id' => $userSubscription->id,
                'enrollable_type' => $enrollableType,
                'enrollable_id' => $enrollableId,
            ]);

            Log::info('enrollment created: ', [
                'enrollment' => $enrollment,
                'order' => $order,
                'order-item' => $item
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error adding subscription:', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }

    public function calculateEndDate(string $interval, int $amount)
    {
        switch ($interval) {
            case 'monthly':
                return now()->addMonths($amount);
                break;
            case 'annualy':
                return now()->addYears($amount);
                break;
            case 'lifetime':
                return null;
                break;

            default:
                return now();
        }
    }
}
