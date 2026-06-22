<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ServiceRequest;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class OpsAssistantService
{
    public function buildDashboardInsights(Collection $orders, Collection $services): array
    {
        $orderInsights = $orders->map(fn (Order $order) => $this->analyzeOrder($order));
        $serviceInsights = $services->map(fn (ServiceRequest $service) => $this->analyzeServiceRequest($service));

        $criticalOrders = $orderInsights
            ->filter(fn (array $insight) => $insight['risk_level'] !== 'low')
            ->sortByDesc(fn (array $insight) => $this->riskWeight($insight['risk_level']))
            ->take(4)
            ->values();

        $criticalServices = $serviceInsights
            ->filter(fn (array $insight) => $insight['risk_level'] !== 'low')
            ->sortByDesc(fn (array $insight) => $this->riskWeight($insight['risk_level']))
            ->take(4)
            ->values();

        return [
            'headline' => $this->headline($criticalOrders, $criticalServices),
            'critical_orders' => $criticalOrders,
            'critical_services' => $criticalServices,
        ];
    }

    public function analyzeOrder(Order $order): array
    {
        $order->loadMissing('items.trackingEvents');
        $items = $order->items;

        $itemInsights = $items->map(function (OrderItem $item) {
            $hours = $this->orderEtaHours($item->fulfillment_status);
            $baseTime = $item->last_tracked_at ?: $item->created_at ?: now();
            $etaAt = $hours === null ? null : $baseTime->copy()->addHours($hours);
            $hoursSinceUpdate = $baseTime ? now()->diffInHours($baseTime) : 0;
            $riskLevel = $this->orderRiskLevel($item->fulfillment_status, $hoursSinceUpdate);

            return [
                'item_id' => $item->id,
                'product_name' => $item->product_name,
                'status' => $item->fulfillment_status,
                'eta_at' => $etaAt,
                'eta_label' => $etaAt ? $this->etaLabel($etaAt) : 'Completed',
                'hours_since_update' => $hoursSinceUpdate,
                'risk_level' => $riskLevel,
                'next_action' => $this->orderNextAction($item->fulfillment_status, $riskLevel),
            ];
        })->values();

        $priorityItem = $itemInsights
            ->sortByDesc(fn (array $insight) => $this->riskWeight($insight['risk_level']))
            ->first();

        $overallEtaAt = $itemInsights
            ->pluck('eta_at')
            ->filter()
            ->sort()
            ->last();

        return [
            'reference' => $order->order_no,
            'customer_name' => $order->user?->name,
            'status' => $order->order_status,
            'eta_at' => $overallEtaAt,
            'eta_label' => $overallEtaAt ? $this->etaLabel($overallEtaAt) : 'Delivered / closed',
            'risk_level' => $priorityItem['risk_level'] ?? 'low',
            'summary' => $priorityItem
                ? "{$priorityItem['product_name']} is currently in ".str_replace('_', ' ', $priorityItem['status'])."."
                : 'No item-level tracking insight available yet.',
            'next_action' => $priorityItem['next_action'] ?? 'Continue monitoring order fulfillment.',
            'item_insights' => $itemInsights,
        ];
    }

    public function analyzeServiceRequest(ServiceRequest $serviceRequest): array
    {
        $serviceRequest->loadMissing('trackingEvents');

        $progressStatus = $serviceRequest->progress_status ?: ServiceRequest::TRACKING_REQUEST_RECEIVED;
        $referenceTime = $serviceRequest->tracking_updated_at
            ?: $serviceRequest->service_window_start
            ?: $serviceRequest->created_at
            ?: now();

        $hours = $this->serviceEtaHours($progressStatus);
        $etaAt = $hours === null ? $serviceRequest->completed_at : $referenceTime->copy()->addHours($hours);
        $hoursSinceUpdate = $referenceTime ? now()->diffInHours($referenceTime) : 0;
        $riskLevel = $this->serviceRiskLevel($progressStatus, $hoursSinceUpdate, $serviceRequest);

        return [
            'reference' => '#SR-'.$serviceRequest->id,
            'title' => $serviceRequest->title,
            'status' => $serviceRequest->status,
            'progress_status' => $progressStatus,
            'eta_at' => $etaAt,
            'eta_label' => $etaAt ? $this->etaLabel($etaAt) : 'Completed / closed',
            'risk_level' => $riskLevel,
            'summary' => "Service request is currently in ".str_replace('_', ' ', $progressStatus).'.',
            'next_action' => $this->serviceNextAction($progressStatus, $riskLevel),
        ];
    }

    private function orderEtaHours(string $status): ?int
    {
        return match ($status) {
            OrderItem::STATUS_PENDING => 72,
            OrderItem::STATUS_PROCUREMENT_REVIEW => 60,
            OrderItem::STATUS_SUPPLIER_CONFIRMED => 48,
            OrderItem::STATUS_PROCUREMENT_IN_PROGRESS => 36,
            OrderItem::STATUS_PROCESSING => 24,
            OrderItem::STATUS_SOURCED => 18,
            OrderItem::STATUS_QUALITY_CHECK => 12,
            OrderItem::STATUS_PACKED => 10,
            OrderItem::STATUS_READY_FOR_DISPATCH => 8,
            OrderItem::STATUS_DISPATCHED => 6,
            OrderItem::STATUS_IN_TRANSIT => 4,
            OrderItem::STATUS_OUT_FOR_DELIVERY => 2,
            OrderItem::STATUS_FAILED_DELIVERY => 24,
            OrderItem::STATUS_RETURNED => 72,
            OrderItem::STATUS_DELIVERED, OrderItem::STATUS_CANCELLED => null,
            default => 24,
        };
    }

    private function orderRiskLevel(string $status, int $hoursSinceUpdate): string
    {
        return match (true) {
            in_array($status, [OrderItem::STATUS_FAILED_DELIVERY, OrderItem::STATUS_RETURNED], true) => 'high',
            $hoursSinceUpdate >= 48 => 'high',
            $hoursSinceUpdate >= 24 => 'medium',
            default => 'low',
        };
    }

    private function orderNextAction(string $status, string $riskLevel): string
    {
        if ($riskLevel === 'high') {
            return 'Escalate to procurement/logistics desk and push a customer-facing update immediately.';
        }

        return match ($status) {
            OrderItem::STATUS_PENDING, OrderItem::STATUS_PROCUREMENT_REVIEW => 'Confirm procurement owner and supplier readiness.',
            OrderItem::STATUS_SUPPLIER_CONFIRMED, OrderItem::STATUS_PROCUREMENT_IN_PROGRESS => 'Check supplier lead time and verify stock movement.',
            OrderItem::STATUS_PROCESSING, OrderItem::STATUS_SOURCED => 'Prepare QC and packaging so dispatch is not blocked.',
            OrderItem::STATUS_QUALITY_CHECK, OrderItem::STATUS_PACKED, OrderItem::STATUS_READY_FOR_DISPATCH => 'Assign rider or courier and publish tracking number.',
            OrderItem::STATUS_DISPATCHED, OrderItem::STATUS_IN_TRANSIT => 'Monitor route progress and update delivery ETA.',
            OrderItem::STATUS_OUT_FOR_DELIVERY => 'Notify customer that final handoff is underway.',
            default => 'Continue monitoring this order item.',
        };
    }

    private function serviceEtaHours(string $status): ?int
    {
        return match ($status) {
            ServiceRequest::TRACKING_REQUEST_RECEIVED => 48,
            ServiceRequest::TRACKING_PAYMENT_CONFIRMED => 36,
            ServiceRequest::TRACKING_UNDER_REVIEW => 24,
            ServiceRequest::TRACKING_TEAM_ASSIGNED => 12,
            ServiceRequest::TRACKING_VISIT_SCHEDULED => 8,
            ServiceRequest::TRACKING_EN_ROUTE => 3,
            ServiceRequest::TRACKING_ON_SITE => 2,
            ServiceRequest::TRACKING_IN_PROGRESS => 6,
            ServiceRequest::TRACKING_AWAITING_PARTS => 24,
            ServiceRequest::TRACKING_QUALITY_CHECK => 4,
            ServiceRequest::TRACKING_COMPLETED, ServiceRequest::TRACKING_CLOSED => null,
            default => 24,
        };
    }

    private function serviceRiskLevel(string $status, int $hoursSinceUpdate, ServiceRequest $serviceRequest): string
    {
        return match (true) {
            $status === ServiceRequest::TRACKING_AWAITING_PARTS => 'high',
            $serviceRequest->service_window_end && now()->greaterThan($serviceRequest->service_window_end) && ! in_array($status, [ServiceRequest::TRACKING_COMPLETED, ServiceRequest::TRACKING_CLOSED], true) => 'high',
            $hoursSinceUpdate >= 36 => 'high',
            $hoursSinceUpdate >= 18 => 'medium',
            default => 'low',
        };
    }

    private function serviceNextAction(string $status, string $riskLevel): string
    {
        if ($riskLevel === 'high') {
            return 'Raise a service escalation, contact the assigned team, and push a revised ETA to the customer.';
        }

        return match ($status) {
            ServiceRequest::TRACKING_REQUEST_RECEIVED, ServiceRequest::TRACKING_PAYMENT_CONFIRMED => 'Move the request through review and assign an execution owner.',
            ServiceRequest::TRACKING_UNDER_REVIEW => 'Approve scope, confirm parts, and assign field staff.',
            ServiceRequest::TRACKING_TEAM_ASSIGNED, ServiceRequest::TRACKING_VISIT_SCHEDULED => 'Confirm visit window and notify the customer before dispatch.',
            ServiceRequest::TRACKING_EN_ROUTE => 'Monitor technician arrival and keep customer informed.',
            ServiceRequest::TRACKING_ON_SITE, ServiceRequest::TRACKING_IN_PROGRESS => 'Track work completion and update next checkpoint.',
            ServiceRequest::TRACKING_QUALITY_CHECK => 'Run final quality verification and close the ticket if successful.',
            default => 'Continue monitoring this service request.',
        };
    }

    private function etaLabel(CarbonInterface $etaAt): string
    {
        $minutes = now()->diffInMinutes($etaAt, false);

        if ($minutes <= 0) {
            return 'Due now';
        }

        if ($minutes < 60) {
            return "About {$minutes} min";
        }

        if ($minutes < 1440) {
            return 'About '.ceil($minutes / 60).' hrs';
        }

        return 'About '.ceil($minutes / 1440).' days';
    }

    private function headline(Collection $criticalOrders, Collection $criticalServices): string
    {
        $highRiskOrders = $criticalOrders->where('risk_level', 'high')->count();
        $highRiskServices = $criticalServices->where('risk_level', 'high')->count();

        return match (true) {
            $highRiskOrders + $highRiskServices === 0 => 'Operations are stable. Focus on proactive ETA updates and keeping tracking fresh.',
            $highRiskOrders >= $highRiskServices => "Order fulfillment needs attention: {$highRiskOrders} high-risk shipment(s) need escalation.",
            default => "Service delivery needs attention: {$highRiskServices} high-risk request(s) need reassignment or ETA correction.",
        };
    }

    private function riskWeight(string $riskLevel): int
    {
        return match ($riskLevel) {
            'high' => 3,
            'medium' => 2,
            default => 1,
        };
    }
}
