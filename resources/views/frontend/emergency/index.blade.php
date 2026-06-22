@extends('layouts.frontend')

@section('meta_title', 'Emergency Response | Kiosk')
@section('meta_description', 'Submit an emergency alert with structured Nigeria location data, official emergency lines, and responder unit coverage.')

@push('styles')
<style>
    .emergency-shell{
        padding:2.25rem 0 5rem;
    }
    .emergency-hero{
        background:linear-gradient(135deg, rgba(255,245,245,.97) 0%, rgba(248,233,233,.94) 100%);
        border:1px solid rgba(220, 70, 70, .18);
        border-radius:32px;
        padding:2rem;
    }
    .emergency-callout{
        background:rgba(255,255,255,.78);
        border:1px solid rgba(220, 70, 70, .14);
        border-radius:24px;
    }
    .emergency-panel,
    .emergency-directory-card .feature-card{
        background:rgba(255,253,249,.9);
        border:1px solid rgba(220, 70, 70, .14);
        border-radius:28px;
        box-shadow:none;
    }
    .emergency-unit-card{
        background:#fff;
        border:1px solid rgba(17, 17, 17, .08);
        border-radius:18px;
        padding:1rem;
    }
    .emergency-side-stack{
        display:grid;
        gap:1rem;
        position:sticky;
        top:1.5rem;
    }
    @media (max-width: 991.98px){
        .emergency-side-stack{
            position:static;
            top:auto;
        }
    }
</style>
@endpush

