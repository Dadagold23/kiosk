<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Services\ActivityLogService;
use App\Services\OpsAssistantService;
use App\Services\OrderFulfillmentService;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Order::class);

        $query = Order::with('user');

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('order_no', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('order_status')) {
            $query->where('order_status', $request->order_status);
        }

        $orders = $query->latest()->paginate(20)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order, OpsAssistantService $opsAssistantService)
    {
        $this->authorize('view', $order);

        $order->load('user', 'items.product', 'items.trackingEvents', 'payments', 'reviews.user', 'reviews.moderator');
        $assistantInsight = $opsAssistantService->analyzeOrder($order);

        return view('admin.orders.show', compact('order', 'assistantInsight'));
    }

    public function update(Request $request, Order $order, ActivityLogService $activityLogService)
    {
        $this->authorize('update', $order);
        
        $validated = $request->validate([
            'order_status' => ['required', 'in:pending,reviewing,processing,sourced,dispatched,delivered,cancelled'],
            'payment_status' => ['required', 'in:pending,paid,failed,cancelled,under_review'],
            'delivery_address' => ['nullable', 'string', 'max:2000'],
            'notes' => ['nullable', 'string', 'max:3000'],
        ]);

        $order->update($validated);

        $activityLogService->log(
            auth()->id(),
            'order_updated',
            Order::class,
            $order->id,
            'Updated order: ' . $order->order_no,
            [
                'order_status' => $validated['order_status'],
                'payment_status' => $validated['payment_status'],
            ]
        );

        return back()->with('success', 'Order updated successfully.');
    }

    public function updateItemTracking(
        Request $request,
        Order $order,
        OrderItem $item,
        ActivityLogService $activityLogService,
        OrderFulfillmentService $orderFulfillmentService
    ) {
        $this->authorize('update', $order);
        abort_unless($item->order_id === $order->id, 404);

        $validated = $request->validate([
            'fulfillment_status' => ['required', Rule::in(config('kiosk.orders.tracking_statuses', []))],
            'logistics_partner' => ['nullable', 'string', 'max:255'],
            'tracking_number' => ['nullable', 'string', 'max:255'],
            'tracking_url' => ['nullable', 'url', 'max:2048'],
            'location' => ['nullable', 'string', 'max:255'],
            'event_note' => ['nullable', 'string', 'max:2000'],
            'event_time' => ['nullable', 'date'],
        ]);

        $eventTime = filled($validated['event_time'] ?? null) ? Carbon::parse($validated['event_time']) : now();

        $attributes = [
            'fulfillment_status' => $validated['fulfillment_status'],
            'logistics_partner' => $validated['logistics_partner'] ?? null,
            'tracking_number' => $validated['tracking_number'] ?? null,
            'tracking_url' => $validated['tracking_url'] ?? null,
            'last_tracked_at' => $eventTime,
        ];

        if (
            in_array($validated['fulfillment_status'], [
                OrderItem::STATUS_READY_FOR_DISPATCH,
                OrderItem::STATUS_DISPATCHED,
                OrderItem::STATUS_IN_TRANSIT,
                OrderItem::STATUS_OUT_FOR_DELIVERY,
                OrderItem::STATUS_DELIVERED,
            ], true)
        ) {
            $attributes['shipped_at'] = $item->shipped_at ?? $eventTime;
        }

        if ($validated['fulfillment_status'] === OrderItem::STATUS_DELIVERED) {
            $attributes['delivered_at'] = $eventTime;
        }

        if ($validated['fulfillment_status'] !== OrderItem::STATUS_DELIVERED) {
            $attributes['delivered_at'] = null;
        }

        $item->update($attributes);

        $item->trackingEvents()->create([
            'status' => $validated['fulfillment_status'],
            'location' => $validated['location'] ?? null,
            'note' => $validated['event_note'] ?? null,
            'event_time' => $eventTime,
            'meta' => [
                'logistics_partner' => $validated['logistics_partner'] ?? null,
                'tracking_number' => $validated['tracking_number'] ?? null,
                'tracking_url' => $validated['tracking_url'] ?? null,
                'updated_by' => auth()->id(),
            ],
        ]);

        $orderFulfillmentService->syncOrderStatusFromItems($order);

        $activityLogService->log(
            auth()->id(),
            'order_item_tracking_updated',
            OrderItem::class,
            $item->id,
            'Updated tracking for order item: ' . $item->product_name,
            [
                'order_no' => $order->order_no,
                'status' => $validated['fulfillment_status'],
                'tracking_number' => $validated['tracking_number'] ?? null,
            ]
        );

        return back()->with('success', 'Order item tracking updated successfully.');
    }

}
