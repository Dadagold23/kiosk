@extends('layouts.customer')

@section('meta_title', 'My Profile | Kiosk')
@section('customer_page_title', 'Profile')
@section('customer_page_subtitle', 'Manage your account, delivery profile, billing details, and KYC readiness.')

@section('customer_body')
@php($user = auth()->user())
@php($profileCountryValue = old('country', $user->country ?: ''))
@php($deliveryCountryValue = old('delivery_country', $user->delivery_country ?: ''))
@php($identityCountryValue = old('identity_country', $user->identity_country ?: ''))
@php($profileUsesNigeriaGeo = strcasecmp($profileCountryValue, 'Nigeria') === 0)
@php($deliveryUsesNigeriaGeo = strcasecmp($deliveryCountryValue, 'Nigeria') === 0)

<div class="customer-page-grid">
    <div class="customer-card customer-page-block muara-detail-hero" id="profile-overview">
        <div class="row g-4 align-items-center">
            <div class="col-lg-8">
                <span class="customer-welcome-chip">Account Profile</span>
                <h2 class="fw-bold mb-2">Keep your Kiosk profile up to date.</h2>
                <p class="muara-detail-copy mb-0">The details here help with checkout, delivery, billing, and account verification, so it is worth keeping them current.</p>
            </div>
            <div class="col-lg-4">
                <div class="muara-summary-grid">
                    <div class="muara-summary-card">
                        <div class="muara-summary-label">Default Country</div>
                        <div class="muara-summary-value">{{ $user->delivery_country ?: $user->country ?: $user->nationality ?: 'Not set yet' }}</div>
                    </div>
                    <div class="muara-summary-card">
                        <div class="muara-summary-label">Preferred Pay</div>
                        <div class="muara-summary-value">{{ $user->preferred_payment_method ? ucfirst(str_replace('_', ' ', $user->preferred_payment_method)) : 'Not set' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('partials.amerce.profile-settings-nav')

    <div class="row g-4">
        <div class="col-xl-4">
            <div class="customer-card customer-page-block h-100">
                <div class="customer-panel-head">
                    <div>
                        <span class="customer-eyebrow">Readiness</span>
                        <h3 class="customer-section-title">Profile readiness</h3>
                    </div>
                </div>

                <div class="customer-page-grid">
                    <div class="customer-info-card">
                        <span class="label">Payment Ready Email</span>
                        <div class="value {{ filled($user->email) && str($user->email)->contains('.') ? 'text-success' : 'text-danger' }}">{{ filled($user->email) ? $user->email : 'Missing email' }}</div>
                    </div>
                    <div class="customer-info-card">
                        <span class="label">Delivery Address</span>
                        <div class="value">{{ $user->deliveryAddress() ?: 'Not set yet' }}</div>
                    </div>
                    <div class="customer-info-card">
                        <span class="label">Preferred Payment</span>
                        <div class="value">{{ $user->preferred_payment_method ? ucfirst(str_replace('_', ' ', $user->preferred_payment_method)) : 'Not selected' }}</div>
                    </div>
                    <div class="customer-info-card">
                        <span class="label">KYC Status</span>
                        <div class="value {{ ($user->kyc_status === 'approved') ? 'text-success' : (($user->kyc_status === 'rejected') ? 'text-danger' : 'text-warning') }}">{{ $user->kyc_status ? ucfirst(str_replace('_', ' ', $user->kyc_status)) : 'Not submitted' }}</div>
                    </div>
                </div>

                <div class="customer-panel-note mt-3">
                    If delivery details change often, keep landmark and alternate phone filled so field teams can complete handoff without calling support.
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <form action="{{ route('profile.update') }}" method="POST" class="customer-page-grid">
                @csrf
                @method('PATCH')

                <div class="customer-card customer-page-block" id="personal-information">
                    <div class="customer-panel-head">
                        <div>
                            <span class="customer-eyebrow">Core Profile</span>
                            <h3 class="customer-section-title">Personal Information</h3>
                            <p class="customer-section-copy">Keep your identity and contact details current for payments and support routing.</p>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6 customer-field">
                            <label>Full Name</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control @error('name') is-invalid @enderror">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 customer-field">
                            <label>Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control @error('email') is-invalid @enderror">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 customer-field">
                            <label>Primary Phone</label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-control @error('phone') is-invalid @enderror">
                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 customer-field">
                            <label>Alternate Phone</label>
                            <input type="text" name="alternate_phone" value="{{ old('alternate_phone', $user->alternate_phone) }}" class="form-control @error('alternate_phone') is-invalid @enderror">
                            @error('alternate_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 customer-field">
                            <label>Date of Birth</label>
                            <input type="date" name="date_of_birth" value="{{ old('date_of_birth', optional($user->date_of_birth)->format('Y-m-d')) }}" class="form-control @error('date_of_birth') is-invalid @enderror">
                            @error('date_of_birth') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 customer-field">
                            <label>Gender</label>
                            <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                                <option value="">Select gender</option>
                                @foreach(['male' => 'Male', 'female' => 'Female', 'other' => 'Other', 'prefer_not_to_say' => 'Prefer not to say'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('gender', $user->gender) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 customer-field">
                            <label>Nationality</label>
                            <input type="text" name="nationality" value="{{ old('nationality', $user->nationality) }}" class="form-control @error('nationality') is-invalid @enderror">
                            @error('nationality') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 customer-field">
                            <label>Country</label>
                            <select name="country" class="form-select @error('country') is-invalid @enderror" data-geo-country data-geo-scope="profile" data-selected-country="{{ $profileCountryValue }}">
                                <option value="{{ $profileCountryValue }}">{{ $profileCountryValue }}</option>
                            </select>
                            @error('country') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 customer-field">
                            <label>State / Region</label>
                            <select name="state" class="form-select @error('state') is-invalid @enderror {{ $profileUsesNigeriaGeo ? '' : 'd-none' }}" data-geo-state data-geo-scope="profile" data-selected="{{ old('state', $user->state) }}">
                                <option value="">{{ old('state', $user->state) ?: 'Select state' }}</option>
                            </select>
                            <input type="text" name="state" value="{{ old('state', $user->state) }}" class="form-control @error('state') is-invalid @enderror {{ $profileUsesNigeriaGeo ? 'd-none' : '' }}" data-geo-state-text data-geo-scope="profile" placeholder="Enter state or region">
                            @error('state') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 customer-field">
                            <label>Local Government Area</label>
                            <select name="local_government_area" class="form-select @error('local_government_area') is-invalid @enderror {{ $profileUsesNigeriaGeo ? '' : 'd-none' }}" data-geo-lga data-geo-scope="profile" data-selected="{{ old('local_government_area', $user->local_government_area) }}">
                                <option value="">{{ old('local_government_area', $user->local_government_area) ?: 'Select local government' }}</option>
                            </select>
                            <input type="text" name="local_government_area" value="{{ old('local_government_area', $user->local_government_area) }}" class="form-control @error('local_government_area') is-invalid @enderror {{ $profileUsesNigeriaGeo ? 'd-none' : '' }}" data-geo-lga-text data-geo-scope="profile" placeholder="Enter local government or area">
                            @error('local_government_area') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 customer-field">
                            <label>City</label>
                            <input type="text" name="city" value="{{ old('city', $user->city) }}" class="form-control @error('city') is-invalid @enderror">
                            @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 customer-field">
                            <label>ZIP / Postal Code</label>
                            <input type="text" name="postal_code" value="{{ old('postal_code', $user->postal_code) }}" class="form-control @error('postal_code') is-invalid @enderror">
                            @error('postal_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 customer-field">
                            <label>General Address</label>
                            <textarea name="address" rows="3" class="form-control @error('address') is-invalid @enderror">{{ old('address', $user->address) }}</textarea>
                            @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="customer-card customer-page-block" id="delivery-profile">
                    <div class="customer-panel-head">
                        <div>
                            <span class="customer-eyebrow">Fulfillment</span>
                            <h3 class="customer-section-title">Delivery Profile</h3>
                            <p class="customer-section-copy">These details become the default shipment and handoff contact for checkout.</p>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6 customer-field"><label>Delivery Contact Name</label><input type="text" name="delivery_contact_name" value="{{ old('delivery_contact_name', $user->delivery_contact_name) }}" class="form-control @error('delivery_contact_name') is-invalid @enderror">@error('delivery_contact_name') <div class="invalid-feedback">{{ $message }}</div> @enderror</div>
                        <div class="col-md-6 customer-field"><label>Delivery Phone</label><input type="text" name="delivery_phone" value="{{ old('delivery_phone', $user->delivery_phone) }}" class="form-control @error('delivery_phone') is-invalid @enderror">@error('delivery_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror</div>
                        <div class="col-md-6 customer-field"><label>Address Line 1</label><input type="text" name="delivery_address_line_1" value="{{ old('delivery_address_line_1', $user->delivery_address_line_1) }}" class="form-control @error('delivery_address_line_1') is-invalid @enderror">@error('delivery_address_line_1') <div class="invalid-feedback">{{ $message }}</div> @enderror</div>
                        <div class="col-md-6 customer-field"><label>Address Line 2</label><input type="text" name="delivery_address_line_2" value="{{ old('delivery_address_line_2', $user->delivery_address_line_2) }}" class="form-control @error('delivery_address_line_2') is-invalid @enderror">@error('delivery_address_line_2') <div class="invalid-feedback">{{ $message }}</div> @enderror</div>
                        <div class="col-md-6 customer-field"><label>Delivery Country</label><select name="delivery_country" class="form-select @error('delivery_country') is-invalid @enderror" data-geo-country data-geo-scope="delivery" data-selected-country="{{ $deliveryCountryValue }}"><option value="{{ $deliveryCountryValue }}">{{ $deliveryCountryValue }}</option></select>@error('delivery_country') <div class="invalid-feedback">{{ $message }}</div> @enderror</div>
                        <div class="col-md-6 customer-field"><label>Delivery State</label><select name="delivery_state" class="form-select @error('delivery_state') is-invalid @enderror {{ $deliveryUsesNigeriaGeo ? '' : 'd-none' }}" data-geo-state data-geo-scope="delivery" data-selected="{{ old('delivery_state', $user->delivery_state) }}"><option value="">{{ old('delivery_state', $user->delivery_state) ?: 'Select state' }}</option></select><input type="text" name="delivery_state" value="{{ old('delivery_state', $user->delivery_state) }}" class="form-control @error('delivery_state') is-invalid @enderror {{ $deliveryUsesNigeriaGeo ? 'd-none' : '' }}" data-geo-state-text data-geo-scope="delivery" placeholder="Enter delivery state or region">@error('delivery_state') <div class="invalid-feedback">{{ $message }}</div> @enderror</div>
                        <div class="col-md-6 customer-field"><label>Delivery Local Government</label><select name="delivery_local_government_area" class="form-select @error('delivery_local_government_area') is-invalid @enderror {{ $deliveryUsesNigeriaGeo ? '' : 'd-none' }}" data-geo-lga data-geo-scope="delivery" data-selected="{{ old('delivery_local_government_area', $user->delivery_local_government_area) }}"><option value="">{{ old('delivery_local_government_area', $user->delivery_local_government_area) ?: 'Select local government' }}</option></select><input type="text" name="delivery_local_government_area" value="{{ old('delivery_local_government_area', $user->delivery_local_government_area) }}" class="form-control @error('delivery_local_government_area') is-invalid @enderror {{ $deliveryUsesNigeriaGeo ? 'd-none' : '' }}" data-geo-lga-text data-geo-scope="delivery" placeholder="Enter delivery local government or area">@error('delivery_local_government_area') <div class="invalid-feedback">{{ $message }}</div> @enderror</div>
                        <div class="col-md-6 customer-field"><label>Delivery City</label><input type="text" name="delivery_city" value="{{ old('delivery_city', $user->delivery_city) }}" class="form-control @error('delivery_city') is-invalid @enderror">@error('delivery_city') <div class="invalid-feedback">{{ $message }}</div> @enderror</div>
                        <div class="col-md-6 customer-field"><label>Delivery ZIP / Postal Code</label><input type="text" name="delivery_postal_code" value="{{ old('delivery_postal_code', $user->delivery_postal_code) }}" class="form-control @error('delivery_postal_code') is-invalid @enderror">@error('delivery_postal_code') <div class="invalid-feedback">{{ $message }}</div> @enderror</div>
                        <div class="col-md-6 customer-field"><label>Landmark / Notes</label><input type="text" name="delivery_landmark" value="{{ old('delivery_landmark', $user->delivery_landmark) }}" class="form-control @error('delivery_landmark') is-invalid @enderror">@error('delivery_landmark') <div class="invalid-feedback">{{ $message }}</div> @enderror</div>
                    </div>
                </div>

                <div class="customer-card customer-page-block" id="billing-profile">
                    <div class="customer-panel-head">
                        <div>
                            <span class="customer-eyebrow">Finance Desk</span>
                            <h3 class="customer-section-title">Billing and Payment Profile</h3>
                            <p class="customer-section-copy">These details help with payments and account verification.</p>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6 customer-field">
                            <label>Preferred Payment Method</label>
                            <select name="preferred_payment_method" class="form-select @error('preferred_payment_method') is-invalid @enderror">
                                <option value="">Select method</option>
                                @foreach(['paystack' => 'Paystack', 'bank_transfer' => 'Bank Transfer', 'cash_deposit' => 'Cash Deposit', 'wallet' => 'Wallet', 'card' => 'Card', 'transfer' => 'Transfer'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('preferred_payment_method', $user->preferred_payment_method) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('preferred_payment_method') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 customer-field"><label>Billing Name</label><input type="text" name="billing_name" value="{{ old('billing_name', $user->billing_name) }}" class="form-control @error('billing_name') is-invalid @enderror">@error('billing_name') <div class="invalid-feedback">{{ $message }}</div> @enderror</div>
                        <div class="col-md-6 customer-field"><label>Billing Email</label><input type="email" name="billing_email" value="{{ old('billing_email', $user->billing_email) }}" class="form-control @error('billing_email') is-invalid @enderror">@error('billing_email') <div class="invalid-feedback">{{ $message }}</div> @enderror</div>
                        <div class="col-md-6 customer-field"><label>Billing Phone</label><input type="text" name="billing_phone" value="{{ old('billing_phone', $user->billing_phone) }}" class="form-control @error('billing_phone') is-invalid @enderror">@error('billing_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror</div>
                        <div class="col-12 customer-field"><label>Billing Address</label><textarea name="billing_address" rows="3" class="form-control @error('billing_address') is-invalid @enderror">{{ old('billing_address', $user->billing_address) }}</textarea>@error('billing_address') <div class="invalid-feedback">{{ $message }}</div> @enderror</div>
                    </div>
                </div>

                <div class="customer-card customer-page-block" id="kyc-profile">
                    <div class="customer-panel-head">
                        <div>
                            <span class="customer-eyebrow">Verification</span>
                            <h3 class="customer-section-title">KYC Information</h3>
                            <p class="customer-section-copy">Record the identity details your operations team may need for approvals and regulated requests.</p>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4 customer-field">
                            <label>KYC Status</label>
                            <select name="kyc_status" class="form-select @error('kyc_status') is-invalid @enderror">
                                <option value="">Not submitted</option>
                                @foreach(['pending' => 'Pending Review', 'approved' => 'Approved', 'rejected' => 'Rejected', 'requires_review' => 'Requires Review'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('kyc_status', $user->kyc_status) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('kyc_status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 customer-field">
                            <label>Identity Type</label>
                            <select name="identity_type" class="form-select @error('identity_type') is-invalid @enderror">
                                <option value="">Select identity type</option>
                                @foreach(['nin' => 'NIN', 'national_id' => 'National ID', 'drivers_license' => 'Driver\'s License', 'international_passport' => 'International Passport', 'voters_card' => 'Voter\'s Card', 'residence_permit' => 'Residence Permit', 'other' => 'Other'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('identity_type', $user->identity_type) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('identity_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 customer-field"><label>Identity Country</label><select name="identity_country" class="form-select @error('identity_country') is-invalid @enderror" data-country-only data-country-scope="identity" data-selected-country="{{ $identityCountryValue }}"><option value="{{ $identityCountryValue }}">{{ $identityCountryValue }}</option></select>@error('identity_country') <div class="invalid-feedback">{{ $message }}</div> @enderror</div>
                        <div class="col-12 customer-field"><label>Identity Number</label><input type="text" name="identity_number" value="{{ old('identity_number', $user->identity_number) }}" class="form-control @error('identity_number') is-invalid @enderror">@error('identity_number') <div class="invalid-feedback">{{ $message }}</div> @enderror</div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button class="btn customer-btn-primary">Save Profile Updates</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var geoOptionsUrl = @json(route('profile.geo-options'));
    var detectCountryUrl = @json(route('profile.detect-country'));
    var csrfToken = @json(csrf_token());
    var detectedCountryPromise = null;

    function fetchJson(url, options) {
        return fetch(url, options || {}).then(function (response) {
            if (!response.ok) {
                throw new Error('Request failed.');
            }

            return response.json();
        });
    }

    function escapeQuery(value) {
        return encodeURIComponent(value || '');
    }

    function countryMatches(country, value) {
        if (!country || !value) {
            return false;
        }

        return String(country.name || '').toLowerCase() === String(value).toLowerCase()
            || String(country.code || '').toLowerCase() === String(value).toLowerCase();
    }

    function normalizeCountryValue(countries, value) {
        if (!value) {
            return '';
        }

        var match = (countries || []).find(function (item) {
            return countryMatches(item, value);
        });

        return match ? match.name : value;
    }

    function detectCountryFromLocale(countries) {
        var localeCandidates = [];

        if (Array.isArray(navigator.languages)) {
            localeCandidates = localeCandidates.concat(navigator.languages);
        }

        if (navigator.language) {
            localeCandidates.push(navigator.language);
        }

        localeCandidates = localeCandidates.filter(Boolean);

        for (var index = 0; index < localeCandidates.length; index += 1) {
            var locale = localeCandidates[index];
            var region = '';

            if (typeof Intl !== 'undefined' && typeof Intl.Locale === 'function') {
                try {
                    region = new Intl.Locale(locale).region || '';
                } catch (error) {
                    region = '';
                }
            }

            if (!region && locale.indexOf('-') !== -1) {
                region = locale.split('-').pop() || '';
            }

            if (!region && locale.indexOf('_') !== -1) {
                region = locale.split('_').pop() || '';
            }

            var match = (countries || []).find(function (item) {
                return String(item.code || '').toLowerCase() === String(region || '').toLowerCase();
            });

            if (match) {
                return match.name;
            }
        }

        return '';
    }

    function detectCountryByLocation(countries) {
        if (detectedCountryPromise) {
            return detectedCountryPromise;
        }

        detectedCountryPromise = new Promise(function (resolve) {
            var resolveFallback = function () {
                resolve(detectCountryFromLocale(countries));
            };

            if (!navigator.geolocation) {
                resolveFallback();
                return;
            }

            navigator.geolocation.getCurrentPosition(function (position) {
                fetchJson(detectCountryUrl, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude,
                    }),
                })
                .then(function (payload) {
                    var normalized = normalizeCountryValue(countries, payload.country || payload.code || '');
                    resolve(normalized || detectCountryFromLocale(countries));
                })
                .catch(function () {
                    resolveFallback();
                });
            }, function () {
                resolveFallback();
            }, {
                enableHighAccuracy: false,
                timeout: 8000,
                maximumAge: 300000,
            });
        });

        return detectedCountryPromise;
    }

    function setLoading(select, label) {
        select.innerHTML = '';

        var option = document.createElement('option');
        option.value = '';
        option.textContent = label;
        option.selected = true;
        select.appendChild(option);
        select.disabled = true;
    }

    function populateCountrySelect(select, countries, selectedCountry) {
        select.innerHTML = '';

        var placeholder = document.createElement('option');
        placeholder.value = '';
        placeholder.textContent = 'Select country';
        placeholder.selected = !selectedCountry;
        select.appendChild(placeholder);

        countries.forEach(function (item) {
            var option = document.createElement('option');
            option.value = item.name;
            option.textContent = item.name;
            option.selected = selectedCountry === item.name;
            select.appendChild(option);
        });

        select.disabled = false;
        select.value = selectedCountry || '';
    }

    function fetchGeoOptions(countryName, stateName) {
        var url = geoOptionsUrl + '?country=' + escapeQuery(countryName || '');

        if (stateName) {
            url += '&state=' + escapeQuery(stateName);
        }

        return fetchJson(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
        });
    }

    function setupGeoScope(scope) {
        var country = document.querySelector('[data-geo-country][data-geo-scope="' + scope + '"]');
        var state = document.querySelector('[data-geo-state][data-geo-scope="' + scope + '"]');
        var lga = document.querySelector('[data-geo-lga][data-geo-scope="' + scope + '"]');
        var stateText = document.querySelector('[data-geo-state-text][data-geo-scope="' + scope + '"]');
        var lgaText = document.querySelector('[data-geo-lga-text][data-geo-scope="' + scope + '"]');

        if (!country || !state || !lga || !stateText || !lgaText) {
            return;
        }

        var selectedState = state.dataset.selected || '';
        var selectedLga = lga.dataset.selected || '';
        var selectedCountry = country.dataset.selectedCountry || country.value || '';

        function setOptions(select, options, placeholder, selectedValue) {
            select.innerHTML = '';

            var defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.textContent = placeholder;
            select.appendChild(defaultOption);

            options.forEach(function (option) {
                var next = document.createElement('option');
                next.value = option.value;
                next.textContent = option.label;
                next.selected = selectedValue === option.value;
                select.appendChild(next);
            });
        }

        function disableSelect(select, placeholder) {
            setOptions(select, [], placeholder, '');
            select.disabled = true;
        }

        function useNigeriaGeoMode(isNigeria) {
            state.classList.toggle('d-none', !isNigeria);
            lga.classList.toggle('d-none', !isNigeria);
            stateText.classList.toggle('d-none', isNigeria);
            lgaText.classList.toggle('d-none', isNigeria);

            state.disabled = !isNigeria;
            lga.disabled = !isNigeria;
            stateText.disabled = isNigeria;
            lgaText.disabled = isNigeria;
        }

        function applyGeoPayload(payload) {
            var isNigeria = (country.value || '').toLowerCase() === 'nigeria';

            useNigeriaGeoMode(isNigeria);

            if (!country.value) {
                disableSelect(state, 'Select country first');
                disableSelect(lga, 'Select state first');
                return;
            }

            if (!isNigeria) {
                disableSelect(state, 'Enter state or region manually');
                disableSelect(lga, 'Enter local area manually');
                return;
            }

            if (String(payload.country || '').toLowerCase() !== 'nigeria') {
                loadStates(true);
                return;
            }

            setLoading(state, 'Loading states...');

            var stateOptions = (payload.states || []).map(function (item) {
                return { value: item.name, label: item.name };
            });

            state.disabled = false;
            setOptions(state, stateOptions, stateOptions.length ? 'Select state' : 'No state list for selected country', selectedState);

            if (!stateOptions.length) {
                disableSelect(lga, 'No local government list for selected country');
                return;
            }

            loadLgas();
        }

        function loadStates(skipDetection) {
            setLoading(country, 'Loading countries...');

            fetchGeoOptions(selectedCountry, '')
            .then(function (payload) {
                if (Array.isArray(payload.countries) && payload.countries.length) {
                    selectedCountry = normalizeCountryValue(payload.countries, selectedCountry);
                    populateCountrySelect(country, payload.countries, selectedCountry);
                }

                if (!skipDetection && !selectedCountry) {
                    return detectCountryByLocation(payload.countries || []).then(function (detectedCountry) {
                        if (detectedCountry) {
                            selectedCountry = detectedCountry;
                            country.value = detectedCountry;
                        }

                        applyGeoPayload(payload);
                    });
                }

                applyGeoPayload(payload);
            })
            .catch(function () {
                country.innerHTML = '<option value="' + (selectedCountry || '') + '">' + (selectedCountry || 'Select country') + '</option>';
                country.value = selectedCountry || '';
                useNigeriaGeoMode(false);
                disableSelect(state, 'Unable to load states');
                disableSelect(lga, 'Unable to load local governments');
            });
        }

        function loadLgas() {
            if (country.value.toLowerCase() !== 'nigeria') {
                return;
            }

            if (!state.value) {
                disableSelect(lga, 'Select state first');
                return;
            }

            setLoading(lga, 'Loading local governments...');

            fetchGeoOptions(country.value, state.value)
            .then(function (payload) {
                var lgaOptions = (payload.lgas || []).map(function (item) {
                    return { value: item, label: item };
                });

                lga.disabled = false;
                setOptions(lga, lgaOptions, lgaOptions.length ? 'Select local government' : 'No local government list for selected state', selectedLga);
            })
            .catch(function () {
                disableSelect(lga, 'Unable to load local government list');
            });
        }

        country.addEventListener('change', function () {
            selectedCountry = country.value;
            selectedState = '';
            selectedLga = '';
            loadStates(true);
        });

        state.addEventListener('change', function () {
            selectedState = state.value;
            selectedLga = '';
            loadLgas();
        });

        loadStates();
    }

    setupGeoScope('profile');
    setupGeoScope('delivery');

    document.querySelectorAll('[data-country-only]').forEach(function (select) {
        var selectedCountry = select.dataset.selectedCountry || select.value || '';

        setLoading(select, 'Loading countries...');

        fetchGeoOptions(selectedCountry, '')
        .then(function (payload) {
            if (!Array.isArray(payload.countries) || !payload.countries.length) {
                return;
            }

            selectedCountry = normalizeCountryValue(payload.countries, selectedCountry);
            populateCountrySelect(select, payload.countries, selectedCountry);

            if (selectedCountry) {
                return;
            }

            return detectCountryByLocation(payload.countries).then(function (detectedCountry) {
                if (!detectedCountry) {
                    return;
                }

                select.value = detectedCountry;
            });
        })
        .catch(function () {
            select.innerHTML = '<option value="' + (selectedCountry || '') + '">' + (selectedCountry || 'Select country') + '</option>';
            select.value = selectedCountry || '';
        });
    });
});
</script>
@endpush
