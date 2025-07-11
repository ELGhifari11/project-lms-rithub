<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function createInvoice(int $orderId): JsonResponse
    {
        $order = Order::with(['items', 'items.item', 'user'])
            ->findOrFail($orderId);

        try {
            if ($order->items->isEmpty()) {
                return response()->json(['status' => 'error', 'message' => 'Order does not have items.'], 404);
            }

            $items = $order->items->map(function ($item) {
                return [
                    'name' => $item->item->title ?? "Mentor " . $item->name,
                    'price' => $item->item->price,
                    'interval' => $item->interval,
                    'category' => $item->item->subCategory->category->name ?? 'Unknown',
                    'sub_category' => $item->item->subCategory->name ?? 'Unknown',
                    'quantity' => $item->amount,
                ];
            })->toArray();

            $itemNames = $order->items->map(function ($item) {
                return $item->item->title ?? '';
            })->filter()->implode(', ');

            $description = $itemNames ? 'Payment for: ' . $itemNames : 'Order payment #' . $orderId;

            $payload = [
                'external_id' => (string) $orderId,
                'amount' => $order->final_amount,
                'description' => $description,
                'customer' => [
                    'user_id' => $order->user->id,
                    'given_names' => $order->user->name ?? "",
                    'email' => $order->user->email,
                ],
                'customer_notification_preference' => [
                    'invoice_created' => ['email'],
                    'invoice_reminder' => ['email'],
                    'invoice_paid' => ['email'],
                ],
                'invoice_duration' => 86400,
                'currency' => 'IDR',
                'locale' => 'id',
                'items' => $items,
                'fees' => [
                    [
                        'type' => 'Platform Fee',
                        'value' => $order->total_admin_fee ?? 0,
                    ],
                    [
                        'type' => 'Discount',
                        'value' => -abs($order->discount_amount),
                    ]
                ],
            ];

            $response = Http::withBasicAuth(config('app.xendit.server_key'), ':')
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post('https://api.xendit.co/v2/invoices', $payload);

            if ($response->failed()) {
                Log::error('Invoice Creation Failed:', ['response' => $response->json()]);
                return response()->json([
                    'message' => 'Payment failed',
                    'error' => $response->json(),
                ], $response->status());
            }

            $invoiceUrl = $response['invoice_url'] ?? null;

            if (!$invoiceUrl) {
                return response()->json([
                    'error' => 'Invoice URL tidak tersedia dari Xendit',
                    'details' => $response->json(),
                ], 500);
            }

            $order->payment_url = $invoiceUrl;
            $order->save();

            return response()->json(["url" => $invoiceUrl], 200);
        } catch (\Exception $e) {
            Log::error('Exception saat membuat invoice:', ['message' => $e->getMessage()]);
            return response()->json([
                'error' => 'Terjadi kesalahan saat membuat invoice',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
