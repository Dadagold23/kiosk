<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmergencyRequest;
use App\Models\EmergencyRequest;
use App\Services\EmergencyDirectoryService;
use App\Services\ModuleReviewService;

class EmergencyController extends Controller
{
    public function index(EmergencyDirectoryService $emergencyDirectoryService, ModuleReviewService $moduleReviewService)
    {
        $emergencyTypes = config('kiosk.emergency.types', []);
        $directoryPayload = $emergencyDirectoryService->getFrontendPayload();
        $testimonials = $moduleReviewService->testimonialsFor('emergency');

        return view('frontend.emergency.index', compact('emergencyTypes', 'directoryPayload', 'testimonials'));
    }

    public function store(StoreEmergencyRequest $request)
    {
        $validated = $request->validated();

        EmergencyRequest::create([
            'user_id' => auth()->id(),
            'country_code' => $validated['country_code'],
            'country_name' => $validated['country_name'],
            'emergency_type' => $validated['emergency_type'],
            'full_name' => $validated['full_name'] ?? (auth()->user()->name ?? null),
            'phone' => $validated['phone'],
            'alternate_phone' => $validated['alternate_phone'] ?? null,
            'state_code' => $validated['state_code'] ?? null,
            'state_name' => $validated['state_name'],
            'local_government_area' => $validated['local_government_area'],
            'location_text' => $validated['location_text'],
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'description' => $validated['description'],
            'status' => EmergencyRequest::STATUS_PENDING,
        ]);

        return redirect()->route('emergency.index')
            ->with('success', 'Emergency request submitted successfully. Please also contact the nearest official emergency line immediately if the situation is critical.');
    }
}
