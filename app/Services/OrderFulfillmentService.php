<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;

class OrderFulfillmentService
{
    public function syncOrderStatusFromItems(Order $order): Order
    {
        $order->loadMissing('items');

        if ($order->items->isEmpty()) {
            return $order;
        }

        $statuses = $order->items->pluck('fulfillment_status')->filter()->values();

        $nextStatus = match (true) {
            $statuses->every(fn ($status) => $status === OrderItem::STATUS_DELIVERED) => Order::STATUS_DELIVERED,
            $statuses->contains(OrderItem::STATUS_OUT_FOR_DELIVERY) => Order::STATUS_DISPATCHED,
            $statuses->contains(OrderItem::STATUS_IN_TRANSIT), $statuses->contains(OrderItem::STATUS_DISPATCHED) => Order::STATUS_DISPATCHED,
            $statuses->contains(OrderItem::STATUS_READY_FOR_DISPATCH), $statuses->contains(OrderItem::STATUS_PACKED), $statuses->contains(OrderItem::STATUS_QUALITY_CHECK), $statuses->contains(OrderItem::STATUS_SOURCED) => Order::STATUS_SOURCED,
            $statuses->contains(OrderItem::STATUS_PROCESSING), $statuses->contains(OrderItem::STATUS_PROCUREMENT_IN_PROGRESS), $statuses->contains(OrderItem::STATUS_SUPPLIER_CONFIRMED), $statuses->contains(OrderItem::STATUS_PROCUREMENT_REVIEW) => Order::STATUS_PROCESSING,
            $statuses->contains(OrderItem::STATUS_CANCELLED) && $statuses->every(fn ($status) => $status === OrderItem::STATUS_CANCELLED) => Order::STATUS_CANCELLED,
            default => Order::STATUS_PENDING,
        };

        if ($order->order_status !== $nextStatus) {
            $order->update(['order_status' => $nextStatus]);
        }

        return $order->fresh();
    }
}
