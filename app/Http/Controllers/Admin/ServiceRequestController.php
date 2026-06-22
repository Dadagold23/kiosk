<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Http\Request;
use App\Notifications\RequestAssignedNotification;
use App\Services\OpsAssistantService;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class ServiceRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = ServiceRequest::with('user', 'category', 'assignedStaff');

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                ->orWhere('location', 'like', "%{$search}%")
                ->orWhereHas('user', function ($u) use ($search) {
                    $u->where('name', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $requests = $query->latest()->paginate(15)->withQueryString();

        return view('admin.services.index', compact('requests'));
    }

    public function show(ServiceRequest $serviceRequest, OpsAssistantService $opsAssistantService)
    {
        $serviceRequest->load('user', 'category', 'assignedStaff', 'payments', 'trackingEvents', 'reviews.user', 'reviews.moderator');
        $assistantInsight = $opsAssistantService->analyzeServiceRequest($serviceRequest);

        $availableRoles = Role::query()
            ->where('guard_name', 'web')
            ->whereIn('name', ['Service Attendant', 'Service Manager', 'Admin'])
            ->pluck('name')
            ->all();

        $staff = $availableRoles === []
            ? collect()
            : User::role($availableRoles)->get();

        return view('admin.services.show', compact('serviceRequest', 'staff', 'assistantInsight'));
    }

    public function assign(Request $request, ServiceRequest $serviceRequest)
    {
        $validated = $request->validate([
            'assigned_to' => ['nullable', 'exists:users,id'],
            'assigned_team' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', 'max:50'],
            'payment_status' => ['required', 'string', 'max:50'],
        ]);

        $serviceRequest->update($validated);

        if ($serviceRequest->user) {
            $serviceRequest->user->notify(new RequestAssignedNotification(
                'service',
                $serviceRequest->title,
                route('customer.services.show', $serviceRequest)
            ));
        }

        return back()->with('success', 'Service request updated successfully.');
    }

    public function track(Request $request, ServiceRequest $serviceRequest)
    {
        $validated = $request->validate([
            'progress_status' => ['required', Rule::in(config('kiosk.services.tracking_statuses', []))],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'assigned_team' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'next_step' => ['nullable', 'string', 'max:255'],
            'tracking_note' => ['nullable', 'string', 'max:2000'],
            'event_time' => ['nullable', 'date'],
            'service_window_start' => ['nullable', 'date'],
            'service_window_end' => ['nullable', 'date', 'after_or_equal:service_window_start'],
        ]);

        $eventTime = filled($validated['event_time'] ?? null) ? Carbon::parse($validated['event_time']) : now();
        $nextStatus = match ($validated['progress_status']) {
            ServiceRequest::TRACKING_REQUEST_RECEIVED,
            ServiceRequest::TRACKING_PAYMENT_CONFIRMED,
            ServiceRequest::TRACKING_UNDER_REVIEW => 'reviewing',
            ServiceRequest::TRACKING_TEAM_ASSIGNED,
            ServiceRequest::TRACKING_VISIT_SCHEDULED => 'approved',
            ServiceRequest::TRACKING_EN_ROUTE,
            ServiceRequest::TRACKING_ON_SITE,
            ServiceRequest::TRACKING_IN_PROGRESS,
            ServiceRequest::TRACKING_AWAITING_PARTS,
            ServiceRequest::TRACKING_QUALITY_CHECK => 'in_progress',
            ServiceRequest::TRACKING_COMPLETED => 'completed',
            ServiceRequest::TRACKING_CLOSED => 'closed',
            default => $serviceRequest->status,
        };

        $serviceRequest->update([
            'assigned_to' => $validated['assigned_to'] ?? $serviceRequest->assigned_to,
            'assigned_team' => $validated['assigned_team'] ?? $serviceRequest->assigned_team,
            'progress_status' => $validated['progress_status'],
            'tracking_updated_at' => $eventTime,
            'service_window_start' => filled($validated['service_window_start'] ?? null) ? Carbon::parse($validated['service_window_start']) : $serviceRequest->service_window_start,
            'service_window_end' => filled($validated['service_window_end'] ?? null) ? Carbon::parse($validated['service_window_end']) : $serviceRequest->service_window_end,
            'completed_at' => $validated['progress_status'] === ServiceRequest::TRACKING_COMPLETED ? $eventTime : $serviceRequest->completed_at,
            'status' => $nextStatus,
        ]);

        $serviceRequest->trackingEvents()->create([
            'status' => $validated['progress_status'],
            'location' => $validated['location'] ?? null,
            'next_step' => $validated['next_step'] ?? null,
            'note' => $validated['tracking_note'] ?? null,
            'event_time' => $eventTime,
            'meta' => [
                'updated_by' => auth()->id(),
                'assigned_to' => $validated['assigned_to'] ?? $serviceRequest->assigned_to,
                'assigned_team' => $validated['assigned_team'] ?? $serviceRequest->assigned_team,
            ],
        ]);

        if ($serviceRequest->user) {
            $serviceRequest->user->notify(new RequestAssignedNotification(
                'service',
                $serviceRequest->title,
                route('customer.services.show', $serviceRequest)
            ));
        }

        return back()->with('success', 'Service tracking updated successfully.');
    }
    
}