@section('content')
<section class="emergency-shell">
    <div class="container">
        @include('partials.flash')

        <div class="emergency-hero mb-4">
            <div class="row g-4 align-items-center">
                <div class="col-lg-7">
                    <span class="section-label text-danger">Emergency Response</span>
                    <h1 class="display-6 fw-bold mb-3">Send an emergency alert</h1>
                    <p class="lead mb-0">
                        Send a structured alert with your state, local government area, contact details, and exact location. If the situation is life-threatening, call the official emergency lines immediately.
                    </p>
                </div>
                <div class="col-lg-5">
                    <div class="emergency-callout p-4">
                        <h4 class="fw-bold mb-3">Before you send the alert</h4>
                        <ul class="mb-0 ps-3">
                            <li>Choose the correct state and local government area</li>
                            <li>Share a working phone number responders can reach</li>
                            <li>Use geolocation only if you can do so safely</li>
                            <li>Call official responders immediately where possible</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 align-items-start">
            <div class="col-lg-7">
                <div class="feature-card emergency-panel p-4 p-lg-5">
                    <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-4">
                        <div>
                            <h3 class="fw-bold text-danger mb-2">Emergency request form</h3>
                            <p class="text-muted mb-0">Use the form below so the dispatch desk can validate your location quickly.</p>
                        </div>
                        <span class="badge text-bg-danger">Priority</span>
                    </div>

                    <form action="{{ route('emergency.store') }}" method="POST" id="emergencyRequestForm">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Country</label>
                                <input type="text" class="form-control" value="{{ $directoryPayload['country']['name'] ?? 'Nigeria' }}" readonly>
                                <input type="hidden" name="country_code" value="{{ old('country_code', $directoryPayload['country']['code'] ?? 'NG') }}">
                                <input type="hidden" name="country_name" value="{{ old('country_name', $directoryPayload['country']['name'] ?? 'Nigeria') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Emergency Type</label>
                                <select name="emergency_type" class="form-select @error('emergency_type') is-invalid @enderror" required>
                                    <option value="">Select emergency type</option>
                                    @foreach($emergencyTypes as $type)
                                        <option value="{{ $type }}" @selected(old('emergency_type') === $type)}>
                                            {{ ucfirst(str_replace('_', ' ', $type)) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('emergency_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="full_name" value="{{ old('full_name', auth()->user()->name ?? '') }}" class="form-control @error('full_name') is-invalid @enderror" required>
                                @error('full_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Phone Number</label>
                                <input type="text" name="phone" value="{{ old('phone', auth()->user()->phone ?? '') }}" class="form-control @error('phone') is-invalid @enderror" required>
                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Alternate Phone</label>
                                <input type="text" name="alternate_phone" value="{{ old('alternate_phone') }}" class="form-control @error('alternate_phone') is-invalid @enderror">
                                @error('alternate_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">State</label>
                                <select name="state_name" id="state_name" class="form-select @error('state_name') is-invalid @enderror" required></select>
                                <input type="hidden" name="state_code" id="state_code" value="{{ old('state_code') }}">
                                @error('state_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Local Government Area</label>
                                <select name="local_government_area" id="local_government_area" class="form-select @error('local_government_area') is-invalid @enderror" required></select>
                                @error('local_government_area') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Latitude</label>
                                <input type="text" name="latitude" id="latitude" value="{{ old('latitude') }}" class="form-control @error('latitude') is-invalid @enderror" placeholder="Optional, or use My Location">
                                @error('latitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Longitude</label>
                                <input type="text" name="longitude" id="longitude" value="{{ old('longitude') }}" class="form-control @error('longitude') is-invalid @enderror" placeholder="Optional, or use My Location">
                                @error('longitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Location Details</label>
                                <input type="text" name="location_text" value="{{ old('location_text') }}" class="form-control @error('location_text') is-invalid @enderror" placeholder="Street, bus stop, estate gate, landmark, or nearest junction" required>
                                @error('location_text') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea name="description" rows="5" class="form-control @error('description') is-invalid @enderror" placeholder="Describe what is happening and any immediate risk..." required>{{ old('description') }}</textarea>
                                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-3 mt-4">
                            <button type="button" class="btn btn-outline-danger" id="captureLocationButton">Use My Location</button>
                            <button class="btn btn-danger btn-lg">Send Alert</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="emergency-side-stack">
                    <div class="emergency-directory-card">
                        <div class="feature-card p-4">
                            <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap mb-3">
                                <div>
                                    <h4 class="fw-bold text-danger mb-1">Official Emergency Lines</h4>
                                    <p class="text-muted mb-0">Verified national lines and state-linked service units.</p>
                                </div>
                                <span class="badge text-bg-danger">Nigeria</span>
                            </div>

                            <div class="d-grid gap-3" id="nationalEmergencyUnits"></div>
                        </div>
                    </div>

                    <div class="emergency-directory-card">
                        <div class="feature-card p-4">
                            <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap mb-3">
                                <div>
                                    <h4 class="fw-bold text-danger mb-1">Units In Selected State</h4>
                                    <p class="text-muted mb-0">The service-unit directory updates when you choose a state.</p>
                                </div>
                                <span class="badge text-bg-light border" id="selectedStateBadge">No state selected</span>
                            </div>

                            <div class="d-grid gap-3" id="stateEmergencyUnits">
                                <div class="alert alert-light border mb-0">Select a state to view the linked emergency response unit and toll-free lines.</div>
                            </div>
                        </div>
                    </div>

                    @auth
                        <div class="emergency-directory-card">
                            <div class="feature-card p-4">
                                <h5 class="fw-bold mb-3">My Emergency History</h5>
                                <a href="{{ route('customer.emergency.index') }}" class="btn btn-outline-primary">View My Requests</a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
(() => {
    const payload = @json($directoryPayload);
    const states = payload.states ?? [];
    const nationalUnits = payload.national_units ?? [];
    const unitsByState = payload.units_by_state ?? {};
    const oldState = @json(old('state_name'));
    const oldLga = @json(old('local_government_area'));

    const stateSelect = document.getElementById('state_name');
    const stateCodeInput = document.getElementById('state_code');
    const lgaSelect = document.getElementById('local_government_area');
    const nationalUnitsContainer = document.getElementById('nationalEmergencyUnits');
    const stateUnitsContainer = document.getElementById('stateEmergencyUnits');
    const selectedStateBadge = document.getElementById('selectedStateBadge');
    const captureLocationButton = document.getElementById('captureLocationButton');

    const renderUnitCard = (unit) => {
        const email = unit.contact_email ? `<div class="small text-muted">${unit.contact_email}</div>` : '';
        const tollFree = unit.toll_free_line ? `<div class="small"><strong>Toll-free:</strong> ${unit.toll_free_line}</div>` : '';

        return `
            <div class="emergency-unit-card">
                <div class="d-flex justify-content-between gap-3 flex-wrap">
                    <div>
                        <div class="fw-semibold">${unit.unit_name}</div>
                        <div class="small text-muted text-uppercase">${unit.service_type.replaceAll('_', ' ')}</div>
                    </div>
                    <div class="text-end">
                        <div class="fw-semibold">${unit.contact_phone ?? 'N/A'}</div>
                    </div>
                </div>
                <div class="small mt-2">${unit.address ?? 'No published address'}</div>
                ${email}
                ${tollFree}
            </div>
        `;
    };

    const renderNationalUnits = () => {
        nationalUnitsContainer.innerHTML = nationalUnits.map(renderUnitCard).join('');
    };

    const populateStates = () => {
        stateSelect.innerHTML = '<option value="">Select state</option>';

        states.forEach((state) => {
            const option = document.createElement('option');
            option.value = state.name;
            option.textContent = state.name;
            option.dataset.code = state.code;
            option.selected = oldState === state.name;
            stateSelect.appendChild(option);
        });
    };

    const populateLgas = (selectedStateName) => {
        lgaSelect.innerHTML = '<option value="">Select local government area</option>';
        const state = states.find((item) => item.name === selectedStateName);

        if (!state) {
            stateCodeInput.value = '';
            return;
        }

        stateCodeInput.value = state.code;

        state.lgas.forEach((lga) => {
            const option = document.createElement('option');
            option.value = lga;
            option.textContent = lga;
            option.selected = oldLga === lga;
            lgaSelect.appendChild(option);
        });
    };

    const renderStateUnits = (selectedStateName) => {
        if (!selectedStateName) {
            selectedStateBadge.textContent = 'No state selected';
            stateUnitsContainer.innerHTML = '<div class="alert alert-light border mb-0">Select a state to view the linked emergency response unit and toll-free lines.</div>';
            return;
        }

        selectedStateBadge.textContent = selectedStateName;
        const units = unitsByState[selectedStateName] ?? [];

        if (!units.length) {
            stateUnitsContainer.innerHTML = '<div class="alert alert-warning mb-0">No state-specific unit record is stored yet for this state. Use the national toll-free lines immediately.</div>';
            return;
        }

        stateUnitsContainer.innerHTML = units.map(renderUnitCard).join('');
    };

    const syncStateUI = () => {
        const selectedStateName = stateSelect.value;
        populateLgas(selectedStateName);
        renderStateUnits(selectedStateName);
    };

    captureLocationButton?.addEventListener('click', () => {
        if (!navigator.geolocation) {
            window.alert('Geolocation is not supported on this device.');
            return;
        }

        navigator.geolocation.getCurrentPosition((position) => {
            document.getElementById('latitude').value = position.coords.latitude.toFixed(7);
            document.getElementById('longitude').value = position.coords.longitude.toFixed(7);
            window.alert('Location captured successfully.');
        }, () => {
            window.alert('Unable to capture your location.');
        });
    });

    stateSelect?.addEventListener('change', () => {
        populateLgas(stateSelect.value);
        renderStateUnits(stateSelect.value);
    });

    renderNationalUnits();
    populateStates();
    syncStateUI();
})();
</script>
@endpush
