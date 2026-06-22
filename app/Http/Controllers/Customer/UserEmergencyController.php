<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\EmergencyRequest;
use App\Services\EmergencyDirectoryService;
use App\Services\ModuleReviewService;

class UserEmergencyController extends Controller
{
    public function index()
    {
        $requests = EmergencyRequest::where('user_id', auth()->id())
            ->with(['assignedUnit', 'latestTrackingEvent'])
            ->latest()
            ->paginate(10);

        return view('customer.emergency.index', compact('requests'));
    }

    public function show(EmergencyRequest $emergencyRequest, ModuleReviewService $moduleReviewService)
    {
        abort_unless($emergencyRequest->user_id === auth()->id(), 403);

        $emergencyRequest->load([
            'assignedUnit',
            'trackingEvents.emergencyServiceUnit',
            'latestTrackingEvent',
        ]);
        $existingReview = $emergencyRequest->reviews()->where('user_id', auth()->id())->first();
        $canSubmitReview = $moduleReviewService->isEligible($emergencyRequest);

        return view('customer.emergency.show', compact('emergencyRequest', 'existingReview', 'canSubmitReview'));
    }

    public function tracking(EmergencyRequest $emergencyRequest, EmergencyDirectoryService $emergencyDirectoryService)
    {
        abort_unless($emergencyRequest->user_id === auth()->id(), 403);

        return response()->json(
            $emergencyDirectoryService->decorateTrackingPayload($emergencyRequest)
        );
    }
}
