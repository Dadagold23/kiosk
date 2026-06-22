<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmergencyRequest;
use App\Models\EmergencyServiceUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class EmergencyController extends Controller
{
    public function index(Request $request)
    {
        $query = EmergencyRequest::with(['user', 'assignedUnit', 'latestTrackingEvent']);

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('location_text', 'like', "%{$search}%")
                ->orWhere('assigned_unit', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('emergency_type')) {
            $query->where('emergency_type', $request->emergency_type);
        }

        if ($request->filled('state_name')) {
            $query->where('state_name', $request->state_name);
        }

        $requests = $query->latest()->paginate(20)->withQueryString();
        $states = EmergencyServiceUnit::query()
            ->whereNotNull('state_name')
            ->orderBy('state_name')
            ->distinct()
            ->pluck('state_name');

        return view('admin.emergency.index', compact('requests', 'states'));
    }

    public function show(EmergencyRequest $emergencyRequest)
    {
        $emergencyRequest->load([
            'user',
            'assignedUnit',
            'trackingEvents.emergencyServiceUnit',
            'latestTrackingEvent',
            'reviews.user',
            'reviews.moderator',
        ]);

        $availableUnits = EmergencyServiceUnit::query()
            ->where('is_national', true)
            ->orWhere('state_name', $emergencyRequest->state_name)
            ->orderByDesc('is_national')
            ->orderBy('unit_name')
            ->get();

        return view('admin.emergency.show', compact('emergencyRequest', 'availableUnits'));
    }

    public function update(Request $request, EmergencyRequest $emergencyRequest)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(config('kiosk.emergency.statuses', []))],
            'assigned_unit_id' => ['nullable', 'exists:emergency_service_units,id'],
            'response_note' => ['nullable', 'string', 'max:3000'],
        ]);

        $assignedUnit = filled($validated['assigned_unit_id'] ?? null)
            ? EmergencyServiceUnit::find($validated['assigned_unit_id'])
            : null;

        $payload = [
            'status' => $validated['status'],
            'response_note' => $validated['response_note'] ?? null,
            'assigned_unit_id' => $assignedUnit?->id,
            'assigned_unit' => $assignedUnit?->unit_name,
            'assigned_unit_contact' => $assignedUnit?->contact_phone,
            'assigned_unit_toll_free' => $assignedUnit?->toll_free_line,
            'dispatch_reference' => $assignedUnit ? ($emergencyRequest->dispatch_reference ?: 'EMG-' . Str::upper(Str::random(10))) : null,
            'assigned_at' => $assignedUnit && ! $emergencyRequest->assigned_at ? now() : $emergencyRequest->assigned_at,
            'resolved_at' => in_array($validated['status'], [EmergencyRequest::STATUS_RESOLVED, EmergencyRequest::STATUS_CLOSED], true) ? now() : null,
        ];

        $emergencyRequest->update($payload);

        return back()->with('success', 'Emergency request updated successfully.');
    }

    public function track(Request $request, EmergencyRequest $emergencyRequest)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(config('kiosk.emergency.tracking_statuses', []))],
            'emergency_service_unit_id' => ['nullable', 'exists:emergency_service_units,id'],
            'location_label' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'eta_minutes' => ['nullable', 'integer', 'min:0', 'max:1440'],
            'note' => ['nullable', 'string', 'max:2000'],
            'event_time' => ['nullable', 'date'],
        ]);

        $unit = filled($validated['emergency_service_unit_id'] ?? null)
            ? EmergencyServiceUnit::find($validated['emergency_service_unit_id'])
            : $emergencyRequest->assignedUnit;

        $eventTime = filled($validated['event_time'] ?? null) ? Carbon::parse($validated['event_time']) : now();

        $event = $emergencyRequest->trackingEvents()->create([
            'emergency_service_unit_id' => $unit?->id,
            'status' => $validated['status'],
            'location_label' => $validated['location_label'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'eta_minutes' => $validated['eta_minutes'] ?? null,
            'note' => $validated['note'] ?? null,
            'event_time' => $eventTime,
            'meta' => [
                'updated_by' => auth()->id(),
            ],
        ]);

        $requestStatus = match ($validated['status']) {
            'received' => EmergencyRequest::STATUS_RECEIVED,
            'contacted' => EmergencyRequest::STATUS_CONTACTED,
            'responding', 'unit_dispatched', 'en_route', 'approaching_destination' => EmergencyRequest::STATUS_RESPONDING,
            'on_scene' => EmergencyRequest::STATUS_ON_SCENE,
            'resolved' => EmergencyRequest::STATUS_RESOLVED,
            'closed' => EmergencyRequest::STATUS_CLOSED,
            default => $emergencyRequest->status,
        };

        $emergencyRequest->update([
            'status' => $requestStatus,
            'assigned_unit_id' => $unit?->id ?? $emergencyRequest->assigned_unit_id,
            'assigned_unit' => $unit?->unit_name ?? $emergencyRequest->assigned_unit,
            'assigned_unit_contact' => $unit?->contact_phone ?? $emergencyRequest->assigned_unit_contact,
            'assigned_unit_toll_free' => $unit?->toll_free_line ?? $emergencyRequest->assigned_unit_toll_free,
            'dispatch_reference' => $unit ? ($emergencyRequest->dispatch_reference ?: 'EMG-' . Str::upper(Str::random(10))) : $emergencyRequest->dispatch_reference,
            'assigned_at' => $unit && ! $emergencyRequest->assigned_at ? $eventTime : $emergencyRequest->assigned_at,
            'last_tracked_at' => $eventTime,
            'resolved_at' => in_array($requestStatus, [EmergencyRequest::STATUS_RESOLVED, EmergencyRequest::STATUS_CLOSED], true) ? $eventTime : $emergencyRequest->resolved_at,
            'response_note' => $validated['note'] ?? $emergencyRequest->response_note,
        ]);

        return back()->with('success', 'Live tracking update added successfully.');
    }
}
