@extends('layouts.customer')

@section('meta_title', 'Emergency Tracking | Kiosk')
@section('customer_page_title', ucfirst(str_replace('_', ' ', $emergencyRequest->emergency_type)) . ' Alert')
@section('customer_page_subtitle', 'Submitted on ' . $emergencyRequest->created_at->format('d M Y, h:i A') . ' and updated as responders move toward the destination.')

@include('partials.amerce.account-detail-styles')

@section('customer_body')
<div class="customer-page-grid">
    <div class="feature-card p-4 p-lg-5 amerce-detail-hero">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <div>
                <span class="customer-status-pill is-danger mb-3">Live Emergency Tracking</span>
                <h4 class="fw-bold mb-1">Responders, route changes, and incident details stay visible here.</h4>
                <p class="amerce-detail-copy mb-0">This page refreshes the assigned unit timeline automatically while the incident is active.</p>
            </div>
            <a href="{{ route('customer.emergency.index') }}" class="customer-soft-button">Back to Requests</a>
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <div class="amerce-detail-stat h-100">
                    <div class="label">Request Status</div>
                    <div class="value" id="requestStatusBadge">{{ ucfirst(str_replace('_', ' ', $emergencyRequest->status)) }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="amerce-detail-stat h-100">
                    <div class="label">Assigned Unit</div>
                    <div class="value" id="assignedUnitNameHero">{{ $emergencyRequest->assigned_unit ?: 'Not assigned yet' }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="amerce-detail-stat h-100">
                    <div class="label">Last Tracked</div>
                    <div class="value" id="lastTrackedAtBadge">{{ $emergencyRequest->last_tracked_at?->format('d M Y, h:i A') ?: 'Awaiting first field update' }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="feature-card customer-page-block mb-4">
                <div class="mb-3">
                    <div class="customer-eyebrow">Incident Snapshot</div>
                    <h3 class="customer-section-title">Incident location</h3>
                    <p class="customer-section-copy">The core destination and caller information the response team is working from.</p>
                </div>

                <div class="customer-info-grid mb-3">
                    <div class="customer-info-card">
                        <span class="label">State</span>
                        <span class="value">{{ $emergencyRequest->state_name ?: 'N/A' }}</span>
                    </div>
                    <div class="customer-info-card">
                        <span class="label">Local Government Area</span>
                        <span class="value">{{ $emergencyRequest->local_government_area ?: 'N/A' }}</span>
                    </div>
                </div>

                <div class="customer-panel-note">
                    <p><strong>Location:</strong> {{ $emergencyRequest->location_text ?: 'N/A' }}</p>
                    <p><strong>Phone:</strong> {{ $emergencyRequest->phone }}</p>
                    <p><strong>Alternate Phone:</strong> {{ $emergencyRequest->alternate_phone ?: 'N/A' }}</p>
                    <p class="mb-0"><strong>Description:</strong><br>{{ $emergencyRequest->description ?: 'N/A' }}</p>
                </div>
            </div>

            <div class="feature-card customer-page-block mb-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <div>
                        <div class="customer-eyebrow">Map View</div>
                        <h3 class="customer-section-title">Response map</h3>
                    </div>
                    <span class="customer-status-pill is-danger" id="requestStatusBadgePanel">{{ ucfirst(str_replace('_', ' ', $emergencyRequest->status)) }}</span>
                </div>

                <div id="emergencyTrackingMap" style="height: 360px; border-radius: 20px; overflow: hidden;"></div>
                <div class="small text-muted mt-3" id="trackingMapHint">Map markers appear when destination coordinates or unit tracking coordinates are available.</div>
            </div>

            <div class="feature-card customer-page-block">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <div>
                        <div class="customer-eyebrow">Live Feed</div>
                        <h3 class="customer-section-title">Tracking timeline</h3>
                    </div>
                    <span class="customer-status-pill is-primary" id="lastTrackedAtBadgePanel">{{ $emergencyRequest->last_tracked_at?->format('d M Y, h:i A') ?: 'Awaiting first field update' }}</span>
                </div>

                <div id="trackingTimeline" class="d-grid gap-3">
                    @forelse($emergencyRequest->trackingEvents as $event)
                        <div class="customer-info-card">
                            <div class="d-flex justify-content-between gap-3 flex-wrap">
                                <div>
                                    <div class="fw-semibold">{{ ucfirst(str_replace('_', ' ', $event->status)) }}</div>
                                    <div class="small text-muted">{{ $event->location_label ?: 'No location label provided' }}</div>
                                </div>
                                <div class="text-end small text-muted">{{ optional($event->event_time)->format('d M Y, h:i A') ?: 'Not timestamped' }}</div>
                            </div>
                            @if($event->note)
                                <div class="small mt-2">{{ $event->note }}</div>
                            @endif
                            @if($event->eta_minutes)
                                <div class="small text-muted mt-1">ETA: {{ $event->eta_minutes }} minute(s)</div>
                            @endif
                        </div>
                    @empty
                        <div class="customer-panel-note">No live tracking events have been added yet.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="feature-card customer-page-block mb-4">
                <div class="mb-3">
                    <div class="customer-eyebrow">Assigned Unit</div>
                    <h3 class="customer-section-title">Response unit</h3>
                </div>

                <div class="customer-info-grid">
                    <div class="customer-info-card">
                        <span class="label">Unit</span>
                        <span class="value" id="assignedUnitName">{{ $emergencyRequest->assigned_unit ?: 'Not assigned yet' }}</span>
                    </div>
                    <div class="customer-info-card">
                        <span class="label">Direct Contact</span>
                        <span class="value" id="assignedUnitContact">{{ $emergencyRequest->assigned_unit_contact ?: 'Awaiting responder contact' }}</span>
                    </div>
                    <div class="customer-info-card">
                        <span class="label">Toll-Free Line</span>
                        <span class="value" id="assignedUnitTollFree">{{ $emergencyRequest->assigned_unit_toll_free ?: 'Use national emergency lines' }}</span>
                    </div>
                    <div class="customer-info-card">
                        <span class="label">Dispatch Reference</span>
                        <span class="value" id="dispatchReference">{{ $emergencyRequest->dispatch_reference ?: 'Not issued yet' }}</span>
                    </div>
                </div>
            </div>

            <div class="feature-card customer-page-block">
                <div class="mb-3">
                    <div class="customer-eyebrow">Requester Details</div>
                    <h3 class="customer-section-title">Submission info</h3>
                </div>

                <div class="customer-panel-note">
                    <p><strong>Name:</strong> {{ $emergencyRequest->full_name ?: 'N/A' }}</p>
                    <p><strong>Country:</strong> {{ $emergencyRequest->country_name ?: 'Nigeria' }}</p>
                    <p><strong>Coordinates:</strong> {{ $emergencyRequest->latitude && $emergencyRequest->longitude ? $emergencyRequest->latitude . ', ' . $emergencyRequest->longitude : 'Not captured yet' }}</p>
                    <p class="mb-0"><strong>Response Note:</strong><br>{{ $emergencyRequest->response_note ?: 'No response note yet.' }}</p>
                </div>
            </div>

            @include('partials.reviews.customer-panel', [
                'reviewType' => 'emergency',
                'reviewable' => $emergencyRequest,
                'existingReview' => $existingReview,
                'canSubmitReview' => $canSubmitReview,
            ])
        </div>
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
    const trackingUrl = @json(route('customer.emergency.tracking', $emergencyRequest));
    const timelineContainer = document.getElementById('trackingTimeline');
    const requestStatusBadge = document.getElementById('requestStatusBadge');
    const requestStatusBadgePanel = document.getElementById('requestStatusBadgePanel');
    const assignedUnitNameHero = document.getElementById('assignedUnitNameHero');
    const assignedUnitName = document.getElementById('assignedUnitName');
    const assignedUnitContact = document.getElementById('assignedUnitContact');
    const assignedUnitTollFree = document.getElementById('assignedUnitTollFree');
    const dispatchReference = document.getElementById('dispatchReference');
    const lastTrackedAtBadge = document.getElementById('lastTrackedAtBadge');
    const lastTrackedAtBadgePanel = document.getElementById('lastTrackedAtBadgePanel');
    const trackingMapHint = document.getElementById('trackingMapHint');

    const map = L.map('emergencyTrackingMap', {
        scrollWheelZoom: false
    }).setView([9.0820, 8.6753], 5);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    let destinationMarker = null;
    let unitMarker = null;
    let connectionLine = null;

    const renderTimeline = (events) => {
        if (!events.length) {
            timelineContainer.innerHTML = '<div class="customer-panel-note">No live tracking events have been added yet.</div>';
            return;
        }

        timelineContainer.innerHTML = events.map((event) => `
            <div class="customer-info-card">
                <div class="d-flex justify-content-between gap-3 flex-wrap">
                    <div>
                        <div class="fw-semibold">${event.status.replaceAll('_', ' ').replace(/\b\w/g, (char) => char.toUpperCase())}</div>
                        <div class="small text-muted">${event.location_label ?? 'No location label provided'}</div>
                    </div>
                    <div class="text-end small text-muted">${event.event_time_human ?? 'Not timestamped'}</div>
                </div>
                ${event.note ? `<div class="small mt-2">${event.note}</div>` : ''}
                ${event.eta_minutes ? `<div class="small text-muted mt-1">ETA: ${event.eta_minutes} minute(s)</div>` : ''}
            </div>
        `).join('');
    };

    const updateMap = (payload) => {
        const destination = payload.request.destination;
        const latestEvent = payload.latest_event;

        if (destination.latitude && destination.longitude) {
            const destinationCoords = [Number(destination.latitude), Number(destination.longitude)];

            if (!destinationMarker) {
                destinationMarker = L.marker(destinationCoords).addTo(map);
            } else {
                destinationMarker.setLatLng(destinationCoords);
            }

            destinationMarker.bindPopup(`Destination: ${destination.location_text ?? 'Emergency location'}`);
        } else if (destinationMarker) {
            map.removeLayer(destinationMarker);
            destinationMarker = null;
        }

        if (latestEvent?.latitude && latestEvent?.longitude) {
            const unitCoords = [Number(latestEvent.latitude), Number(latestEvent.longitude)];

            if (!unitMarker) {
                unitMarker = L.marker(unitCoords).addTo(map);
            } else {
                unitMarker.setLatLng(unitCoords);
            }

            unitMarker.bindPopup(`Responder: ${latestEvent.location_label ?? latestEvent.status}`);
        } else if (unitMarker) {
            map.removeLayer(unitMarker);
            unitMarker = null;
        }

        if (connectionLine) {
            map.removeLayer(connectionLine);
            connectionLine = null;
        }

        if (destinationMarker && unitMarker) {
            connectionLine = L.polyline([destinationMarker.getLatLng(), unitMarker.getLatLng()], { color: '#dc3545' }).addTo(map);
        }

        if (destinationMarker || unitMarker) {
            const bounds = L.featureGroup([destinationMarker, unitMarker].filter(Boolean)).getBounds();
            if (bounds.isValid()) {
                map.fitBounds(bounds.pad(0.25));
            }

            trackingMapHint.textContent = latestEvent?.location_label
                ? `Latest unit location: ${latestEvent.location_label}`
                : 'Destination coordinates captured. Waiting for the first live unit location update.';
        } else {
            trackingMapHint.textContent = 'Map markers appear when destination coordinates or unit tracking coordinates are available.';
        }
    };

    const refreshTracking = async () => {
        const response = await fetch(trackingUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            return;
        }

        const payload = await response.json();

        requestStatusBadge.textContent = payload.request.status.replaceAll('_', ' ').replace(/\b\w/g, (char) => char.toUpperCase());
        requestStatusBadgePanel.textContent = requestStatusBadge.textContent;
        assignedUnitNameHero.textContent = payload.request.assigned_unit ?? 'Not assigned yet';
        assignedUnitName.textContent = payload.request.assigned_unit ?? 'Not assigned yet';
        assignedUnitContact.textContent = payload.request.assigned_unit_contact ?? 'Awaiting responder contact';
        assignedUnitTollFree.textContent = payload.request.assigned_unit_toll_free ?? 'Use national emergency lines';
        dispatchReference.textContent = payload.request.dispatch_reference ?? 'Not issued yet';
        lastTrackedAtBadge.textContent = payload.request.last_tracked_at ? new Date(payload.request.last_tracked_at).toLocaleString() : 'Awaiting first field update';
        lastTrackedAtBadgePanel.textContent = lastTrackedAtBadge.textContent;

        renderTimeline(payload.events ?? []);
        updateMap(payload);
    };

    refreshTracking();
    setInterval(refreshTracking, 30000);
})();
</script>
@endpush
