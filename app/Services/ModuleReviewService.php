<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\ConsultancyRequest;
use App\Models\EmergencyRequest;
use App\Models\ModuleReview;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ModuleReviewService
{
    public function resolveReviewable(string $type, string $record): Model
    {
        $modelClass = $this->modelClassForType($type);
        $model = new $modelClass();

        return $modelClass::query()
            ->where($model->getRouteKeyName(), $record)
            ->firstOrFail();
    }

    public function testimonialsFor(string $type, int $limit = 6): Collection
    {
        return ModuleReview::query()
            ->with('user')
            ->approved()
            ->where('reviewable_type', $this->modelClassForType($type))
            ->orderByDesc('is_featured')
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function isOwnedBy(Model $reviewable, User $user): bool
    {
        return (int) $reviewable->user_id === (int) $user->id;
    }

    public function isEligible(Model $reviewable): bool
    {
        return match (true) {
            $reviewable instanceof Order => $reviewable->order_status === Order::STATUS_DELIVERED
                && $reviewable->payment_status === Payment::STATUS_PAID,
            $reviewable instanceof ServiceRequest => in_array($reviewable->status, ['completed', 'closed'], true)
                || in_array($reviewable->progress_status, [ServiceRequest::TRACKING_COMPLETED, ServiceRequest::TRACKING_CLOSED], true),
            $reviewable instanceof ConsultancyRequest => in_array($reviewable->status, ['completed', 'closed', ConsultancyRequest::STATUS_DELIVERED], true)
                || filled($reviewable->report_file),
            $reviewable instanceof Booking => in_array($reviewable->status, ['completed', 'closed'], true),
            $reviewable instanceof EmergencyRequest => in_array($reviewable->status, [EmergencyRequest::STATUS_RESOLVED, EmergencyRequest::STATUS_CLOSED], true),
            default => false,
        };
    }

    private function modelClassForType(string $type): string
    {
        return match ($type) {
            'order' => Order::class,
            'service' => ServiceRequest::class,
            'consultancy' => ConsultancyRequest::class,
            'booking' => Booking::class,
            'emergency' => EmergencyRequest::class,
            default => abort(404),
        };
    }
}
