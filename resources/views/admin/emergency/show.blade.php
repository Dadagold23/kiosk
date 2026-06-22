@extends('layouts.admin')

@section('meta_title', 'Emergency Dispatch | Kiosk Admin')
@section('page_title', 'Emergency Dispatch')
@section('page_subtitle', 'Assign responders, track movement, and close incidents with live updates')

@section('content')
<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Emergency</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.emergency.index') }}">Emergency Desk</a></li>
                <li class="breadcrumb-item active" aria-current="page">Dispatch #{{ $emergencyRequest->id }}</li>
            </ol>
        </nav>
    </div>
    <div class="ms-auto">
        <a href="{{ route('admin.emergency.index') }}" class="btn btn-outline-secondary radius-30"><i class="bx bx-arrow-back me-1"></i>Back to List</a>
    </div>
</div>
<!--end breadcrumb-->

<div class="row g-4">
    <!-- Snapshot & Tracking -->
    <div class="col-xl-7">
        <!-- Hero Card -->
        <div class="card radius-10 border-0 shadow-sm mb-4 bg-danger text-white">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                    <div>
                        <span class="badge bg-white text-danger mb-2">Priority Incident</span>
                        <h4 class="mb-1 fw-bold text-white">{{ ucfirst(str_replace('_', ' ', $emergencyRequest->emergency_type)) }}</h4>
                        <p class="mb-0 text-white-50 small">Submitted {{ $emergencyRequest->created_at->format('d M Y, h:i A') }}</p>
                    </div>
                    <span class="badge bg-white text-danger px-3 py-2 text-uppercase rounded-pill fw-bold">
                        {{ ucfirst(str_replace('_', ' ', $emergencyRequest->status)) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Incident Snapshot -->
        <div class="card radius-10 border-0 shadow-sm mb-4">
            <div class="card-body">
                <h5 class="mb-4 fw-bold">Incident Snapshot</h5>
                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <div class="p-3 radius-10 bg-light">
                            <small class="text-muted d-block text-uppercase mb-1">Requester</small>
                            <h6 class="mb-0 fw-bold">{{ $emergencyRequest->full_name ?: 'Anonymous or guest' }}</h6>
                            <small class="text-muted">{{ $emergencyRequest->user?->name ?? 'Guest submission' }}</small>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 radius-10 bg-light">
                            <small class="text-muted d-block text-uppercase mb-1">Contact</small>
                            <h6 class="mb-0 fw-bold">{{ $emergencyRequest->phone }}</h6>
                            <small class="text-muted">{{ $emergencyRequest->alternate_phone ?: 'No alternate phone' }}</small>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="p-3 radius-10 bg-light">
                            <small class="text-muted d-block text-uppercase mb-1">State / LGA</small>
                            <h6 class="mb-0 fw-bold">{{ $emergencyRequest->state_name ?: 'N/A' }}</h6>
                            <small class="text-muted">{{ $emergencyRequest->local_government_area ?: 'N/A' }}</small>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="p-3 radius-10 bg-light">
                            <small class="text-muted d-block text-uppercase mb-1">Location text</small>
                            <h6 class="mb-0 fw-bold text-truncate" title="{{ $emergencyRequest->location_text }}">{{ $emergencyRequest->location_text ?: 'N/A' }}</h6>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="p-3 radius-10 bg-light">
                            <small class="text-muted d-block text-uppercase mb-1">Coordinates</small>
                            <h6 class="mb-0 fw-bold">{{ $emergencyRequest->latitude && $emergencyRequest->longitude ? $emergencyRequest->latitude . ', ' . $emergencyRequest->longitude : 'Not captured' }}</h6>
                        </div>
                    </div>
                </div>

                <div class="p-3 radius-10 bg-light">
                    <span class="d-block text-uppercase small text-muted fw-bold mb-2">Situation Note</span>
                    <p class="mb-0">{{ $emergencyRequest->description ?: 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Dispatch Map -->
        <div class="card radius-10 border-0 shadow-sm mb-4">
            <div class="card-body">
                <h5 class="mb-3 fw-bold">Dispatch Map</h5>
                <div id="adminEmergencyMap" style="height: 380px; border-radius: 10px; overflow: hidden;"></div>
            </div>
        </div>

        <!-- Timeline -->
        <div class="card radius-10 border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center mb-4">
                    <h5 class="mb-0 fw-bold">Tracking Timeline</h5>
                    <span class="ms-auto badge bg-light-secondary text-secondary rounded-pill">Last update: {{ $emergencyRequest->last_tracked_at?->format('d M Y, h:i A') ?: 'Awaiting first update' }}</span>
                </div>

                <div class="list-group list-group-flush">
                    @forelse($emergencyRequest->trackingEvents as $event)
                        <div class="list-group-item px-0">
                            <div class="d-flex justify-content-between gap-3 flex-wrap align-items-center mb-2">
                                <div>
                                    <span class="badge bg-light-primary text-primary text-uppercase">{{ ucfirst(str_replace('_', ' ', $event->status)) }}</span>
                                    <span class="ms-2 fw-semibold">{{ $event->location_label ?: 'Unspecified Location' }}</span>
                                </div>
                                <small class="text-muted">{{ optional($event->event_time)->format('d M Y, h:i A') ?: 'Not timestamped' }}</small>
                            </div>
                            <p class="mb-1 text-muted small">{{ $event->note ?: 'No note added.' }}</p>
                            @if($event->eta_minutes)
                                <small class="text-primary-emphasis d-block"><i class="bx bx-time me-1"></i>ETA: {{ $event->eta_minutes }} minute(s)</small>
                            @endif
                        </div>
                    @empty
                        <div class="p-3 text-center text-muted"><i class="bx bx-history fs-3 mb-2"></i><p class="mb-0">No live tracking events have been logged yet.</p></div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Actions & Dispatches -->
    <div class="col-xl-5">
        <!-- Status & Assignment -->
        <div class="card radius-10 border-0 shadow-sm mb-4">
            <div class="card-body">
                <h5 class="mb-3 fw-bold">Assign Responder Unit</h5>
                <form action="{{ route('admin.emergency.update', $emergencyRequest) }}" method="POST" class="row g-3">
                    @csrf

                    <div class="col-12">
                        <label class="form-label fw-bold small text-uppercase text-secondary">Request Status</label>
                        <select name="status" class="form-select radius-30">
                            @foreach(config('kiosk.emergency.statuses', []) as $status)
                                <option value="{{ $status }}" @selected($emergencyRequest->status === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold small text-uppercase text-secondary">Assigned Official Unit</label>
                        <select name="assigned_unit_id" class="form-select radius-30">
                            <option value="">No unit assigned</option>
                            @foreach($availableUnits as $unit)
                                <option value="{{ $unit->id }}" @selected(old('assigned_unit_id', $emergencyRequest->assigned_unit_id) == $unit->id)>{{ $unit->unit_name }}{{ $unit->is_national ? ' (National)' : '' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold small text-uppercase text-secondary">Response Note</label>
                        <textarea name="response_note" rows="4" class="form-control radius-30" placeholder="Summarize dispatch actions...">{{ old('response_note', $emergencyRequest->response_note) }}</textarea>
                    </div>

                    <div class="col-12 text-end mt-4">
                        <button type="submit" class="btn btn-danger radius-30 w-100">Save Assignment</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Add Live Tracking Event -->
        <div class="card radius-10 border-0 shadow-sm mb-4">
            <div class="card-body">
                <h5 class="mb-3 fw-bold">Add Live Tracking Update</h5>
                <form action="{{ route('admin.emergency.track', $emergencyRequest) }}" method="POST" class="row g-3">
                    @csrf

                    <div class="col-12">
                        <label class="form-label fw-bold small text-uppercase text-secondary">Tracking Status</label>
                        <select name="status" class="form-select radius-30" required>
                            @foreach(config('kiosk.emergency.tracking_statuses', []) as $status)
                                <option value="{{ $status }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold small text-uppercase text-secondary">Service Unit</label>
                        <select name="emergency_service_unit_id" class="form-select radius-30">
                            <option value="">Use current assigned unit</option>
                            @foreach($availableUnits as $unit)
                                <option value="{{ $unit->id }}" @selected($emergencyRequest->assigned_unit_id == $unit->id)>{{ $unit->unit_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-uppercase text-secondary">Latitude</label>
                        <input type="text" name="latitude" class="form-control radius-30" placeholder="e.g. 6.4474">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-uppercase text-secondary">Longitude</label>
                        <input type="text" name="longitude" class="form-control radius-30" placeholder="e.g. 3.4698">
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold small text-uppercase text-secondary">Current Location Label</label>
                        <input type="text" name="location_label" class="form-control radius-30" placeholder="e.g. Corridor description">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-uppercase text-secondary">ETA Minutes</label>
                        <input type="number" min="0" max="1440" name="eta_minutes" class="form-control radius-30" placeholder="10">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-uppercase text-secondary">Event Time</label>
                        <input type="datetime-local" name="event_time" class="form-control radius-30">
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold small text-uppercase text-secondary">Tracking Note</label>
                        <textarea name="note" rows="4" class="form-control radius-30" placeholder="Describe field developments..."></textarea>
                    </div>

                    <div class="col-12 mt-4 text-end">
                        <button type="submit" class="btn btn-outline-danger radius-30 w-100">Add Tracking Update</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Assigned Unit Snapshot -->
        <div class="card radius-10 border-0 shadow-sm mb-4">
            <div class="card-body">
                <h5 class="mb-3 fw-bold">Assigned Unit Snapshot</h5>
                <div class="list-group list-group-flush">
                    <div class="list-group-item px-0 py-2 d-flex justify-content-between">
                        <small class="text-secondary fw-bold text-uppercase">Unit</small>
                        <span class="fw-semibold text-dark">{{ $emergencyRequest->assigned_unit ?: 'Not assigned yet' }}</span>
                    </div>
                    <div class="list-group-item px-0 py-2 d-flex justify-content-between">
                        <small class="text-secondary fw-bold text-uppercase">Direct Contact</small>
                        <span class="fw-semibold text-dark">{{ $emergencyRequest->assigned_unit_contact ?: 'N/A' }}</span>
                    </div>
                    <div class="list-group-item px-0 py-2 d-flex justify-content-between">
                        <small class="text-secondary fw-bold text-uppercase">Toll-Free</small>
                        <span class="fw-semibold text-dark">{{ $emergencyRequest->assigned_unit_toll_free ?: 'N/A' }}</span>
                    </div>
                    <div class="list-group-item px-0 py-2 d-flex justify-content-between">
                        <small class="text-secondary fw-bold text-uppercase">Dispatch Ref</small>
                        <span class="fw-semibold text-dark">{{ $emergencyRequest->dispatch_reference ?: 'N/A' }}</span>
                    </div>
                    <div class="list-group-item px-0 py-2 d-flex justify-content-between">
                        <small class="text-secondary fw-bold text-uppercase">Assigned At</small>
                        <span class="fw-semibold text-dark small">{{ $emergencyRequest->assigned_at?->format('d M Y, h:i A') ?: 'N/A' }}</span>
                    </div>
                    <div class="list-group-item px-0 py-2 d-flex justify-content-between">
                        <small class="text-secondary fw-bold text-uppercase">Resolved At</small>
                        <span class="fw-semibold text-dark small">{{ $emergencyRequest->resolved_at?->format('d M Y, h:i A') ?: 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>

        @include('partials.reviews.admin-panel', [
            'reviews' => $emergencyRequest->reviews,
            'title' => 'Emergency Response Reviews',
            'subtitle' => 'Moderate responder feedback before featuring it on the emergency page.',
            'wrapperClass' => 'card radius-10 border-0 shadow-sm mt-4 p-4',
        ])
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(() => {
    const destinationLat = @json($emergencyRequest->latitude);
    const destinationLng = @json($emergencyRequest->longitude);
    const latestEvent = @json(optional($emergencyRequest->latestTrackingEvent)->only(['latitude', 'longitude', 'location_label']));

    const map = L.map('adminEmergencyMap', {
        scrollWheelZoom: false
    }).setView([9.0820, 8.6753], 5);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    const markers = [];

    if (destinationLat && destinationLng) {
        markers.push(
            L.marker([Number(destinationLat), Number(destinationLng)])
                .addTo(map)
                .bindPopup('Emergency destination')
        );
    }

    if (latestEvent && latestEvent.latitude && latestEvent.longitude) {
        markers.push(
            L.marker([Number(latestEvent.latitude), Number(latestEvent.longitude)])
                .addTo(map)
                .bindPopup(latestEvent.location_label ?? 'Latest tracked unit position')
        );
    }

    if (markers.length === 2) {
        L.polyline([markers[0].getLatLng(), markers[1].getLatLng()], { color: '#dc3545' }).addTo(map);
    }

    if (markers.length) {
        const bounds = L.featureGroup(markers).getBounds();
        if (bounds.isValid()) {
            map.fitBounds(bounds.pad(0.25));
        }
    }
})();
</script>
@endpush

